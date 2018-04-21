<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessWebsocketUpdatesCommand extends Command
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('point:update:websocket-messages')
            ->setDescription('Reads and processes updates from Beanstalkd queue pipe')
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
