<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200403132332 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE sessions (sessionId VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, maximumNumberOfAttendees INTEGER NOT NULL, wasClosed BOOLEAN NOT NULL, urlForCall VARCHAR(255) DEFAULT NULL, PRIMARY KEY(sessionId))');
        $this->addSql('CREATE TABLE attendees (sessionId VARCHAR(255) NOT NULL, memberId VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_C8C96B253950B5F6 ON attendees (sessionId)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE sessions');
        $this->addSql('DROP TABLE attendees');
    }
}
