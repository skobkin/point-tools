<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Schema refactoring
 */
class Version20171106023155 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER INDEX subscriptions.author_idx RENAME TO idx_subscription_author');
        $this->addSql('ALTER INDEX subscriptions.subscriber_idx RENAME TO idx_subscription_subscriber');
        $this->addSql('ALTER INDEX subscriptions.date_idx RENAME TO idx_subscription_date');
        $this->addSql('ALTER TABLE posts.posts_tags DROP CONSTRAINT FK_7870CC82BAD26311');
        $this->addSql('ALTER TABLE posts.posts_tags ADD CONSTRAINT FK_7870CC82BAD26311 FOREIGN KEY (tag_id) REFERENCES posts.tags (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX posts.idx_tag_text');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE INDEX idx_tag_text ON posts.tags (text)');
        $this->addSql('ALTER TABLE posts.posts_tags DROP CONSTRAINT fk_7870cc82bad26311');
        $this->addSql('ALTER TABLE posts.posts_tags ADD CONSTRAINT fk_7870cc82bad26311 FOREIGN KEY (tag_id) REFERENCES posts.tags (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX subscriptions.idx_subscription_author RENAME TO author_idx');
        $this->addSql('ALTER INDEX subscriptions.idx_subscription_date RENAME TO date_idx');
        $this->addSql('ALTER INDEX subscriptions.idx_subscription_subscriber RENAME TO subscriber_idx');
    }
}
