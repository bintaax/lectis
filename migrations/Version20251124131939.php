<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124131939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_panier_panier DROP FOREIGN KEY `FK_9A56EE8D38989DF4`');
        $this->addSql('ALTER TABLE ligne_panier_panier DROP FOREIGN KEY `FK_9A56EE8DF77D927C`');
        $this->addSql('DROP TABLE ligne_panier_panier');
        $this->addSql('ALTER TABLE ligne_panier ADD panier_id INT NOT NULL, ADD livre_id INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_panier ADD CONSTRAINT FK_21691B4F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE ligne_panier ADD CONSTRAINT FK_21691B437D925CB FOREIGN KEY (livre_id) REFERENCES livres (id)');
        $this->addSql('CREATE INDEX IDX_21691B4F77D927C ON ligne_panier (panier_id)');
        $this->addSql('CREATE INDEX IDX_21691B437D925CB ON ligne_panier (livre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ligne_panier_panier (ligne_panier_id INT NOT NULL, panier_id INT NOT NULL, INDEX IDX_9A56EE8D38989DF4 (ligne_panier_id), INDEX IDX_9A56EE8DF77D927C (panier_id), PRIMARY KEY (ligne_panier_id, panier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ligne_panier_panier ADD CONSTRAINT `FK_9A56EE8D38989DF4` FOREIGN KEY (ligne_panier_id) REFERENCES ligne_panier (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ligne_panier_panier ADD CONSTRAINT `FK_9A56EE8DF77D927C` FOREIGN KEY (panier_id) REFERENCES panier (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ligne_panier DROP FOREIGN KEY FK_21691B4F77D927C');
        $this->addSql('ALTER TABLE ligne_panier DROP FOREIGN KEY FK_21691B437D925CB');
        $this->addSql('DROP INDEX IDX_21691B4F77D927C ON ligne_panier');
        $this->addSql('DROP INDEX IDX_21691B437D925CB ON ligne_panier');
        $this->addSql('ALTER TABLE ligne_panier DROP panier_id, DROP livre_id');
    }
}
