<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Exception\Api\ForbiddenException;
use App\Exception\Api\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\Api\UserApi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:privacy:update', description: 'Check removed users status and restore if user was deleted by error.')]
class UpdateUsersPrivacyCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface        $logger,
        private readonly UserRepository         $userRepo,
        private readonly UserApi                $api,
        private readonly int                    $pointApiDelay,
        private readonly int                    $pointAppUserId,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption(
                'all-users',
                null,
                InputOption::VALUE_NONE,
                'If set, command will check all users instead of service subscribers only'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->logger->debug(static::class.' started.');

        $progress = $io->createProgressBar();
        $progress->setFormat(ProgressBar::FORMAT_DEBUG);

        try {
            $usersForUpdate = $this->getUsersForUpdate($input);
        } catch (\Exception $e) {
            $this->logger->error('Error while getting service subscribers', ['exception' => get_class($e), 'message' => $e->getMessage()]);

            return Command::FAILURE;
        }

        $this->logger->info('Processing users privacy.');

        $progress->start(count($usersForUpdate));

        foreach ($usersForUpdate as $user) {
            usleep($this->pointApiDelay);

            $progress->advance();
            $this->logger->info('Processing @'.$user->getLogin());

            $this->updateUser($user);
        }

        $progress->finish();

        $this->em->flush();

        $this->logger->debug('Finished');

        return Command::SUCCESS;
    }

    private function updateUser(User $user): void
    {
        try {
            $remoteUser = $this->api->getUserById($user->getId());

            if ($remoteUser !== $user) {
                $this->logger->error('Remote user is not equal with local.', [
                    'local_user_id' => $user->getId(),
                    'local_user_login' => $user->getLogin(),
                    'local_user_name' => $user->getName(),
                    'remote_user_id' => $remoteUser->getId(),
                    'remote_user_login' => $remoteUser->getLogin(),
                    'remote_user_name' => $remoteUser->getName(),
                ]);
            }
        } catch (UserNotFoundException $e) {
            $this->logger->info('User not found. Marking as removed.', ['user_id' => $user->getId(), 'user_login' => $user->getLogin()]);

            $user->markAsRemoved();
        } catch (ForbiddenException $e) {
            $this->logger->info('User profile access forbidden', ['user_id' => $user->getId(), 'user_login' => $user->getLogin()]);

            $user->updatePrivacy(false, true);
        } catch (\Exception $e) {
            $this->logger->error(
                'Error while updating user privacy',
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

    /** @return User[] */
    private function getUsersForUpdate(InputInterface $input): array
    {
        if ($input->getOption('all-users')) {
            return $this->userRepo->findBy(['removed' => false]);
        }

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
            return $this->api->getUserSubscribersById($this->pointAppUserId);
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

            $localSubscribers = [];

            foreach ($serviceUser->getSubscribers() as $subscription) {
                $localSubscribers[] = $subscription->getSubscriber();
            }

            return $localSubscribers;
        }
    }
}
