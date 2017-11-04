<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * New fields for User entity: 'public' and 'whitelistOnly' (privacy support)
 */
class Version20171104182713 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users.users ADD public BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('ALTER TABLE users.users ADD whitelist_only BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('CREATE INDEX idx_user_public ON users.users (public)');
        $this->addSql('CREATE INDEX idx_user_removed ON users.users (is_removed)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX users.idx_user_public');
        $this->addSql('DROP INDEX users.idx_user_removed');
        $this->addSql('ALTER TABLE users.users DROP public');
        $this->addSql('ALTER TABLE users.users DROP whitelist_only');
    }
}
