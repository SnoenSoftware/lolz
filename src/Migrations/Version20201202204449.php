<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201202204449 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__feed AS SELECT id, url, parser FROM feed');
        $this->addSql('DROP TABLE feed');
        $this->addSql('CREATE TABLE feed (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL COLLATE BINARY, parser VARCHAR(255) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO feed (id, url, parser) SELECT id, url, parser FROM __temp__feed');
        $this->addSql('DROP TABLE __temp__feed');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_234044ABF47645AE ON feed (url)');
        $this->addSql('DROP INDEX UNIQ_18EDB14DAC9C95FD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__lol AS SELECT id, url, image_url, caption, fetched, title, video_sources FROM lol');
        $this->addSql('DROP TABLE lol');
        $this->addSql('CREATE TABLE lol (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL COLLATE BINARY, image_url VARCHAR(255) NOT NULL COLLATE BINARY, caption VARCHAR(255) DEFAULT NULL COLLATE BINARY, fetched DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , title VARCHAR(255) DEFAULT NULL COLLATE BINARY, video_sources CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO lol (id, url, image_url, caption, fetched, title, video_sources) SELECT id, url, image_url, caption, fetched, title, video_sources FROM __temp__lol');
        $this->addSql('DROP TABLE __temp__lol');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_18EDB14DAC9C95FD ON lol (image_url)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_234044ABF47645AE');
        $this->addSql('CREATE TEMPORARY TABLE __temp__feed AS SELECT id, url, parser FROM feed');
        $this->addSql('DROP TABLE feed');
        $this->addSql('CREATE TABLE feed (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL, parser VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO feed (id, url, parser) SELECT id, url, parser FROM __temp__feed');
        $this->addSql('DROP TABLE __temp__feed');
        $this->addSql('DROP INDEX UNIQ_18EDB14DAC9C95FD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__lol AS SELECT id, url, image_url, caption, fetched, title, video_sources FROM lol');
        $this->addSql('DROP TABLE lol');
        $this->addSql('CREATE TABLE lol (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL, image_url VARCHAR(255) NOT NULL, caption VARCHAR(255) DEFAULT NULL, fetched DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , title VARCHAR(255) DEFAULT NULL, video_sources CLOB DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO lol (id, url, image_url, caption, fetched, title, video_sources) SELECT id, url, image_url, caption, fetched, title, video_sources FROM __temp__lol');
        $this->addSql('DROP TABLE __temp__lol');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_18EDB14DAC9C95FD ON lol (image_url)');
    }
}
