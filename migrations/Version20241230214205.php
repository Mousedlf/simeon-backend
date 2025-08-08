<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230214205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE day_of_trip DROP CONSTRAINT FK_2BAB0D8AA5BC2E0E');
        $this->addSql('ALTER TABLE day_of_trip ADD CONSTRAINT FK_2BAB0D8AA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_invite DROP CONSTRAINT FK_28ED4B7DA5BC2E0E');
        $this->addSql('ALTER TABLE trip_invite ADD CONSTRAINT FK_28ED4B7DA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_participant DROP CONSTRAINT FK_23BECC9BA5BC2E0E');
        $this->addSql('ALTER TABLE trip_participant DROP CONSTRAINT FK_23BECC9B9D1C3019');
        $this->addSql('ALTER TABLE trip_participant ADD CONSTRAINT FK_23BECC9BA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_participant ADD CONSTRAINT FK_23BECC9B9D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trip_invite DROP CONSTRAINT fk_28ed4b7da5bc2e0e');
        $this->addSql('ALTER TABLE trip_invite ADD CONSTRAINT fk_28ed4b7da5bc2e0e FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_participant DROP CONSTRAINT fk_23becc9ba5bc2e0e');
        $this->addSql('ALTER TABLE trip_participant DROP CONSTRAINT fk_23becc9b9d1c3019');
        $this->addSql('ALTER TABLE trip_participant ADD CONSTRAINT fk_23becc9ba5bc2e0e FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_participant ADD CONSTRAINT fk_23becc9b9d1c3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE day_of_trip DROP CONSTRAINT fk_2bab0d8aa5bc2e0e');
        $this->addSql('ALTER TABLE day_of_trip ADD CONSTRAINT fk_2bab0d8aa5bc2e0e FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
