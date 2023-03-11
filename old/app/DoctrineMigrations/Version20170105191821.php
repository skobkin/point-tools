<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Telegram accounts added
 */
class Version20170105191821 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE users.telegram_accounts (account_id INT NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, linked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, first_name TEXT NOT NULL, last_name TEXT DEFAULT NULL, username TEXT DEFAULT NULL, private_chat_id BIGINT DEFAULT NULL, subscriber_notification BOOLEAN NOT NULL, rename_notification BOOLEAN NOT NULL, PRIMARY KEY(account_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1EDB9B25A76ED395 ON users.telegram_accounts (user_id)');
        $this->addSql('CREATE INDEX subscriber_notification_idx ON users.telegram_accounts (subscriber_notification) WHERE subscriber_notification = TRUE');
        $this->addSql('CREATE INDEX rename_notification_idx ON users.telegram_accounts (rename_notification) WHERE rename_notification = TRUE');
        $this->addSql('ALTER TABLE users.telegram_accounts ADD CONSTRAINT FK_1EDB9B25A76ED395 FOREIGN KEY (user_id) REFERENCES users.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE users.telegram_accounts');
    }
}
