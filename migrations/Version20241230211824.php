<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230211824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE day_of_trip (id SERIAL NOT NULL, trip_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, note TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2BAB0D8AA5BC2E0E ON day_of_trip (trip_id)');
        $this->addSql('COMMENT ON COLUMN day_of_trip.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE day_of_trip ADD CONSTRAINT FK_2BAB0D8AA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip ADD public BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE trip RENAME COLUMN days TO nb_of_days');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE day_of_trip DROP CONSTRAINT FK_2BAB0D8AA5BC2E0E');
        $this->addSql('DROP TABLE day_of_trip');
        $this->addSql('ALTER TABLE trip DROP public');
        $this->addSql('ALTER TABLE trip RENAME COLUMN nb_of_days TO days');
    }
}
