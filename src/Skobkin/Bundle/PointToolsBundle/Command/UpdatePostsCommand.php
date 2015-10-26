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

class UpdatePostsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('point:messages:receive')
            ->setDescription('Receives last posts and comments and saves them to the database')
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

        // @todo
    }
}