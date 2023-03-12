<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20160326030437 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
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

    public function down(Schema $schema): void
    {
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
