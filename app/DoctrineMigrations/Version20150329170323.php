<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150329170323 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, INDEX idx_name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriptions_events (id INT AUTO_INCREMENT NOT NULL, subscriber_id INT NOT NULL, author_id INT NOT NULL, date DATETIME NOT NULL, action VARCHAR(12) NOT NULL, INDEX IDX_7778274B7808B1AD (subscriber_id), INDEX IDX_7778274BF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriptions (author_id INT NOT NULL, subscriber_id INT NOT NULL, INDEX IDX_4778A01F675F31B (author_id), INDEX IDX_4778A017808B1AD (subscriber_id), PRIMARY KEY(author_id, subscriber_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscriptions_events ADD CONSTRAINT FK_7778274B7808B1AD FOREIGN KEY (subscriber_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE subscriptions_events ADD CONSTRAINT FK_7778274BF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A017808B1AD FOREIGN KEY (subscriber_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscriptions_events DROP FOREIGN KEY FK_7778274B7808B1AD');
        $this->addSql('ALTER TABLE subscriptions_events DROP FOREIGN KEY FK_7778274BF675F31B');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A01F675F31B');
        $this->addSql('ALTER TABLE subscriptions DROP FOREIGN KEY FK_4778A017808B1AD');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE subscriptions_events');
        $this->addSql('DROP TABLE subscriptions');
    }
}
