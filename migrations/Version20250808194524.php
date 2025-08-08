<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808194524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les catégories de dépenses par défaut.';
    }

    public function up(Schema $schema): void
    {
        //migration perso pour ajouter categories par défaut
        $this->addSql("INSERT INTO category (name, discr) VALUES 
            ('transport', 'expense_category'),
            ('restaurant', 'expense_category'),
            ('food', 'expense_category'),
            ('drinks', 'expense_category'),
            ('museum', 'expense_category'),
            ('activity', 'expense_category'),
            ('shopping', 'expense_category'),
            ('groceries', 'expense_category'),
            ('cleaning', 'expense_category'),
            ('housing', 'expense_category'),
            ('flight', 'expense_category'),
            ('coffee', 'expense_category'),
            ('withdrawal', 'expense_category'),
            ('health', 'expense_category'),
            ('gifts', 'expense_category');");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM category WHERE name IN (
            'transport', 'restaurant', 'food', 'drinks', 'museum', 'activity', 'shopping',
            'groceries', 'cleaning', 'housing', 'flight', 'coffee', 'withdrawal',
                                    'health',  'gifts'
        );");
    }
}
