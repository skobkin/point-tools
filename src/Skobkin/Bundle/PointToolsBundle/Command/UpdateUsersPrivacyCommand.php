<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\{Subscription, User};
use Skobkin\Bundle\PointToolsBundle\Exception\Api\{ForbiddenException, UserNotFoundException};
use Skobkin\Bundle\PointToolsBundle\Repository\UserRepository;
use Skobkin\Bundle\PointToolsBundle\Service\Api\UserApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUsersPrivacyCommand extends ContainerAwareCommand
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

    /** @var ProgressBar */
    private $progress;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, UserRepository $userRepo, UserApi $api, int $apiDelay, int $appUserId)
    {
        parent::__construct();

        $this->em = $em;
        $this->logger = $logger;
        $this->userRepo = $userRepo;
        $this->api = $api;
        $this->apiDelay = $apiDelay;
        $this->appUserId = $appUserId;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('point:update:privacy')
            ->setDescription('Update users privacy')
            ->addOption(
                'all-users',
                null,
                InputOption::VALUE_NONE,
                'If set, command will check all users instead of service subscribers only'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;

        $this->logger->debug(static::class.' started.');

        $this->progress = new ProgressBar($output);
        $this->progress->setFormat('debug');

        try {
            /** @var User[] $usersForUpdate */
            $usersForUpdate = $this->getUsersForUpdate();
        } catch (\Exception $e) {
            $this->logger->error('Error while getting service subscribers', ['exception' => get_class($e), 'message' => $e->getMessage()]);

            return 1;
        }

        $this->logger->info('Processing users privacy.');

        $this->progress->start(count($usersForUpdate));

        foreach ($usersForUpdate as $idx => $user) {
            usleep($this->apiDelay);

            $this->progress->advance();
            $this->logger->info('Processing @'.$user->getLogin());

            $this->updateUser($user);

            // Flushing each 10 users
            if (0 === $idx % 10) {
                $this->em->flush();
            }
        }

        $this->progress->finish();

        $this->em->flush();

        $this->logger->debug('Finished');

        return 0;
    }

    private function updateUser(User $user): void
    {
        try {
            $remoteUser = $this->api->getUserById($user->getId());

            if ($remoteUser !== $user) {
                $this->logger->error('Remote user is not equal with local.', ['user_id' => $user->getId(), 'user_login' => $user->getLogin()]);
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

    private function getUsersForUpdate(): array
    {
        if ($this->input->getOption('all-users')) {
            return $this->userRepo->findBy(['removed' => false]);
        }

        /** @var User $serviceUser */
        try {
            $serviceUser = $this->userRepo->findActiveUserWithSubscribers($this->appUserId);
        } catch (\Exception $e) {
            $this->logger->error('Error while getting active user with subscribers', ['app_user_id' => $this->appUserId]);

            throw $e;
        }

        if (!$serviceUser) {
            $this->logger->critical('Service user not found or marked as removed');

            throw new \RuntimeException('Service user not found in the database');
        }

        $this->logger->info('Getting service subscribers');

        try {
            return $this->api->getUserSubscribersById($this->appUserId);
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

            /** @var Subscription $subscription */
            foreach ($serviceUser->getSubscribers() as $subscription) {
                $localSubscribers[] = $subscription->getSubscriber();
            }

            return $localSubscribers;
        }
    }
}
