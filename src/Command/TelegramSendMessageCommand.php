<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\Telegram\MessageSender;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:telegram:message', description: 'Send message via Telegram')]
class TelegramSendMessageCommand extends Command
{
    public function __construct(
        private readonly MessageSender $messenger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('chat-id', 'c', InputOption::VALUE_OPTIONAL, 'ID of the chat')
            ->addOption('stdin', 'i', InputOption::VALUE_NONE, 'Read message from stdin instead of option')
            ->addArgument('message', InputArgument::OPTIONAL, 'Text of the message')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('stdin')) {
            $message = \file_get_contents('php://stdin');
        } elseif (null !== $input->getArgument('message')) {
            $message = $input->getArgument('message');
        } else {
            $io->error('Either \'--stdin\' option or \'message\' argument should be specified.');

            return Command::FAILURE;
        }

        if (mb_strlen($message) > 4096) {
            $io->comment('Message is too long (>4096). Cutting the tail...');
            $message = \mb_substr($message, 0, 4090) . PHP_EOL . '...';
        }

        try {
            $this->messenger->sendMessageToChat(
                (int) $input->getOption('chat-id'),
                $message,
            );
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
