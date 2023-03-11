<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160325001415 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE posts.files_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE posts.comments_files (comment_id INT NOT NULL, file_id INT NOT NULL, PRIMARY KEY(comment_id, file_id))');
        $this->addSql('CREATE INDEX IDX_D0F69329F8697D13 ON posts.comments_files (comment_id)');
        $this->addSql('CREATE INDEX IDX_D0F6932993CB796C ON posts.comments_files (file_id)');
        $this->addSql('CREATE TABLE posts.posts_files (post_id VARCHAR(16) NOT NULL, file_id INT NOT NULL, PRIMARY KEY(post_id, file_id))');
        $this->addSql('CREATE INDEX IDX_D799EBF04B89032C ON posts.posts_files (post_id)');
        $this->addSql('CREATE INDEX IDX_D799EBF093CB796C ON posts.posts_files (file_id)');
        $this->addSql('CREATE TABLE posts.files (id INT NOT NULL, remoteUrl VARCHAR(128) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_744CC52C80445AEA ON posts.files (remoteUrl)');
        $this->addSql('ALTER TABLE posts.comments_files ADD CONSTRAINT FK_D0F69329F8697D13 FOREIGN KEY (comment_id) REFERENCES posts.comments (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts.comments_files ADD CONSTRAINT FK_D0F6932993CB796C FOREIGN KEY (file_id) REFERENCES posts.files (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts.posts_files ADD CONSTRAINT FK_D799EBF04B89032C FOREIGN KEY (post_id) REFERENCES posts.posts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE posts.posts_files ADD CONSTRAINT FK_D799EBF093CB796C FOREIGN KEY (file_id) REFERENCES posts.files (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE posts.comments_files DROP CONSTRAINT FK_D0F6932993CB796C');
        $this->addSql('ALTER TABLE posts.posts_files DROP CONSTRAINT FK_D799EBF093CB796C');
        $this->addSql('DROP SEQUENCE posts.files_id_seq CASCADE');
        $this->addSql('DROP TABLE posts.comments_files');
        $this->addSql('DROP TABLE posts.posts_files');
        $this->addSql('DROP TABLE posts.files');
    }
}
