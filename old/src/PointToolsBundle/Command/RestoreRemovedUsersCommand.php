<?php

namespace src\PointToolsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use src\PointToolsBundle\Entity\User;
use src\PointToolsBundle\Exception\Api\UserNotFoundException;
use src\PointToolsBundle\Repository\UserRepository;
use src\PointToolsBundle\Service\Api\UserApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RestoreRemovedUsersCommand extends Command
{
    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserRepository */
    private $userRepo;

    /** @var UserApi */
    private $userApi;

    /** @var int */
    private $delay;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, UserRepository $userRepo, UserApi $userApi, int $apiDelay)
    {
        parent::__construct();

        $this->logger = $logger;
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->userApi = $userApi;
        $this->delay = $apiDelay;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('point:users:restore')
            ->setDescription('Check removed users status and restore if user was deleted by error.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var User $removedUser */
        foreach ($this->userRepo->findBy(['removed' => true]) as $removedUser) {
            usleep($this->delay);

            try {
                /** @var User $remoteUser */
                $remoteUser = $this->userApi->getUserById($removedUser->getId());

                if ($remoteUser->getId() === $removedUser->getId()) {
                    $this->logger->info('Restoring user', [
                        'id' => $removedUser->getId(),
                        'login' => $removedUser->getLogin(),
                    ]);
                    $removedUser->restore();

                    $this->em->flush();
                }
            } catch (UserNotFoundException $e) {
                $this->logger->debug('User is really removed. Keep going.', [
                    'id' => $removedUser->getId(),
                    'login' => $removedUser->getLogin(),
                ]);

                continue;
            } catch (\Exception $e) {
                $this->logger->error('Error while trying to restore user', [
                    'user_id' => $removedUser->getId(),
                    'user_login' => $removedUser->getLogin(),
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        }

        $this->em->flush();
    }
}
