<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Issue #44 - Post and Comment schema refactoring.
 * - Post subscription status added.
 * - Comments parent-child relations removed.
 * - User login index
 * - Other adjustments
 */
class Version20180427143940 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE posts.posts ADD is_subscribed BOOLEAN DEFAULT FALSE NOT NULL');
        // Removing parent_id constraint and index
        $this->addSql('ALTER TABLE posts.comments DROP CONSTRAINT fk_62899975727aca70');
        $this->addSql('DROP INDEX posts.idx_62899975727aca70');
        $this->addSql('ALTER TABLE posts.comments DROP parent_id');

        $this->addSql('ALTER TABLE posts.comments ADD to_number INT');
        $this->addSql('ALTER TABLE posts.comments ALTER number TYPE INT');
        $this->addSql('ALTER TABLE posts.comments ALTER number DROP DEFAULT');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6289997596901F54 ON posts.comments (number)');

        $this->addSql('CREATE INDEX idx_user_login ON users.users (login)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE posts.posts DROP is_subscribed');
        $this->addSql('DROP INDEX posts.UNIQ_6289997596901F54');
        $this->addSql('ALTER TABLE posts.comments ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts.comments DROP to_number');
        $this->addSql('ALTER TABLE posts.comments ALTER number TYPE SMALLINT');
        $this->addSql('ALTER TABLE posts.comments ALTER number DROP DEFAULT');
        $this->addSql('ALTER TABLE posts.comments ADD CONSTRAINT fk_62899975727aca70 FOREIGN KEY (parent_id) REFERENCES posts.comments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_62899975727aca70 ON posts.comments (parent_id)');
        $this->addSql('DROP INDEX users.idx_user_login');
    }
}
