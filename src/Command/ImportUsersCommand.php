<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Import users from CSV file exported from database by query:
 * COPY (
 *      SELECT u.id, u.login, ui.name, to_char(ui.created, 'YYYY-MM-DD_HH24:MI:SS') AS created_at
 *      FROM users.logins u
 *      LEFT JOIN users.info ui ON (ui.id = u.id)
 *      WHERE u.id <> (-1)
 * ) TO '/tmp/point_users.csv' WITH HEADER DELIMITER '|' CSV;
 */
#[AsCommand(name: 'app:import:users', description: 'Import users from CSV file')]
class ImportUsersCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'CSV file path'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'If set, command will not perform write operations in the database'
            )
            ->addOption(
                'no-skip-first',
                null,
                InputOption::VALUE_NONE,
                'Do not skip first line (if no headers in CSV file)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();

        $fileName = $input->getArgument('file');

        if (!($fs->exists($fileName) && \is_readable($fileName))) {
            $io->error('File does not exists or not readable.');

            return Command::FAILURE;
        }

        if (false === ($file = fopen($fileName, 'rb'))) {
            $io->error('fopen() error');

            return Command::FAILURE;
        }

        if (!$input->getOption('no-skip-first')) {
            // Reading headers line
            fgets($file);
        }

        $count = 0;

        while (false !== ($row = fgetcsv($file, 1000, '|'))) {
            if (\count($row) !== 4) {
                continue;
            }

            $createdAt = \DateTime::createFromFormat('Y-m-d_H:i:s', $row[3]) ?: new \DateTime();

            $user = new User($row[0], $row[1], $createdAt, $row[2]);

            if (!$input->getOption('dry-run')) {
                $this->em->persist($user);
                $this->em->flush($user);
                $this->em->detach($user);
            }

            if (OutputInterface::VERBOSITY_VERBOSE === $io->getVerbosity()) {
                $io->info('@' . $row[1] . ' added');
            }

            $count++;
        }

        $io->success($count . ' users imported.');

        return Command::SUCCESS;
    }
}
