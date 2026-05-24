<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260524112211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clients (id_client INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_C82E74E7927C74 (email), PRIMARY KEY (id_client)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, category VARCHAR(50) NOT NULL, category_color VARCHAR(20) DEFAULT \'#d9822b\' NOT NULL, players VARCHAR(50) NOT NULL, duration VARCHAR(50) NOT NULL, image_path VARCHAR(255) NOT NULL, rules LONGTEXT DEFAULT \'Not available \' NOT NULL, exemplaires_total INT DEFAULT 1 NOT NULL, exemplaires_disponibles INT DEFAULT 1 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE livres (id_livre INT AUTO_INCREMENT NOT NULL, titre VARCHAR(200) NOT NULL, auteur VARCHAR(100) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, exemplaires_total INT DEFAULT 1 NOT NULL, exemplaires_disponibles INT DEFAULT 1 NOT NULL, PRIMARY KEY (id_livre)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_categories (id INT AUTO_INCREMENT NOT NULL, section VARCHAR(20) NOT NULL, title VARCHAR(100) NOT NULL, image_url VARCHAR(255) NOT NULL, link VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu_items (id INT AUTO_INCREMENT NOT NULL, category_slug VARCHAR(50) NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, price VARCHAR(50) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, is_popular TINYINT DEFAULT 0 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation_jeux (id_RJ INT AUTO_INCREMENT NOT NULL, id_reservation INT DEFAULT NULL, id_game INT DEFAULT NULL, INDEX IDX_216C78655ADA84A2 (id_reservation), INDEX IDX_216C7865A80B2D8E (id_game), PRIMARY KEY (id_RJ)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation_livres (id_RL INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) DEFAULT NULL, date_expiration DATE DEFAULT NULL, id_reservation INT DEFAULT NULL, id_livre INT DEFAULT NULL, INDEX IDX_DA80CE3F5ADA84A2 (id_reservation), INDEX IDX_DA80CE3F42E60EA9 (id_livre), PRIMARY KEY (id_RL)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservations (id_reservation INT AUTO_INCREMENT NOT NULL, date_reservation DATE NOT NULL, heure_reservation TIME NOT NULL, nb_personnes INT NOT NULL, allergies LONGTEXT DEFAULT NULL, commentaires LONGTEXT DEFAULT NULL, statut VARCHAR(20) DEFAULT \'confirmee\' NOT NULL, id_client INT DEFAULT NULL, id_table INT DEFAULT NULL, INDEX IDX_4DA239E173B1B8 (id_client), INDEX IDX_4DA23918ACCE76 (id_table), PRIMARY KEY (id_reservation)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tables (id_table INT AUTO_INCREMENT NOT NULL, numero INT NOT NULL, places INT NOT NULL, id_zone INT DEFAULT NULL, INDEX IDX_844702212BCBDC05 (id_zone), PRIMARY KEY (id_table)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE zones (id_zone INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY (id_zone)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reservation_jeux ADD CONSTRAINT FK_216C78655ADA84A2 FOREIGN KEY (id_reservation) REFERENCES reservations (id_reservation)');
        $this->addSql('ALTER TABLE reservation_jeux ADD CONSTRAINT FK_216C7865A80B2D8E FOREIGN KEY (id_game) REFERENCES game (id)');
        $this->addSql('ALTER TABLE reservation_livres ADD CONSTRAINT FK_DA80CE3F5ADA84A2 FOREIGN KEY (id_reservation) REFERENCES reservations (id_reservation)');
        $this->addSql('ALTER TABLE reservation_livres ADD CONSTRAINT FK_DA80CE3F42E60EA9 FOREIGN KEY (id_livre) REFERENCES livres (id_livre)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239E173B1B8 FOREIGN KEY (id_client) REFERENCES clients (id_client)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA23918ACCE76 FOREIGN KEY (id_table) REFERENCES tables (id_table)');
        $this->addSql('ALTER TABLE tables ADD CONSTRAINT FK_844702212BCBDC05 FOREIGN KEY (id_zone) REFERENCES zones (id_zone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_jeux DROP FOREIGN KEY FK_216C78655ADA84A2');
        $this->addSql('ALTER TABLE reservation_jeux DROP FOREIGN KEY FK_216C7865A80B2D8E');
        $this->addSql('ALTER TABLE reservation_livres DROP FOREIGN KEY FK_DA80CE3F5ADA84A2');
        $this->addSql('ALTER TABLE reservation_livres DROP FOREIGN KEY FK_DA80CE3F42E60EA9');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239E173B1B8');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA23918ACCE76');
        $this->addSql('ALTER TABLE tables DROP FOREIGN KEY FK_844702212BCBDC05');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE livres');
        $this->addSql('DROP TABLE menu_categories');
        $this->addSql('DROP TABLE menu_items');
        $this->addSql('DROP TABLE reservation_jeux');
        $this->addSql('DROP TABLE reservation_livres');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE tables');
        $this->addSql('DROP TABLE zones');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
