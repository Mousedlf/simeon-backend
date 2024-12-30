<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229181842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip_participant (id SERIAL NOT NULL, trip_id INT NOT NULL, participant_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_23BECC9BA5BC2E0E ON trip_participant (trip_id)');
        $this->addSql('CREATE INDEX IDX_23BECC9B9D1C3019 ON trip_participant (participant_id)');
        $this->addSql('ALTER TABLE trip_participant ADD CONSTRAINT FK_23BECC9BA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_participant ADD CONSTRAINT FK_23BECC9B9D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_user DROP CONSTRAINT fk_a6ab4522a5bc2e0e');
        $this->addSql('ALTER TABLE trip_user DROP CONSTRAINT fk_a6ab4522a76ed395');
        $this->addSql('DROP TABLE trip_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE trip_user (trip_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(trip_id, user_id))');
        $this->addSql('CREATE INDEX idx_a6ab4522a76ed395 ON trip_user (user_id)');
        $this->addSql('CREATE INDEX idx_a6ab4522a5bc2e0e ON trip_user (trip_id)');
        $this->addSql('ALTER TABLE trip_user ADD CONSTRAINT fk_a6ab4522a5bc2e0e FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_user ADD CONSTRAINT fk_a6ab4522a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_participant DROP CONSTRAINT FK_23BECC9BA5BC2E0E');
        $this->addSql('ALTER TABLE trip_participant DROP CONSTRAINT FK_23BECC9B9D1C3019');
        $this->addSql('DROP TABLE trip_participant');
    }
}
