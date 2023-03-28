<?php
declare(strict_types=1);

namespace App\Factory\Telegram;

use App\Factory\AbstractFactory;
use Psr\Log\LoggerInterface;
use App\Entity\Telegram\Account;
use App\Repository\Telegram\AccountRepository;
use unreal4u\Telegram\Types\Message;

class AccountFactory extends AbstractFactory
{
    public function __construct(
        LoggerInterface $logger,
        private readonly AccountRepository $accountRepository,
    ) {
        parent::__construct($logger);
    }

    public function findOrCreateFromMessage(Message $message): Account
    {
        if (null === $account = $this->accountRepository->findOneBy(['id' => $message->from->id])) {
            $account = new Account($message->from->id);
            $this->accountRepository->save($account);
        }

        // Setting/updating account data
        $account->updateFromMessageData(
            $message->from->first_name,
            $message->from->last_name,
            $message->from->username,
            $message->chat->id
        );

        return $account;
    }
}
