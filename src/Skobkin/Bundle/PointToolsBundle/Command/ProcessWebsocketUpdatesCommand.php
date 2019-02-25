<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use JMS\Serializer\Serializer;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;
use Pheanstalk\Job;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\WebSocket\Message;
use Skobkin\Bundle\PointToolsBundle\Service\WebSocket\WebSocketMessageProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command processes WebSocket updates MQ and stores new content in the DB
 */
class ProcessWebsocketUpdatesCommand extends Command
{
    /** @var PheanstalkProxy */
    private $bsClient;

    /** @var string */
    private $bsTubeName;

    /** @var Serializer */
    private $serializer;

    /** @var WebSocketMessageProcessor */
    private $messageProcessor;

    public function __construct(
        PheanstalkProxy $bsClient,
        string $bsTubeName,
        Serializer $serializer,
        WebSocketMessageProcessor $processor
    ) {
        $this->serializer = $serializer;
        $this->messageProcessor = $processor;
        $this->bsClient = $bsClient;
        $this->bsTubeName = $bsTubeName;

        parent::__construct();
    }


    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('point:update:websocket-messages')
            ->setDescription('Reads and processes updates from Beanstalkd queue pipe')
            ->addOption('keep-jobs', 'k', InputOption::VALUE_NONE, 'Don\'t delete jobs from queue after processing')
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keepJobs = (bool) $input->getOption('keep-jobs');

        /** @var Job $job */
        while ($job = $this->bsClient->reserveFromTube($this->bsTubeName, 0)) {
            try {
                $message = $this->serializer->deserialize($job->getData(), Message::class, 'json');
            } catch (\Exception $e) {
                $output->writeln(sprintf(
                    'Error while deserializing #%d data: \'%s\'',
                    $job->getId(),
                    $job->getData()
                ));

                continue;
            }

            $output->writeln('Processing job #'.$job->getId());

            if ($this->messageProcessor->processMessage($message)) {
                if ($keepJobs) {
                    $this->bsClient->release($job);
                } else {
                    $this->bsClient->delete($job);
                }
            } else {
                $this->bsClient->release($job);
            }
        }
    }
}
