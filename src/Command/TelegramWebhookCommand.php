<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\BotApi;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(name: 'app:telegram:webhook', description: 'Set webhook')]
class TelegramWebhookCommand extends Command
{
    private const MODE_SET = 'set';
    private const MODE_DELETE = 'delete';

    public function __construct(
        private readonly BotApi $client,
        private readonly UrlGeneratorInterface $router,
        private readonly string $telegramToken,
        private readonly int $telegramWebhookMaxConnections,
    ) {
        parent::__construct();
    }

    protected function configure(): void
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

            try {
                $this->client->setWebhook(
                    url: $url,
                    maxConnections: $this->telegramWebhookMaxConnections,
                );
            } catch (\Exception $e) {
                $io->error(\sprintf(
                    'setWebhook error: %s',
                    $e->getMessage(),
                ));
            }

            $output->writeln('Done');
        } elseif (self::MODE_DELETE === strtolower($input->getArgument('mode'))) {
            $this->client->deleteWebhook();
        } else {
            throw new \InvalidArgumentException(sprintf('Mode must be exactly one of: %s', implode(', ', [self::MODE_SET, self::MODE_DELETE])));
        }

        return Command::SUCCESS;
    }
}
