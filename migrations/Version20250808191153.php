<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808191153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, of_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C15A1B2224 ON category (of_user_id)');
        $this->addSql('CREATE TABLE document (id SERIAL NOT NULL, trip_activity_id INT DEFAULT NULL, of_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, added_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, file VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D8698A7675E1C71C ON document (trip_activity_id)');
        $this->addSql('CREATE INDEX IDX_D8698A765A1B2224 ON document (of_user_id)');
        $this->addSql('COMMENT ON COLUMN document.added_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE trip_activity (id SERIAL NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4253A4A12469DE2 ON trip_activity (category_id)');
        $this->addSql('CREATE TABLE trip_activity_day_of_trip (trip_activity_id INT NOT NULL, day_of_trip_id INT NOT NULL, PRIMARY KEY(trip_activity_id, day_of_trip_id))');
        $this->addSql('CREATE INDEX IDX_911F6AE575E1C71C ON trip_activity_day_of_trip (trip_activity_id)');
        $this->addSql('CREATE INDEX IDX_911F6AE57E495F60 ON trip_activity_day_of_trip (day_of_trip_id)');

        $this->addSql('INSERT INTO category (name, discr) VALUES (\'default\', \'expense_category\');'); // important ! sinon pb lors migration

        // ajout direct de mes catégories (pour ne pas avoir a les créer en prod


        $this->addSql('ALTER TABLE expense ADD category_id INT DEFAULT NULL;');
        $this->addSql('UPDATE expense SET category_id = 1 WHERE category_id IS NULL;');
        $this->addSql('ALTER TABLE expense ALTER COLUMN category_id SET NOT NULL;');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
        $this->addSql('CREATE INDEX IDX_2D3A8DA612469DE2 ON expense (category_id);');

        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C15A1B2224 FOREIGN KEY (of_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7675E1C71C FOREIGN KEY (trip_activity_id) REFERENCES trip_activity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A765A1B2224 FOREIGN KEY (of_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_activity ADD CONSTRAINT FK_4253A4A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_activity_day_of_trip ADD CONSTRAINT FK_911F6AE575E1C71C FOREIGN KEY (trip_activity_id) REFERENCES trip_activity (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_activity_day_of_trip ADD CONSTRAINT FK_911F6AE57E495F60 FOREIGN KEY (day_of_trip_id) REFERENCES day_of_trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT FK_2D3A8DA612469DE2');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C15A1B2224');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A7675E1C71C');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A765A1B2224');
        $this->addSql('ALTER TABLE trip_activity DROP CONSTRAINT FK_4253A4A12469DE2');
        $this->addSql('ALTER TABLE trip_activity_day_of_trip DROP CONSTRAINT FK_911F6AE575E1C71C');
        $this->addSql('ALTER TABLE trip_activity_day_of_trip DROP CONSTRAINT FK_911F6AE57E495F60');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE trip_activity');
        $this->addSql('DROP TABLE trip_activity_day_of_trip');
        $this->addSql('DROP INDEX IDX_2D3A8DA612469DE2');
        $this->addSql('ALTER TABLE expense DROP category_id');
    }
}
