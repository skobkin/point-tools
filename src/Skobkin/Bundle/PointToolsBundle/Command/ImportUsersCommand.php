<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Import users from CSV file exported from database by query:
 * COPY (SELECT u.id, u.login, ui.name, to_char(ui.created, 'YYYY-MM-DD_HH24:MI:SS') AS created_at FROM users.logins u LEFT JOIN users.info ui ON (ui.id = u.id) WHERE u.id <> (-1)) TO '/tmp/point_users.csv' WITH HEADER DELIMITER '|' CSV;
 */
class ImportUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('point:import:users')
            ->setDescription('Import users from CSV file')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'CSV file path'
            )
            ->addOption(
                'check-only',
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $fs = new Filesystem();

        $fileName = $input->getArgument('file');

        if (!($fs->exists($fileName) && is_readable($fileName))) {
            $output->writeln('File does not exists or not readable.');
            return false;
        }

        if (false === ($file = fopen($fileName, 'r'))) {
            $output->writeln('fopen() error');
            return false;
        }

        if (!$input->getOption('no-skip-first')) {
            // Reading headers line
            $test = fgets($file);
        }

        $count = 0;

        while (false !== ($row = fgetcsv($file, 1000, '|'))) {
            if (count($row) !== 4) {
                continue;
            }

            $createdAt = \DateTime::createFromFormat('Y-m-d_H:i:s', $row[3]);

            if (!$createdAt) {
                $createdAt = new \DateTime();
            }

            $user = new User();
            $user
                ->setId($row[0])
                ->setLogin($row[1])
                ->setName($row[2])
                ->setCreatedAt($createdAt)
            ;

            if (!$input->getOption('check-only')) {
                $em->persist($user);
                $em->flush($user);
                $em->detach($user);
            }

            if ($output->isVerbose()) {
                $output->writeln('@' . $row[1] . ' added');
            }

            $count++;
        }

        $output->writeln($count . ' users imported.');
    }
}