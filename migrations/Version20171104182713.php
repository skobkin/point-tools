<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * New fields for User entity: 'public' and 'whitelistOnly' (privacy support)
 */
final class Version20171104182713 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE users.users ADD public BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('ALTER TABLE users.users ADD whitelist_only BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addSql('CREATE INDEX idx_user_public ON users.users (public)');
        $this->addSql('CREATE INDEX idx_user_removed ON users.users (is_removed)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX users.idx_user_public');
        $this->addSql('DROP INDEX users.idx_user_removed');
        $this->addSql('ALTER TABLE users.users DROP public');
        $this->addSql('ALTER TABLE users.users DROP whitelist_only');
    }
}
