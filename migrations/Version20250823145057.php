<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250823145057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_file (id SERIAL NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_original_name VARCHAR(255) DEFAULT NULL, file_mime_type VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, file_dimensions TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN document_file.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN document_file.file_dimensions IS \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE document ADD file_id INT NOT NULL');
        $this->addSql('ALTER TABLE document DROP file');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7693CB796C FOREIGN KEY (file_id) REFERENCES document_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8698A7693CB796C ON document (file_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A7693CB796C');
        $this->addSql('DROP TABLE document_file');
        $this->addSql('DROP INDEX UNIQ_D8698A7693CB796C');
        $this->addSql('ALTER TABLE document ADD file VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE document DROP file_id');
    }
}
