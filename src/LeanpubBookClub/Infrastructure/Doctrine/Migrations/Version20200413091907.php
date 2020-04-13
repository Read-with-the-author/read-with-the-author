<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200413091907 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE sessions ADD COLUMN wasCancelled BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__sessions AS SELECT sessionId, date, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall FROM sessions');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('CREATE TABLE sessions (sessionId VARCHAR(255) NOT NULL, date DATETIME NOT NULL, duration INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL, maximumNumberOfAttendees INTEGER NOT NULL, wasClosed BOOLEAN NOT NULL, urlForCall VARCHAR(255) DEFAULT NULL, PRIMARY KEY(sessionId))');
        $this->addSql('INSERT INTO sessions (sessionId, date, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall) SELECT sessionId, date, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall FROM __temp__sessions');
        $this->addSql('DROP TABLE __temp__sessions');
    }
}
