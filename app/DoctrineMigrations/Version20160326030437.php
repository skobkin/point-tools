<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160326030437 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE posts.comments ALTER post_id TYPE TEXT');
        $this->addSql('ALTER TABLE posts.posts ALTER id TYPE TEXT');
        $this->addSql('ALTER TABLE posts.posts_files ALTER post_id TYPE TEXT');
        $this->addSql('ALTER TABLE posts.tags ALTER text TYPE TEXT');
        $this->addSql('ALTER TABLE posts.posts_tags ALTER post_id TYPE TEXT');
        $this->addSql('ALTER TABLE posts.posts_tags ALTER text TYPE TEXT');
        $this->addSql('DROP INDEX posts.uniq_744cc52c80445aea');
        $this->addSql('ALTER TABLE posts.files ADD remote_url TEXT NOT NULL');
        $this->addSql('ALTER TABLE posts.files DROP remoteurl');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_744CC52C68EA44FC ON posts.files (remote_url)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE posts.comments ALTER post_id TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE posts.posts ALTER id TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE posts.tags ALTER text TYPE VARCHAR(128)');
        $this->addSql('ALTER TABLE posts.posts_tags ALTER post_id TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE posts.posts_tags ALTER text TYPE VARCHAR(128)');
        $this->addSql('DROP INDEX posts.UNIQ_744CC52C68EA44FC');
        $this->addSql('ALTER TABLE posts.files ADD remoteurl VARCHAR(128) NOT NULL');
        $this->addSql('ALTER TABLE posts.files DROP remote_url');
        $this->addSql('CREATE UNIQUE INDEX uniq_744cc52c80445aea ON posts.files (remoteurl)');
        $this->addSql('ALTER TABLE posts.posts_files ALTER post_id TYPE VARCHAR(16)');
    }
}
