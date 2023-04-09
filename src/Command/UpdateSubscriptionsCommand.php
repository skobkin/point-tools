<?php
declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use App\Entity\{Subscription, User};
use App\Exception\Api\UserNotFoundException;
use App\Service\Api\UserApi;
use App\Service\SubscriptionsManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:subscriptions:update', description: 'Update subscriptions of users subscribed to service')]
class UpdateSubscriptionsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface        $logger,
        private readonly UserRepository         $userRepo,
        private readonly UserApi                $api,
        private readonly SubscriptionsManager   $subscriptionManager,
        private readonly int                    $pointApiDelay,
        private readonly int                    $pointAppUserId,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->logger->debug('UpdateSubscriptionsCommand started.');

        $progress = $io->createProgressBar();
        $progress->setFormat(ProgressBar::FORMAT_DEBUG);

        if (!$input->getOption('check-only')) { // Beginning transaction for all changes
            $this->em->beginTransaction();
        }

        try {
            $usersForUpdate = $this->getUsersForUpdate($input);
        } catch (\Exception $e) {
            $this->logger->error('Error while getting service subscribers', ['exception' => get_class($e), 'message' => $e->getMessage()]);

            return Command::FAILURE;
        }

        if (0 === count($usersForUpdate)) {
            $this->logger->info('No local subscribers. Finishing.');

            return Command::SUCCESS;
        }

        $this->logger->info('Processing users subscribers');
        $progress->start(count($usersForUpdate));

        foreach ($usersForUpdate as $user) {
            \usleep($this->pointApiDelay);

            $progress->advance();
            $this->logger->info('Processing @'.$user->getLogin());

            $this->updateUser($user);
        }

        $progress->finish();

        // Flushing all changes at once to the database
        if (!$input->getOption('check-only')) {
            $this->em->flush();
            $this->em->commit();
        }

        $this->logger->debug('Finished');

        return Command::SUCCESS;
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

    private function getUsersForUpdate(InputInterface $input): array
    {
        $usersForUpdate = [];

        if ($input->getOption('all-users')) {
            $usersForUpdate = $this->userRepo->findBy(['removed' => false]);
        } else {
            /** @var User $serviceUser */
            try {
                $serviceUser = $this->userRepo->findActiveUserWithSubscribers($this->pointAppUserId);
            } catch (\Exception $e) {
                $this->logger->error('Error while getting active user with subscribers', ['app_user_id' => $this->pointAppUserId]);

                throw $e;
            }

            if (!$serviceUser) {
                $this->logger->warning('Service user not found or marked as removed. Falling back to API.');

                try {
                    $serviceUser = $this->api->getUserById($this->pointAppUserId);
                } catch (UserNotFoundException $e) {
                    throw new \RuntimeException('Service user not found in the database and could not be retrieved from API.');
                }
            }

            $this->logger->info('Getting service subscribers');

            try {
                $usersForUpdate = $this->api->getUserSubscribersById($this->pointAppUserId);
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
