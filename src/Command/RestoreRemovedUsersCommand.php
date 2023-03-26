<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\Api\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\Api\UserApi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:users:restore', description: 'Check removed users status and restore if user was deleted by error.')]
class RestoreRemovedUsersCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface        $logger,
        private readonly EntityManagerInterface $em,
        private readonly UserRepository         $userRepo,
        private readonly UserApi                $userApi,
        private readonly int                    $pointApiDelay,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->userRepo->findBy(['removed' => true]) as $removedUser) {
            \usleep($this->pointApiDelay);

            try {
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

        return Command::SUCCESS;
    }
}
