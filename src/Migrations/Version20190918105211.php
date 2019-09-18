<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190918105211 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE lol ADD COLUMN title VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_18EDB14DAC9C95FD');
        $this->addSql('CREATE TEMPORARY TABLE __temp__lol AS SELECT id, url, image_url, caption, fetched FROM lol');
        $this->addSql('DROP TABLE lol');
        $this->addSql('CREATE TABLE lol (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL, image_url VARCHAR(255) NOT NULL, caption VARCHAR(255) DEFAULT NULL, fetched DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO lol (id, url, image_url, caption, fetched) SELECT id, url, image_url, caption, fetched FROM __temp__lol');
        $this->addSql('DROP TABLE __temp__lol');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_18EDB14DAC9C95FD ON lol (image_url)');
    }
}
