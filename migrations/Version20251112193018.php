<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112193018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, texte LONGTEXT NOT NULL, semaine VARCHAR(20) NOT NULL, scenario VARCHAR(20) NOT NULL, consequence_type VARCHAR(50) DEFAULT NULL, semaine_applicable INT DEFAULT NULL, type VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, delta_budget NUMERIC(10, 2) NOT NULL, delta_bien_etre INT NOT NULL, delta_bonheur INT NOT NULL, INDEX IDX_5A8600B0FD02F13 (evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, date DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', type VARCHAR(50) DEFAULT NULL, budget_initial NUMERIC(10, 2) NOT NULL, budget_courant NUMERIC(10, 2) NOT NULL, bien_etre_initial INT NOT NULL, bonheur_courant INT NOT NULL, semaine_courante INT NOT NULL, nb_semaines INT NOT NULL, etat VARCHAR(20) NOT NULL, INDEX IDX_59B1F3DFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, score INT DEFAULT NULL, field VARCHAR(255) DEFAULT NULL, INDEX IDX_E6D6B297FB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semaine (id INT AUTO_INCREMENT NOT NULL, partie_id INT NOT NULL, evenement_courant_id INT DEFAULT NULL, numero INT NOT NULL, budget_restant NUMERIC(10, 2) NOT NULL, bien_etre INT NOT NULL, bonheur_enfants INT NOT NULL, INDEX IDX_7B4D8BEAE075F7A4 (partie_id), INDEX IDX_7B4D8BEAAC4A45F2 (evenement_courant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tableau_de_bord (id INT AUTO_INCREMENT NOT NULL, profil_id INT DEFAULT NULL, classement JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', meilleur_score INT DEFAULT NULL, enregistre_score TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_38B30D8A275ED078 (profil_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `option` ADD CONSTRAINT FK_5A8600B0FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE semaine ADD CONSTRAINT FK_7B4D8BEAE075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
        $this->addSql('ALTER TABLE semaine ADD CONSTRAINT FK_7B4D8BEAAC4A45F2 FOREIGN KEY (evenement_courant_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE tableau_de_bord ADD CONSTRAINT FK_38B30D8A275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `option` DROP FOREIGN KEY FK_5A8600B0FD02F13');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3DFB88E14F');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297FB88E14F');
        $this->addSql('ALTER TABLE semaine DROP FOREIGN KEY FK_7B4D8BEAE075F7A4');
        $this->addSql('ALTER TABLE semaine DROP FOREIGN KEY FK_7B4D8BEAAC4A45F2');
        $this->addSql('ALTER TABLE tableau_de_bord DROP FOREIGN KEY FK_38B30D8A275ED078');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE partie');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE semaine');
        $this->addSql('DROP TABLE tableau_de_bord');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
