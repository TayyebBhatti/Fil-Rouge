<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251009095006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9B80EC64E7927C74 ON utilisateur (email)');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EFB88E14F');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur (id)');
        $this->addSql('DROP INDEX uniq_user_event ON inscription');
        $this->addSql('ALTER TABLE inscription DROP created_at, DROP status');
        $this->addSql('CREATE INDEX IDX_5E90F6D6FB88E14F ON inscription (utilisateur_id)');
        $this->addSql('ALTER TABLE inscription RENAME INDEX fk_insc_event TO IDX_5E90F6D6FD02F13');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EFB88E14F');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id_utilisateur) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6FB88E14F');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6FD02F13');
        $this->addSql('DROP INDEX IDX_5E90F6D6FB88E14F ON inscription');
        $this->addSql('ALTER TABLE inscription ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD status VARCHAR(30) DEFAULT \'confirmed\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_event ON inscription (utilisateur_id, evenement_id)');
        $this->addSql('ALTER TABLE inscription RENAME INDEX idx_5e90f6d6fd02f13 TO fk_insc_event');
        $this->addSql('DROP INDEX UNIQ_9B80EC64E7927C74 ON Utilisateur');
    }
}
