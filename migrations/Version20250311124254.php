<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311124254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE conversation_trip_participant (conversation_id INT NOT NULL, trip_participant_id INT NOT NULL, PRIMARY KEY(conversation_id, trip_participant_id))');
        $this->addSql('CREATE INDEX IDX_94BE6E3A9AC0396 ON conversation_trip_participant (conversation_id)');
        $this->addSql('CREATE INDEX IDX_94BE6E3ADC9194CB ON conversation_trip_participant (trip_participant_id)');
        $this->addSql('ALTER TABLE conversation_trip_participant ADD CONSTRAINT FK_94BE6E3A9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation_trip_participant ADD CONSTRAINT FK_94BE6E3ADC9194CB FOREIGN KEY (trip_participant_id) REFERENCES trip_participant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip ADD conversation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7656F53B9AC0396 ON trip (conversation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53B9AC0396');
        $this->addSql('ALTER TABLE conversation_trip_participant DROP CONSTRAINT FK_94BE6E3A9AC0396');
        $this->addSql('ALTER TABLE conversation_trip_participant DROP CONSTRAINT FK_94BE6E3ADC9194CB');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE conversation_trip_participant');
        $this->addSql('DROP INDEX UNIQ_7656F53B9AC0396');
        $this->addSql('ALTER TABLE trip DROP conversation_id');
    }
}
