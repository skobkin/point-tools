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
                'check-only',
                null,
                InputOption::VALUE_NONE,
                'If set, command will not perform write operations in the database'
            )
            // @todo add option for checking only selected user
        ;
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $log = $this->getContainer()->get('logger');

        $log->info('UpdateSubscriptionsCommand started.');

        /** @var UserApi $api */
        $api = $this->getContainer()->get('skobkin_point_tools.api_user');
        /** @var SubscriptionsManager $subscriptionsManager */
        $subscriptionsManager = $this->getContainer()->get('skobkin_point_tools.subscriptions_manager');

        try {
            $serviceUserId = $this->getContainer()->getParameter('point_id');
        } catch (\InvalidArgumentException $e) {
            $log->alert('Could not get point_id parameter from config file', ['exception_message' => $e->getMessage()]);
            return false;
        }

        $serviceUser = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('SkobkinPointToolsBundle:User')->find($serviceUserId);

        if (!$serviceUser) {
            $log->info('Service user not found');
            // @todo Retrieving user

            return false;
        }

        if ($output->isVerbose()) {
            $output->writeln('Getting service subscribers');
        }

        try {
            $serviceSubscribers = $api->getUserSubscribersById($serviceUserId);
        } catch (\Exception $e) {
            // @todo fallback to the local subscribers list
            $output->writeln('Error while getting service subscribers');
            $log->error('Error while getting service subscribers.', ['user_login' => $serviceUser->getLogin(), 'user_id' => $serviceUser->getId(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

            return false;
        }

        if ($output->isVerbose()) {
            $output->writeln('Updating service subscribers');
        }

        // Updating service subscribers
        try {
            $subscriptionsManager->updateUserSubscribers($serviceUser, $serviceSubscribers);
        } catch (\Exception $e) {
            $log->error('Error while updating service subscribers', ['user_login' => $serviceUser->getLogin(), 'user_id' => $serviceUser->getId(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

            return false;
        }

        if ($output->isVerbose()) {
            $output->writeln('Processing service subscribers');
        }

        // Updating service users subscribers
        foreach ($serviceSubscribers as $user) {
            $output->writeln('  Processing @' . $user->getLogin());
            $log->info('Processing @' . $user->getLogin());

            try {
                $userCurrentSubscribers = $api->getUserSubscribersById($user->getId());
            } catch (\Exception $e) {
                $output->writeln('    Error while getting subscribers. Skipping.');
                $log->error('Error while getting subscribers.', ['user_login' => $user->getLogin(), 'user_id' => $user->getId(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);

                continue;
            }

            if ($output->isVerbose()) {
                $output->writeln('    Updating user subscribers');
            }

            try {
                // Updating user subscribers
                $subscriptionsManager->updateUserSubscribers($user, $userCurrentSubscribers);
            } catch (\Exception $e) {
                $log->error('Error while updating user subscribers', ['user_login' => $user->getLogin(), 'user_id' => $user->getId(), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            }

            // @todo move to the config
            usleep(200000);
        }
    }
}