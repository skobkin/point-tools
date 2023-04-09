<?php
declare(strict_types=1);

namespace App\Factory\Telegram;

use App\Factory\AbstractFactory;
use Psr\Log\LoggerInterface;
use App\Entity\Telegram\Account;
use App\Repository\Telegram\AccountRepository;
use TelegramBot\Api\Types\Message;

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
        if (null === $account = $this->accountRepository->findOneBy(['id' => $message->getFrom()->getId()])) {
            $account = new Account($message->getFrom()->getId());
            $this->accountRepository->save($account);
        }

        $account->updateFromMessageData(
            $message->getFrom()->getFirstName(),
            $message->getFrom()->getLastName(),
            $message->getFrom()->getUsername(),
            $message->getFrom()->getId(),
        );

        return $account;
    }
}
