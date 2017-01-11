<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Telegram;

use Skobkin\Bundle\PointToolsBundle\Entity\Telegram\Account;
use Skobkin\Bundle\PointToolsBundle\Repository\Telegram\AccountRepository;
use unreal4u\TelegramAPI\Telegram\Types\Message;

class AccountFactory
{
    /**
     * @var AccountRepository
     */
    private $accountRepo;


    public function __construct(AccountRepository $accountRepository)
    {
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