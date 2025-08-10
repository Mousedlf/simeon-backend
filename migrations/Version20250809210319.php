<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250809210319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajouter catgégories pour les activités';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO category (name, discr) VALUES 
            ('culture & history', 'activity_category'),
            ('nature & outdoors', 'activity_category'),
            ('adventure & sports', 'activity_category'),
            ('relaxation & wellness', 'activity_category'),
            ('food & drinks', 'activity_category'),
            ('entertainment', 'activity_category'),
            ('shopping', 'activity_category'),
            ('transport', 'activity_category'),
            ('groceries', 'activity_category'),
            ('housing', 'activity_category'),
            ('other', 'activity_category')");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM category WHERE name IN (
            'culture & history',
            'nature & outdoors',
            'adventure & sports',
            'relaxation & wellness',
            'food & drinks',
            'entertainment',
            'shopping',
            'transport',
            'groceries',
            'housing',
            'other'
        ) AND discr = 'activity_category';");
    }
}
