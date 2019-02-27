<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Serializer;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;
use Pheanstalk\Job;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\WebSocket\Message;
use Skobkin\Bundle\PointToolsBundle\Exception\WebSocket\UnsupportedTypeException;
use Skobkin\Bundle\PointToolsBundle\Service\WebSocket\WebSocketMessageProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command processes WebSocket updates MQ and stores new content in the DB
 */
class ProcessWebsocketUpdatesCommand extends Command
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PheanstalkProxy */
    private $bsClient;

    /** @var string */
    private $bsTubeName;

    /** @var Serializer */
    private $serializer;

    /** @var WebSocketMessageProcessor */
    private $messageProcessor;

    /** @var \Raven_Client */
    private $sentryClient;

    public function __construct(
        EntityManagerInterface $em,
        \Raven_Client $raven,
        PheanstalkProxy $bsClient,
        string $bsTubeName,
        Serializer $serializer,
        WebSocketMessageProcessor $processor
    ) {
        $this->em = $em;
        $this->sentryClient = $raven;
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
                /** @var Message $message */
                $message = $this->serializer->deserialize($job->getData(), Message::class, 'json');
            } catch (\Exception $e) {
                $output->writeln(sprintf(
                    'Error while deserializing #%d data: \'%s\'',
                    $job->getId(),
                    $job->getData()
                ));
                $this->sentryClient->captureException($e);

                continue;
            }

            $output->writeln('Processing job #'.$job->getId().' ('.$message->getA().')');

            try {
                if ($this->messageProcessor->processMessage($message)) {
                    $this->em->flush();

                    if (!$keepJobs) {
                        $this->bsClient->delete($job);
                    }
                }
            } catch (UnsupportedTypeException $e) {
                $output->writeln('  Unsupported message type: '.$message->getA());
                $this->sentryClient->captureException($e);

                continue;
            } catch (\Exception $e) {
                $output->writeln('  Message processing error: '.$e->getMessage());
                $this->sentryClient->captureException($e);

                continue;
            }
        }
    }
}
