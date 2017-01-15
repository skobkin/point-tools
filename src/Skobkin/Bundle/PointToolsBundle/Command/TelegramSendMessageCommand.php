<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Skobkin\Bundle\PointToolsBundle\Service\Telegram\MessageSender;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TelegramSendMessageCommand extends ContainerAwareCommand
{
    /**
     * @var MessageSender
     */
    private $messenger;

    /**
     * @var int
     */
    private $logChatId;

    public function setMessenger(MessageSender $messenger): void
    {
        $this->messenger = $messenger;
    }

    /**
     * @param int $logChatId
     */
    public function setLogChatId(int $logChatId): void
    {
        $this->logChatId = $logChatId;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('telegram:send-message')
            ->setDescription('Send message via Telegram')
            ->addOption('chat-id', 'c', InputOption::VALUE_OPTIONAL, 'ID of the chat')
            ->addOption('stdin', 'i', InputOption::VALUE_NONE, 'Read message from stdin instead of option')
            ->addArgument('message', InputArgument::OPTIONAL, 'Text of the message')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Sending message...');

        if ($input->getOption('stdin')) {
            $message = file_get_contents('php://stdin');
        } elseif (null !== $input->getArgument('message')) {
            $message = $input->getArgument('message');
        } else {
            $output->writeln('<error>Either \'--stdin\' option or \'message\' argument should be specified.</error>');

            return 1;
        }

        if (mb_strlen($message) > 4096) {
            $output->writeln('<comment>Message is too long (>4096). Cutting the tail...</comment>');
            $message = mb_substr($message, 0, 4090).PHP_EOL.'...';
        }

        try {
            $this->messenger->sendMessageToChat(
                (int) $input->getOption('chat-id') ?: $this->logChatId,
                $message
            );
        } catch (\Exception $e) {
            $output->writeln('Error: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
