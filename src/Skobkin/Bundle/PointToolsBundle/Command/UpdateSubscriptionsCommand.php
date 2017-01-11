<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Subscription;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
use Skobkin\Bundle\PointToolsBundle\Service\SubscriptionsManager;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @todo https://symfony.com/doc/current/console/lockable_trait.html
 */
class UpdateSubscriptionsCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var UserApi
     */
    private $api;

    /**
     * @var int
     */
    private $apiDelay = 500000;

    /**
     * @var SubscriptionsManager
     */
    private $subscriptionManager;

    /**
     * @var ProgressBar
     */
    private $progress;


    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setUserRepository(UserRepository $repository)
    {
        $this->userRepo = $repository;
    }

    public function setApiClient(UserApi $userApi)
    {
        $this->api = $userApi;
    }

    public function setApiDelay(int $microSecs)
    {
        $this->apiDelay = $microSecs;
    }

    public function setSubscriptionManager(SubscriptionsManager $subscriptionsManager)
    {
        $this->subscriptionManager = $subscriptionsManager;
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
            // @todo add option for checking only selected user
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;

        $this->logger->debug('UpdateSubscriptionsCommand started.');

        try {
            $appUserId = $this->getContainer()->getParameter('point_id');
        } catch (\InvalidArgumentException $e) {
            $this->logger->alert('Could not get point_id parameter from config file', ['exception_message' => $e->getMessage()]);
            return 1;
        }

        $this->progress = new ProgressBar($output);
        $this->progress->setFormat('debug');

        // Beginning transaction for all changes
        $this->em->beginTransaction();

        $this->progress->setMessage('Getting service subscribers');

        try {
            $usersForUpdate = $this->getUsersForUpdate($appUserId);
        } catch (\Exception $e) {
            $this->logger->error('Error while getting service subscribers', ['exception' => get_class($e), 'message' => $e->getMessage()]);

            return 1;
        }

        if (0 === count($usersForUpdate)) {
            $this->logger->info('No local subscribers. Finishing.');

            return 0;
        }

        $this->logger->info('Processing users subscribers');
        $this->progress->setMessage('Processing users subscribers');
        $this->progress->start(count($usersForUpdate));

        $this->updateUsersSubscribers($usersForUpdate);

        $this->progress->finish();

        // Flushing all changes at once to database
        $this->em->flush();
        $this->em->commit();

        $this->logger->debug('Finished');

        return 0;
    }

    /**
     * @param User[] $users
     */
    private function updateUsersSubscribers(array $users)
    {
        // Updating users subscribers
        foreach ($users as $user) {
            $this->logger->info('Processing @'.$user->getLogin());

            try {
                $userCurrentSubscribers = $this->api->getUserSubscribersById($user->getId());
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

                continue;
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

            $this->progress->advance();

            usleep($this->apiDelay);
        }
    }

    private function getUsersForUpdate(int $appUserId): array
    {
        if ($this->input->getOption('all-users')) {
            $usersForUpdate = $this->userRepo->findAll();
        } else {
            /** @var User $serviceUser */
            $serviceUser = $this->userRepo->find($appUserId);

            if (!$serviceUser) {
                $this->logger->info('Service user not found');
                // @todo Retrieving user

                throw new \RuntimeException('Service user not found in the database');
            }

            $this->logger->info('Getting service subscribers');

            try {
                $usersForUpdate = $this->api->getUserSubscribersById($appUserId);
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

                $usersForUpdate = [];

                /** @var Subscription $subscription */
                foreach ((array) $serviceUser->getSubscribers() as $subscription) {
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