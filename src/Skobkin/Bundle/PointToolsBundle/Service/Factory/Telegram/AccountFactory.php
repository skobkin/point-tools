<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Telegram;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Telegram\Account;
use unreal4u\TelegramAPI\Telegram\Types\Message;

class AccountFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $accountRepo;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->accountRepo = $em->getRepository('SkobkinPointToolsBundle:Telegram\Account');
    }

    public function findOrCreateFromMessage(Message $message): Account
    {
        if (null === $account = $this->accountRepo->findOneBy(['id' => $message->from->id])) {
            $account = new Account($message->from->id);
            $this->em->persist($account);
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