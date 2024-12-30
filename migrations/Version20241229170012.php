<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229170012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip_user (trip_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(trip_id, user_id))');
        $this->addSql('CREATE INDEX IDX_A6AB4522A5BC2E0E ON trip_user (trip_id)');
        $this->addSql('CREATE INDEX IDX_A6AB4522A76ED395 ON trip_user (user_id)');
        $this->addSql('CREATE TABLE trip_invite (id SERIAL NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, trip_id INT NOT NULL, message TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_28ED4B7DF624B39D ON trip_invite (sender_id)');
        $this->addSql('CREATE INDEX IDX_28ED4B7DCD53EDB6 ON trip_invite (receiver_id)');
        $this->addSql('CREATE INDEX IDX_28ED4B7DA5BC2E0E ON trip_invite (trip_id)');
        $this->addSql('COMMENT ON COLUMN trip_invite.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE trip_user ADD CONSTRAINT FK_A6AB4522A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_user ADD CONSTRAINT FK_A6AB4522A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_invite ADD CONSTRAINT FK_28ED4B7DF624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_invite ADD CONSTRAINT FK_28ED4B7DCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_invite ADD CONSTRAINT FK_28ED4B7DA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trip_user DROP CONSTRAINT FK_A6AB4522A5BC2E0E');
        $this->addSql('ALTER TABLE trip_user DROP CONSTRAINT FK_A6AB4522A76ED395');
        $this->addSql('ALTER TABLE trip_invite DROP CONSTRAINT FK_28ED4B7DF624B39D');
        $this->addSql('ALTER TABLE trip_invite DROP CONSTRAINT FK_28ED4B7DCD53EDB6');
        $this->addSql('ALTER TABLE trip_invite DROP CONSTRAINT FK_28ED4B7DA5BC2E0E');
        $this->addSql('DROP TABLE trip_user');
        $this->addSql('DROP TABLE trip_invite');
    }
}
