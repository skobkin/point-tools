<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20170108152129 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER INDEX subscriptions.idx_22da64ddf675f31b RENAME TO author_idx');
        $this->addSql('ALTER INDEX subscriptions.idx_22da64dd7808b1ad RENAME TO subscriber_idx');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER INDEX subscriptions.subscriber_idx RENAME TO idx_22da64dd7808b1ad');
        $this->addSql('ALTER INDEX subscriptions.author_idx RENAME TO idx_22da64ddf675f31b');
    }
}
