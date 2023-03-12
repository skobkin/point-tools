<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * PostgreSQL database initialization
 */
final class Version20150528203127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS users');
        $this->addSql('CREATE SCHEMA IF NOT EXISTS subscriptions');

        $this->addSql('CREATE SEQUENCE subscriptions.log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE subscriptions.subscriptions (author_id INT NOT NULL, subscriber_id INT NOT NULL, PRIMARY KEY(author_id, subscriber_id))');
        $this->addSql('CREATE INDEX IDX_3B7621A2F675F31B ON subscriptions.subscriptions (author_id)');
        $this->addSql('CREATE INDEX IDX_3B7621A27808B1AD ON subscriptions.subscriptions (subscriber_id)');
        $this->addSql('CREATE UNIQUE INDEX subscription_unique ON subscriptions.subscriptions (author_id, subscriber_id)');
        $this->addSql('CREATE TABLE subscriptions.log (id INT NOT NULL, author_id INT NOT NULL, subscriber_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, action VARCHAR(12) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_22DA64DDF675F31B ON subscriptions.log (author_id)');
        $this->addSql('CREATE INDEX IDX_22DA64DD7808B1AD ON subscriptions.log (subscriber_id)');
        $this->addSql('CREATE INDEX date_idx ON subscriptions.log (date)');
        $this->addSql('CREATE TABLE users.users (id INT NOT NULL, login VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_338ADFC4AA08CB10 ON users.users (login)');
        $this->addSql('ALTER TABLE subscriptions.subscriptions ADD CONSTRAINT FK_3B7621A2F675F31B FOREIGN KEY (author_id) REFERENCES users.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriptions.subscriptions ADD CONSTRAINT FK_3B7621A27808B1AD FOREIGN KEY (subscriber_id) REFERENCES users.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriptions.log ADD CONSTRAINT FK_22DA64DDF675F31B FOREIGN KEY (author_id) REFERENCES users.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriptions.log ADD CONSTRAINT FK_22DA64DD7808B1AD FOREIGN KEY (subscriber_id) REFERENCES users.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE subscriptions.subscriptions DROP CONSTRAINT FK_3B7621A2F675F31B');
        $this->addSql('ALTER TABLE subscriptions.subscriptions DROP CONSTRAINT FK_3B7621A27808B1AD');
        $this->addSql('ALTER TABLE subscriptions.log DROP CONSTRAINT FK_22DA64DDF675F31B');
        $this->addSql('ALTER TABLE subscriptions.log DROP CONSTRAINT FK_22DA64DD7808B1AD');
        $this->addSql('DROP SEQUENCE subscriptions.log_id_seq CASCADE');
        $this->addSql('DROP TABLE subscriptions.subscriptions');
        $this->addSql('DROP TABLE subscriptions.log');
        $this->addSql('DROP TABLE users.users');

        $this->addSql('DROP SCHEMA IF EXISTS users');
        $this->addSql('DROP SCHEMA IF EXISTS subscriptions');
    }
}
