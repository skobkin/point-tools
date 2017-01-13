<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use unreal4u\TelegramAPI\Telegram\Methods\DeleteWebhook;
use unreal4u\TelegramAPI\Telegram\Methods\SetWebhook;
use unreal4u\TelegramAPI\TgLog;

/**
 * Sets or deletes Telegram bot Web-Hook
 * @see https://core.telegram.org/bots/api#setwebhook
 */
class TelegramWebHookCommand extends ContainerAwareCommand
{
    private const MODE_SET = 'set';
    private const MODE_DELETE = 'delete';

    /**
     * @var TgLog
     */
    private $client;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $maxConnections;

    public function setClient(TgLog $client): void
    {
        $this->client = $client;
    }

    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setMaxConnections(int $maxConnections)
    {
        $this->maxConnections = $maxConnections;
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
