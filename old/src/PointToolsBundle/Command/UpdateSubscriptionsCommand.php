<?php

namespace src\PointToolsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use src\PointToolsBundle\Entity\User;
use src\PointToolsBundle\Service\SubscriptionsManager;
use src\PointToolsBundle\Entity\{Subscription};
use src\PointToolsBundle\Exception\Api\UserNotFoundException;
use src\PointToolsBundle\Repository\UserRepository;
use src\PointToolsBundle\Service\{Api\UserApi};
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @todo https://symfony.com/doc/current/console/lockable_trait.html
 */
class UpdateSubscriptionsCommand extends Command
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var LoggerInterface */
    private $logger;

    /** @var UserRepository */
    private $userRepo;

    /** @var InputInterface */
    private $input;

    /** @var UserApi */
    private $api;

    /** @var int */
    private $apiDelay = 500000;

    /** @var int */
    private $appUserId;

    /** @var SubscriptionsManager */
    private $subscriptionManager;

    /** @var ProgressBar */
    private $progress;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        UserRepository $userRepo,
        UserApi $api,
        SubscriptionsManager $subscriptionManager,
        int $apiDelay,
        int $appUserId
    ) {
        parent::__construct();

        $this->em = $em;
        $this->logger = $logger;
        $this->userRepo = $userRepo;
        $this->api = $api;
        $this->subscriptionManager = $subscriptionManager;
        $this->apiDelay = $apiDelay;
        $this->appUserId = $appUserId;
    }

    protected function configure()
    {
        $this
            ->setName('point:update:subscriptions')
            ->setDescription('Update subscriptions of users subscribed to service')
            ->addOption(
                'all-users',
                null,
                InputOption::VALUE_NONE,
                'If set, command will check subscribers of all service users instead of service subscribers only'
            )
            ->addOption(
                'check-only',
                null,
                InputOption::VALUE_NONE,
                'If set, command will not perform write operations in the database'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;

        $this->logger->debug('UpdateSubscriptionsCommand started.');

        $this->progress = new ProgressBar($output);
        $this->progress->setFormat('debug');

        if (!$input->getOption('check-only')) { // Beginning transaction for all changes
            $this->em->beginTransaction();
        }

        try {
            $usersForUpdate = $this->getUsersForUpdate();
        } catch (\Exception $e) {
            $this->logger->error('Error while getting service subscribers', ['exception' => get_class($e), 'message' => $e->getMessage()]);

            return 1;
        }

        if (0 === count($usersForUpdate)) {
            $this->logger->info('No local subscribers. Finishing.');

            return 0;
        }

        $this->logger->info('Processing users subscribers');
        $this->progress->start(count($usersForUpdate));

        foreach ($usersForUpdate as $user) {
            usleep($this->apiDelay);

            $this->progress->advance();
            $this->logger->info('Processing @'.$user->getLogin());

            $this->updateUser($user);
        }

        $this->progress->finish();

        // Flushing all changes at once to the database
        if (!$input->getOption('check-only')) {
            $this->em->flush();
            $this->em->commit();
        }

        $this->logger->debug('Finished');

        return 0;
    }

    private function updateUser(User $user): void
    {
        try {
            $userCurrentSubscribers = $this->api->getUserSubscribersById($user->getId());
        } catch (UserNotFoundException $e) {
            $this->logger->warning('User not found. Marking as removed.', ['login' => $user->getLogin(), 'user_id' => $user->getId()]);

            $user->markAsRemoved();

            return;
        } catch (\Exception $e) {
            $this->logger->error(
                'Error while getting subscribers. Skipping.',
                [
                    'user_login' => $user->getLogin(),
                    'user_id' => $user->getId(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return;
        }

        $this->logger->debug('Updating user subscribers');

        try {
            // Updating user subscribers
            $this->subscriptionManager->updateUserSubscribers($user, $userCurrentSubscribers);
        } catch (\Exception $e) {
            $this->logger->error(
                'Error while updating user subscribers',
                [
                    'user_login' => $user->getLogin(),
                    'user_id' => $user->getId(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );
        }
    }

    private function getUsersForUpdate(): array
    {
        $usersForUpdate = [];

        if ($this->input->getOption('all-users')) {
            $usersForUpdate = $this->userRepo->findBy(['removed' => false]);
        } else {
            /** @var User $serviceUser */
            try {
                $serviceUser = $this->userRepo->findActiveUserWithSubscribers($this->appUserId);
            } catch (\Exception $e) {
                $this->logger->error('Error while getting active user with subscribers', ['app_user_id' => $this->appUserId]);

                throw $e;
            }

            if (!$serviceUser) {
                $this->logger->warning('Service user not found or marked as removed. Falling back to API.');

                try {
                    $serviceUser = $this->api->getUserById($this->appUserId);
                } catch (UserNotFoundException $e) {
                    throw new \RuntimeException('Service user not found in the database and could not be retrieved from API.');
                }
            }

            $this->logger->info('Getting service subscribers');

            try {
                $usersForUpdate = $this->api->getUserSubscribersById($this->appUserId);
            } catch (UserNotFoundException $e) {
                $this->logger->critical('Service user deleted or API response is invalid');

                throw $e;
            } catch (\Exception $e) {
                $this->logger->warning(
                    'Error while getting service subscribers. Fallback to local list.',
                    [
                        'user_login' => $serviceUser->getLogin(),
                        'user_id' => $serviceUser->getId(),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                );

                /** @var Subscription $subscription */
                foreach ($serviceUser->getSubscribers() as $subscription) {
                    $usersForUpdate[] = $subscription->getSubscriber();
                }
            }

            $this->logger->debug('Updating service subscribers');

            // Updating service subscribers
            try {
                $this->subscriptionManager->updateUserSubscribers($serviceUser, $usersForUpdate);
            } catch (\Exception $e) {
                $this->logger->error(
                    'Error while updating service subscribers',
                    [
                        'user_login' => $serviceUser->getLogin(),
                        'user_id' => $serviceUser->getId(),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                );

                throw $e;
            }
        }

        return $usersForUpdate;
    }
}