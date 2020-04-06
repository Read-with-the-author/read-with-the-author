<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200406074304 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE members ADD COLUMN wasGrantedAccess BOOLEAN DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__members AS SELECT memberId, emailAddress, accessToken, timeZone FROM members');
        $this->addSql('DROP TABLE members');
        $this->addSql('CREATE TABLE members (memberId VARCHAR(255) NOT NULL, emailAddress VARCHAR(255) NOT NULL, accessToken VARCHAR(255) DEFAULT NULL, timeZone VARCHAR(255) NOT NULL, PRIMARY KEY(memberId))');
        $this->addSql('INSERT INTO members (memberId, emailAddress, accessToken, timeZone) SELECT memberId, emailAddress, accessToken, timeZone FROM __temp__members');
        $this->addSql('DROP TABLE __temp__members');
    }
}
