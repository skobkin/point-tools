<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20160328060523 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE users.rename_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users.rename_log (id INT NOT NULL, user_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, old_login TEXT NOT NULL, new_login TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_10D64DDA76ED395 ON users.rename_log (user_id)');
        $this->addSql('CREATE INDEX idx_rename_log_date ON users.rename_log (date)');
        $this->addSql('CREATE INDEX idx_rename_log_old_login ON users.rename_log (old_login)');
        $this->addSql('ALTER TABLE users.rename_log ADD CONSTRAINT FK_10D64DDA76ED395 FOREIGN KEY (user_id) REFERENCES users.users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE users.rename_log_id_seq CASCADE');
        $this->addSql('DROP TABLE users.rename_log');
    }
}
