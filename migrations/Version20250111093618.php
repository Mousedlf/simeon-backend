<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250111093618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expense_trip_participant (expense_id INT NOT NULL, trip_participant_id INT NOT NULL, PRIMARY KEY(expense_id, trip_participant_id))');
        $this->addSql('CREATE INDEX IDX_3DE58CDCF395DB7B ON expense_trip_participant (expense_id)');
        $this->addSql('CREATE INDEX IDX_3DE58CDCDC9194CB ON expense_trip_participant (trip_participant_id)');
        $this->addSql('ALTER TABLE expense_trip_participant ADD CONSTRAINT FK_3DE58CDCF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense_trip_participant ADD CONSTRAINT FK_3DE58CDCDC9194CB FOREIGN KEY (trip_participant_id) REFERENCES trip_participant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE expense ADD payment_method VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE expense_trip_participant DROP CONSTRAINT FK_3DE58CDCF395DB7B');
        $this->addSql('ALTER TABLE expense_trip_participant DROP CONSTRAINT FK_3DE58CDCDC9194CB');
        $this->addSql('DROP TABLE expense_trip_participant');
        $this->addSql('ALTER TABLE expense DROP payment_method');
    }
}
