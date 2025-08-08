<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808203819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, symbol VARCHAR(255) NOT NULL, exchange_rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE expense ADD currency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE expense ADD amount_local_currency INT NOT NULL');
        $this->addSql('ALTER TABLE expense ADD amount_euro INT DEFAULT NULL');
        $this->addSql('ALTER TABLE expense ADD exchange_rate DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE expense DROP sum');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA638248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2D3A8DA638248176 ON expense (currency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE expense DROP CONSTRAINT FK_2D3A8DA638248176');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP INDEX IDX_2D3A8DA638248176');
        $this->addSql('ALTER TABLE expense ADD sum DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE expense DROP currency_id');
        $this->addSql('ALTER TABLE expense DROP amount_local_currency');
        $this->addSql('ALTER TABLE expense DROP amount_euro');
        $this->addSql('ALTER TABLE expense DROP exchange_rate');
    }
}
