<?php

namespace Skobkin\Bundle\PointToolsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use unreal4u\TelegramAPI\Telegram\Methods\DeleteWebhook;
use unreal4u\TelegramAPI\Telegram\Methods\SetWebhook;

/**
 * Sets or deletes Telegram bot Web-Hook
 * @see https://core.telegram.org/bots/api#setwebhook
 */
class TelegramSetWebHookCommand extends ContainerAwareCommand
{
    private const MODE_SET = 'set';
    private const MODE_DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('telegram:webhook')
            ->setDescription('Set webhook')
            ->addArgument('mode', InputArgument::REQUIRED, 'Command mode (set or delete)')
            ->addArgument('host', InputArgument::OPTIONAL, 'Host of telegram hook')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $telegramClient = $container->get('app.telegram.api_client');

        if (self::MODE_SET === strtolower($input->getArgument('mode'))) {
            if (!$input->hasArgument('host')) {
                throw new \InvalidArgumentException('Host must be specified when using --set flag');
            }

            $router = $container->get('router');
            $token = $container->getParameter('telegram_token');

            $url = sprintf(
                'https://%s%s',
                $input->getArgument('host'),
                $router->generate('telegram_webhook', ['token' => $token])
            );

            $output->writeln('Setting webhook: '.$url);

            $setWebHook = new SetWebhook();
            $setWebHook->url = $url;
            $setWebHook->max_connections = (int) $container->getParameter('telegram_max_connections');

            $telegramClient->performApiRequest($setWebHook);

            $output->writeln('Done');
        } elseif (self::MODE_DELETE === strtolower($input->getArgument('mode'))) {
            $output->writeln('Deleting webhook');

            $deleteWebHook = new DeleteWebhook();

            $telegramClient->performApiRequest($deleteWebHook);

            $output->writeln('Done');
        } else {
            throw new \InvalidArgumentException(sprintf('Mode must be exaclty one of: %s', implode(', ', [self::MODE_SET, self::MODE_DELETE])));
        }

        return 0;
    }
}
