<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229203828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip_invite DROP CONSTRAINT fk_28ed4b7dcd53edb6');
        $this->addSql('DROP INDEX idx_28ed4b7dcd53edb6');
        $this->addSql('ALTER TABLE trip_invite RENAME COLUMN receiver_id TO recipient_id');
        $this->addSql('ALTER TABLE trip_invite ADD CONSTRAINT FK_28ED4B7DE92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_28ED4B7DE92F8F78 ON trip_invite (recipient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trip_invite DROP CONSTRAINT FK_28ED4B7DE92F8F78');
        $this->addSql('DROP INDEX IDX_28ED4B7DE92F8F78');
        $this->addSql('ALTER TABLE trip_invite RENAME COLUMN recipient_id TO receiver_id');
        $this->addSql('ALTER TABLE trip_invite ADD CONSTRAINT fk_28ed4b7dcd53edb6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_28ed4b7dcd53edb6 ON trip_invite (receiver_id)');
    }
}
