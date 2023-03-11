<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170108152129 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER INDEX subscriptions.idx_22da64ddf675f31b RENAME TO author_idx');
        $this->addSql('ALTER INDEX subscriptions.idx_22da64dd7808b1ad RENAME TO subscriber_idx');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER INDEX subscriptions.subscriber_idx RENAME TO idx_22da64dd7808b1ad');
        $this->addSql('ALTER INDEX subscriptions.author_idx RENAME TO idx_22da64ddf675f31b');
    }
}
