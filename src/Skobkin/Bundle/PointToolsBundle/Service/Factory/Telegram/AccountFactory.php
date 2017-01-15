<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Telegram;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Telegram\Account;
use Skobkin\Bundle\PointToolsBundle\Repository\Telegram\AccountRepository;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\AbstractFactory;
use unreal4u\TelegramAPI\Telegram\Types\Message;

class AccountFactory extends AbstractFactory
{
    /**
     * @var AccountRepository
     */
    private $accountRepo;


    public function __construct(LoggerInterface $logger, AccountRepository $accountRepository)
    {
        parent::__construct($logger);
        $this->accountRepo = $accountRepository;
    }

    public function findOrCreateFromMessage(Message $message): Account
    {
        if (null === $account = $this->accountRepo->findOneBy(['id' => $message->from->id])) {
            $account = new Account($message->from->id);
            $this->accountRepo->add($account);
        }

        // Setting/updating account data
        $account
            ->setFirstName($message->from->first_name)
            ->setLastName($message->from->last_name)
            ->setUsername($message->from->username)
            ->setChatId($message->chat->id)
        ;

        return $account;
    }
}