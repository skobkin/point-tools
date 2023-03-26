<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use unreal4u\Telegram\Methods\SetWebhook;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use unreal4u\TgLog;

#[AsCommand(name: 'app:telegram:webhook', description: 'Set webhook')]
class TelegramWebhookCommand extends Command
{
    private const MODE_SET = 'set';
    private const MODE_DELETE = 'delete';

    public function __construct(
        private readonly TgLog $client,
        private readonly UrlGeneratorInterface $router,
        private readonly string $telegramToken,
        private readonly int $telegramWebhookMaxConnections,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('mode', InputArgument::REQUIRED, 'Command mode (set or delete)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (self::MODE_SET === strtolower($input->getArgument('mode'))) {

            $url = $this->router->generate(
                'telegram_webhook',
                ['token' => $this->telegramToken],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $io->info('Setting webhook: ' . $url);

            $setWebHook = new SetWebhook();
            $setWebHook->url = $url;
            $setWebHook->max_connections = $this->telegramWebhookMaxConnections;

            $this->client->performApiRequest($setWebHook);

            $output->writeln('Done');
        } elseif (self::MODE_DELETE === strtolower($input->getArgument('mode'))) {
            $io->warning('Unsupported until moving to another library.');
        } else {
            throw new \InvalidArgumentException(sprintf('Mode must be exactly one of: %s', implode(', ', [self::MODE_SET, self::MODE_DELETE])));
        }

        return Command::SUCCESS;
    }
}
