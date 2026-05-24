<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260524_MenuItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create menu_items table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS menu_items (
            id INT AUTO_INCREMENT NOT NULL,
            category_slug VARCHAR(50) NOT NULL,
            name VARCHAR(150) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            price VARCHAR(50) NOT NULL,
            image_url VARCHAR(500) DEFAULT NULL,
            is_popular TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS menu_items');
    }
}
