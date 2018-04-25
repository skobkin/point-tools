<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use JMS\Serializer\Serializer;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\WebSocket\Message;
use Skobkin\Bundle\PointToolsBundle\Service\WebSocket\WebSocketMessageProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command processes WebSocket updates MQ and stores new content in the DB
 */
class ProcessWebsocketUpdatesCommand extends Command
{
    /** @var string */
    private $bsTubeName;

    /** @var Serializer */
    private $serializer;

    /** @var WebSocketMessageProcessor */
    private $messageProcessor;

    public function __construct(
        Serializer $serializer,
        WebSocketMessageProcessor $processor,
        string $bsTubeName
    ) {
        $this->serializer = $serializer;
        $this->messageProcessor = $processor;
        $this->bsTubeName = $bsTubeName;

        parent::__construct();
    }


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

        foreach ($updates as $update) {
            $message = $this->serializer->deserialize($update, Message::class, 'json');

            if ($this->messageProcessor->processMessage($message)) {
                // BS delete item
            } else {
                // BS return to queue
            }
        }
    }
}
