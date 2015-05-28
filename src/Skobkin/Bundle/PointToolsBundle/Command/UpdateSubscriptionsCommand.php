<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;


use Skobkin\Bundle\PointToolsBundle\Service\SubscriptionsManager;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var UserApi $api */
        $api = $this->getContainer()->get('skobkin_point_tools.api_user');
        /** @var SubscriptionsManager $subscriptionsManager */
        $subscriptionsManager = $this->getContainer()->get('skobkin_point_tools.subscriptions_manager');

        $serviceUserName = $this->getContainer()->getParameter('point_login');
        $serviceUser = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('SkobkinPointToolsBundle:User')->findOneBy(['login' => $serviceUserName]);

        if (!$serviceUser) {
            // @todo Retrieving user
        }

        $serviceSubscribers = $api->getUserSubscribersByLogin($serviceUserName);

        // Updating service subscribers
        $subscriptionsManager->updateUserSubscribers($serviceUser, $serviceSubscribers);

        // Updating service users subscribers
        foreach ($serviceSubscribers as $user) {
            $userCurrentSubscribers = $api->getUserSubscribersByLogin($user->getLogin());

            $subscriptionsManager->updateUserSubscribers($user, $userCurrentSubscribers);

            // @todo some pause for lower API load
        }
    }
}