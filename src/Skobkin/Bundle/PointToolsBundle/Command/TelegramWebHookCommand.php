<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use unreal4u\TelegramAPI\Telegram\Methods\{DeleteWebhook, SetWebhook};
use unreal4u\TelegramAPI\TgLog;

/**
 * Sets or deletes Telegram bot Web-Hook
 * @see https://core.telegram.org/bots/api#setwebhook
 */
class TelegramWebHookCommand extends Command
{
    private const MODE_SET = 'set';
    private const MODE_DELETE = 'delete';

    /** @var TgLog */
    private $client;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var string */
    private $token;

    /** @var int */
    private $maxConnections;

    public function __construct(TgLog $client, UrlGeneratorInterface $router, string $telegramToken, int $telegramWebhookMaxConnections)
    {
        parent::__construct();

        $this->client = $client;
        $this->router = $router;
        $this->token = $telegramToken;
        $this->maxConnections = $telegramWebhookMaxConnections;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('telegram:webhook')
            ->setDescription('Set webhook')
            ->addArgument('mode', InputArgument::REQUIRED, 'Command mode (set or delete)')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (self::MODE_SET === strtolower($input->getArgument('mode'))) {

            $url = $this->router->generate(
                'telegram_webhook',
                ['token' => $this->token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $output->writeln('Setting webhook: '.$url);

            $setWebHook = new SetWebhook();
            $setWebHook->url = $url;
            $setWebHook->max_connections = $this->maxConnections;

            $this->client->performApiRequest($setWebHook);

            $output->writeln('Done');
        } elseif (self::MODE_DELETE === strtolower($input->getArgument('mode'))) {
            $output->writeln('Deleting webhook');

            $deleteWebHook = new DeleteWebhook();

            $this->client->performApiRequest($deleteWebHook);

            $output->writeln('Done');
        } else {
            throw new \InvalidArgumentException(sprintf('Mode must be exactly one of: %s', implode(', ', [self::MODE_SET, self::MODE_DELETE])));
        }

        return 0;
    }
}
