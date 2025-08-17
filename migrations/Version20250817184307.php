<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250817184307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip_activity ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip_activity ADD CONSTRAINT FK_4253A4A3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4253A4A3DA5256D ON trip_activity (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trip_activity DROP CONSTRAINT FK_4253A4A3DA5256D');
        $this->addSql('DROP INDEX UNIQ_4253A4A3DA5256D');
        $this->addSql('ALTER TABLE trip_activity DROP image_id');
    }
}
