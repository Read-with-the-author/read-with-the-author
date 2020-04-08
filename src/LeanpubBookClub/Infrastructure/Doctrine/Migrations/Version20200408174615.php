<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200408174615 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__sessions AS SELECT sessionId, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall, date FROM sessions');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('CREATE TABLE sessions (sessionId VARCHAR(255) NOT NULL COLLATE BINARY, duration INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL COLLATE BINARY, maximumNumberOfAttendees INTEGER NOT NULL, wasClosed BOOLEAN NOT NULL, urlForCall VARCHAR(255) DEFAULT NULL COLLATE BINARY, date DATETIME NOT NULL, PRIMARY KEY(sessionId))');
        $this->addSql('INSERT INTO sessions (sessionId, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall, date) SELECT sessionId, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall, date FROM __temp__sessions');
        $this->addSql('DROP TABLE __temp__sessions');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__sessions AS SELECT sessionId, date, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall FROM sessions');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('CREATE TABLE sessions (sessionId VARCHAR(255) NOT NULL, duration INTEGER DEFAULT NULL, description VARCHAR(255) NOT NULL, maximumNumberOfAttendees INTEGER NOT NULL, wasClosed BOOLEAN NOT NULL, urlForCall VARCHAR(255) DEFAULT NULL, date VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(sessionId))');
        $this->addSql('INSERT INTO sessions (sessionId, date, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall) SELECT sessionId, date, duration, description, maximumNumberOfAttendees, wasClosed, urlForCall FROM __temp__sessions');
        $this->addSql('DROP TABLE __temp__sessions');
    }
}
