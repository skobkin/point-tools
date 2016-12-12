<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Skobkin\Bundle\PointToolsBundle\Service\SubscriptionsManager;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSubscriptionsCommand extends ContainerAwareCommand
{
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
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $log = $this->getContainer()->get('logger');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository('SkobkinPointToolsBundle:User');

        $log->info('UpdateSubscriptionsCommand started.');

        /** @var UserApi $api */
        $api = $this->getContainer()->get('skobkin_point_tools.api_user');
        /** @var SubscriptionsManager $subscriptionsManager */
        $subscriptionsManager = $this->getContainer()->get('skobkin_point_tools.subscriptions_manager');

        try {
            $serviceUserId = $this->getContainer()->getParameter('point_id');
        } catch (\InvalidArgumentException $e) {
            $log->alert('Could not get point_id parameter from config file', ['exception_message' => $e->getMessage()]);
            return 1;
        }

        if ($input->getOption('all-users')) {
            $usersForUpdate = $userRepository->findAll();
        } else {
            $serviceUser = $userRepository->find($serviceUserId);

            if (!$serviceUser) {
                $log->info('Service user not found');
                // @todo Retrieving user

                return 1;
            }

            if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                $output->writeln('Getting service subscribers');
            }

            try {
                $usersForUpdate = $api->getUserSubscribersById($serviceUserId);
            } catch (\Exception $e) {
                $output->writeln('Error while getting service subscribers');
                $log->error('Error while getting service subscribers.', ['user_login' => $serviceUser->getLogin(), 'user_id' => $serviceUser->getId(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

                $usersForUpdate = [];

                foreach ($serviceUser->getSubscribers() as $subscription) {
                    $usersForUpdate[] = $subscription->getSubscriber();
                }

                $output->writeln('Fallback to local list');
                $log->error('Fallback to local list');

                if (!count($usersForUpdate)) {
                    $log->info('No local subscribers. Finishing.');
                    return 0;
                }
            }

            if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                $output->writeln('Updating service subscribers');
            }

            // Updating service subscribers
            try {
                $subscriptionsManager->updateUserSubscribers($serviceUser, $usersForUpdate);
            } catch (\Exception $e) {
                $log->error('Error while updating service subscribers', ['user_login' => $serviceUser->getLogin(), 'user_id' => $serviceUser->getId(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

                return 1;
            }
        }

        if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
            $output->writeln('Processing users subscribers');
        }

        // Updating users subscribers
        foreach ($usersForUpdate as $user) {
            $output->writeln('  Processing @' . $user->getLogin());
            $log->info('Processing @' . $user->getLogin());

            try {
                $userCurrentSubscribers = $api->getUserSubscribersById($user->getId());
            } catch (\Exception $e) {
                $output->writeln('    Error while getting subscribers. Skipping.');
                $log->error('Error while getting subscribers.', ['user_login' => $user->getLogin(), 'user_id' => $user->getId(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

                continue;
            }

            if (OutputInterface::VERBOSITY_VERBOSE === $output->getVerbosity()) {
                $output->writeln('    Updating user subscribers');
            }

            try {
                // Updating user subscribers
                $subscriptionsManager->updateUserSubscribers($user, $userCurrentSubscribers);
            } catch (\Exception $e) {
                $log->error('Error while updating user subscribers', ['user_login' => $user->getLogin(), 'user_id' => $user->getId(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            }

            // @todo move to the config
            usleep(500000);
        }

        return 0;
    }
}