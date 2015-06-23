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
            ->setName('point:update:posts')
            ->setDescription('Update posts from /all')
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


    }
}