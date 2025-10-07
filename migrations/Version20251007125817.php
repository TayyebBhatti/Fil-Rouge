<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251007125817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON categorie');
        $this->addSql('ALTER TABLE categorie ADD nom_cat VARCHAR(100) NOT NULL, DROP nom, CHANGE id id_categorie INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE categorie ADD PRIMARY KEY (id_categorie)');
        $this->addSql('ALTER TABLE evenement MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON evenement');
        $this->addSql('ALTER TABLE evenement ADD id_lieu INT NOT NULL, ADD id_categorie INT NOT NULL, ADD capacite_max INT NOT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE id id_event INT AUTO_INCREMENT NOT NULL, CHANGE capacité_max id_utilisateur INT NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_89D7EABD50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_89D7EABDA477615B FOREIGN KEY (id_lieu) REFERENCES Lieu (id_lieu)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_89D7EABDC9486A13 FOREIGN KEY (id_categorie) REFERENCES Categorie (id_categorie)');
        $this->addSql('CREATE INDEX IDX_89D7EABD50EAE44 ON evenement (id_utilisateur)');
        $this->addSql('CREATE INDEX IDX_89D7EABDA477615B ON evenement (id_lieu)');
        $this->addSql('CREATE INDEX IDX_89D7EABDC9486A13 ON evenement (id_categorie)');
        $this->addSql('ALTER TABLE evenement ADD PRIMARY KEY (id_event)');
        $this->addSql('ALTER TABLE lieu MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON lieu');
        $this->addSql('ALTER TABLE lieu CHANGE pays pays VARCHAR(100) NOT NULL, CHANGE id id_lieu INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE lieu ADD PRIMARY KEY (id_lieu)');
        $this->addSql('ALTER TABLE utilisateur MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur CHANGE id id_utilisateur INT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9B80EC64E7927C74 ON utilisateur (email)');
        $this->addSql('ALTER TABLE utilisateur ADD PRIMARY KEY (id_utilisateur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Categorie MODIFY id_categorie INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON Categorie');
        $this->addSql('ALTER TABLE Categorie ADD nom VARCHAR(255) NOT NULL, DROP nom_cat, CHANGE id_categorie id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE Categorie ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE Evenement MODIFY id_event INT NOT NULL');
        $this->addSql('ALTER TABLE Evenement DROP FOREIGN KEY FK_89D7EABD50EAE44');
        $this->addSql('ALTER TABLE Evenement DROP FOREIGN KEY FK_89D7EABDA477615B');
        $this->addSql('ALTER TABLE Evenement DROP FOREIGN KEY FK_89D7EABDC9486A13');
        $this->addSql('DROP INDEX IDX_89D7EABD50EAE44 ON Evenement');
        $this->addSql('DROP INDEX IDX_89D7EABDA477615B ON Evenement');
        $this->addSql('DROP INDEX IDX_89D7EABDC9486A13 ON Evenement');
        $this->addSql('DROP INDEX `PRIMARY` ON Evenement');
        $this->addSql('ALTER TABLE Evenement ADD capacité_max INT NOT NULL, DROP id_utilisateur, DROP id_lieu, DROP id_categorie, DROP capacite_max, CHANGE description description VARCHAR(255) NOT NULL, CHANGE image image VARCHAR(255) NOT NULL, CHANGE id_event id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE Evenement ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE Lieu MODIFY id_lieu INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON Lieu');
        $this->addSql('ALTER TABLE Lieu CHANGE pays pays VARCHAR(255) NOT NULL, CHANGE id_lieu id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE Lieu ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE Utilisateur MODIFY id_utilisateur INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_9B80EC64E7927C74 ON Utilisateur');
        $this->addSql('DROP INDEX `PRIMARY` ON Utilisateur');
        $this->addSql('ALTER TABLE Utilisateur CHANGE id_utilisateur id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE Utilisateur ADD PRIMARY KEY (id)');
    }
}
