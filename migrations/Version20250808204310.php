<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808204310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les devises par défaut (EUR, USD, etc.).';
    }

    public function up(Schema $schema): void
    {
        // migration perso pour ajouter qq monnaies par défaut.
        $this->addSql("INSERT INTO currency (name, code, symbol, exchange_rate) VALUES
            ('Euro', 'EUR', '€', 1.0),
            ('Dollar américain', 'USD', '$', 0.0),
            ('Livre Sterling', 'GBP', '£', 0.0),
            ('Yen japonais', 'JPY', '¥', 0.0),
            ('Dollar canadien', 'CAD', '$', 0.0),
            ('Franc suisse', 'CHF', 'CHF', 0.0),
            ('Dollar australien', 'AUD', '$', 0.0),
            ('Yuan chinois', 'CNY', '¥', 0.0);"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM currency WHERE code IN ('EUR', 'USD', 'GBP', 'JPY', 'CAD', 'CHF', 'AUD', 'CNY');");
    }
}
