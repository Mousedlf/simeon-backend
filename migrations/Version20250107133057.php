<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107133057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expense (id SERIAL NOT NULL, day_of_trip_id INT NOT NULL, trip_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, sum DOUBLE PRECISION NOT NULL, divide BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2D3A8DA67E495F60 ON expense (day_of_trip_id)');
        $this->addSql('CREATE INDEX IDX_2D3A8DA6A5BC2E0E ON expense (trip_id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA67E495F60 FOREIGN KEY (day_of_trip_id) REFERENCES day_of_trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT FK_2D3A8DA67E495F60');
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT FK_2D3A8DA6A5BC2E0E');
        $this->addSql('DROP TABLE expense');
    }
}
