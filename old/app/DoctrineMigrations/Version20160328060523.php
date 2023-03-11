<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160328060523 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE users.rename_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users.rename_log (id INT NOT NULL, user_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, old_login TEXT NOT NULL, new_login TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_10D64DDA76ED395 ON users.rename_log (user_id)');
        $this->addSql('CREATE INDEX idx_rename_log_date ON users.rename_log (date)');
        $this->addSql('CREATE INDEX idx_rename_log_old_login ON users.rename_log (old_login)');
        $this->addSql('ALTER TABLE users.rename_log ADD CONSTRAINT FK_10D64DDA76ED395 FOREIGN KEY (user_id) REFERENCES users.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE users.rename_log_id_seq CASCADE');
        $this->addSql('DROP TABLE users.rename_log');
    }
}
