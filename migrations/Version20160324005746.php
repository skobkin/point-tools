<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20160324005746 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS posts');

        $this->addSql('CREATE SEQUENCE posts.comments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE posts.tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE posts.posts_tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE posts.comments (id INT NOT NULL, post_id VARCHAR(16) DEFAULT NULL, author_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_rec BOOLEAN NOT NULL, is_deleted BOOLEAN NOT NULL, number SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_628999754B89032C ON posts.comments (post_id)');
        $this->addSql('CREATE INDEX IDX_62899975F675F31B ON posts.comments (author_id)');
        $this->addSql('CREATE INDEX IDX_62899975727ACA70 ON posts.comments (parent_id)');
        $this->addSql('CREATE INDEX idx_comment_created_at ON posts.comments (created_at)');
        $this->addSql('CREATE TABLE posts.posts (id VARCHAR(16) NOT NULL, author INT DEFAULT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(6) NOT NULL, private BOOLEAN DEFAULT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA243F8FBDAFD8C8 ON posts.posts (author)');
        $this->addSql('CREATE INDEX idx_post_created_at ON posts.posts (created_at)');
        $this->addSql('CREATE INDEX idx_post_private ON posts.posts (private)');
        $this->addSql('CREATE TABLE posts.tags (id INT NOT NULL, text VARCHAR(128) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_115E64E03B8BA7C7 ON posts.tags (text)');
        $this->addSql('CREATE TABLE posts.posts_tags (id INT NOT NULL, post_id VARCHAR(16) DEFAULT NULL, tag_id INT DEFAULT NULL, text VARCHAR(128) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7870CC824B89032C ON posts.posts_tags (post_id)');
        $this->addSql('CREATE INDEX IDX_7870CC82BAD26311 ON posts.posts_tags (tag_id)');
        $this->addSql('ALTER TABLE posts.comments ADD CONSTRAINT FK_628999754B89032C FOREIGN KEY (post_id) REFERENCES posts.posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts.comments ADD CONSTRAINT FK_62899975F675F31B FOREIGN KEY (author_id) REFERENCES users.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts.comments ADD CONSTRAINT FK_62899975727ACA70 FOREIGN KEY (parent_id) REFERENCES posts.comments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts.posts ADD CONSTRAINT FK_FA243F8FBDAFD8C8 FOREIGN KEY (author) REFERENCES users.users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts.posts_tags ADD CONSTRAINT FK_7870CC824B89032C FOREIGN KEY (post_id) REFERENCES posts.posts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts.posts_tags ADD CONSTRAINT FK_7870CC82BAD26311 FOREIGN KEY (tag_id) REFERENCES posts.tags (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE posts.comments DROP CONSTRAINT FK_62899975727ACA70');
        $this->addSql('ALTER TABLE posts.comments DROP CONSTRAINT FK_628999754B89032C');
        $this->addSql('ALTER TABLE posts.posts_tags DROP CONSTRAINT FK_7870CC824B89032C');
        $this->addSql('ALTER TABLE posts.posts_tags DROP CONSTRAINT FK_7870CC82BAD26311');
        $this->addSql('DROP SEQUENCE posts.comments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE posts.tags_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE posts.posts_tags_id_seq CASCADE');
        $this->addSql('DROP TABLE posts.comments');
        $this->addSql('DROP TABLE posts.posts');
        $this->addSql('DROP TABLE posts.tags');
        $this->addSql('DROP TABLE posts.posts_tags');

        $this->addSql('DROP SCHEMA IF EXISTS posts');
    }
}
