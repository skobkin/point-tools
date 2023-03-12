<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Telegram accounts added
 */
final class Version20170105191821 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE users.telegram_accounts (account_id INT NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, linked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, first_name TEXT NOT NULL, last_name TEXT DEFAULT NULL, username TEXT DEFAULT NULL, private_chat_id BIGINT DEFAULT NULL, subscriber_notification BOOLEAN NOT NULL, rename_notification BOOLEAN NOT NULL, PRIMARY KEY(account_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1EDB9B25A76ED395 ON users.telegram_accounts (user_id)');
        $this->addSql('CREATE INDEX subscriber_notification_idx ON users.telegram_accounts (subscriber_notification) WHERE subscriber_notification = TRUE');
        $this->addSql('CREATE INDEX rename_notification_idx ON users.telegram_accounts (rename_notification) WHERE rename_notification = TRUE');
        $this->addSql('ALTER TABLE users.telegram_accounts ADD CONSTRAINT FK_1EDB9B25A76ED395 FOREIGN KEY (user_id) REFERENCES users.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE users.telegram_accounts');
    }
}
