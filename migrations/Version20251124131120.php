<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124131120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, total DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, adresse_livraison VARCHAR(255) NOT NULL, paiement VARCHAR(255) NOT NULL, utilisateurs_id INT DEFAULT NULL, ligne_commande_id INT DEFAULT NULL, INDEX IDX_35D4282C1E969C5 (utilisateurs_id), INDEX IDX_35D4282CE10FEE63 (ligne_commande_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_commande (id INT AUTO_INCREMENT NOT NULL, total DOUBLE PRECISION NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_panier (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_panier_panier (ligne_panier_id INT NOT NULL, panier_id INT NOT NULL, INDEX IDX_9A56EE8D38989DF4 (ligne_panier_id), INDEX IDX_9A56EE8DF77D927C (panier_id), PRIMARY KEY (ligne_panier_id, panier_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, utilisateurs_id_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_24CC0DF29CCC1BA8 (utilisateurs_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C1E969C5 FOREIGN KEY (utilisateurs_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282CE10FEE63 FOREIGN KEY (ligne_commande_id) REFERENCES ligne_commande (id)');
        $this->addSql('ALTER TABLE ligne_panier_panier ADD CONSTRAINT FK_9A56EE8D38989DF4 FOREIGN KEY (ligne_panier_id) REFERENCES ligne_panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ligne_panier_panier ADD CONSTRAINT FK_9A56EE8DF77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF29CCC1BA8 FOREIGN KEY (utilisateurs_id_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE livres ADD ligne_panier_id INT DEFAULT NULL, ADD ligne_commande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livres ADD CONSTRAINT FK_927187A438989DF4 FOREIGN KEY (ligne_panier_id) REFERENCES ligne_panier (id)');
        $this->addSql('ALTER TABLE livres ADD CONSTRAINT FK_927187A4E10FEE63 FOREIGN KEY (ligne_commande_id) REFERENCES ligne_commande (id)');
        $this->addSql('CREATE INDEX IDX_927187A438989DF4 ON livres (ligne_panier_id)');
        $this->addSql('CREATE INDEX IDX_927187A4E10FEE63 ON livres (ligne_commande_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282C1E969C5');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282CE10FEE63');
        $this->addSql('ALTER TABLE ligne_panier_panier DROP FOREIGN KEY FK_9A56EE8D38989DF4');
        $this->addSql('ALTER TABLE ligne_panier_panier DROP FOREIGN KEY FK_9A56EE8DF77D927C');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF29CCC1BA8');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE ligne_commande');
        $this->addSql('DROP TABLE ligne_panier');
        $this->addSql('DROP TABLE ligne_panier_panier');
        $this->addSql('DROP TABLE panier');
        $this->addSql('ALTER TABLE livres DROP FOREIGN KEY FK_927187A438989DF4');
        $this->addSql('ALTER TABLE livres DROP FOREIGN KEY FK_927187A4E10FEE63');
        $this->addSql('DROP INDEX IDX_927187A438989DF4 ON livres');
        $this->addSql('DROP INDEX IDX_927187A4E10FEE63 ON livres');
        $this->addSql('ALTER TABLE livres DROP ligne_panier_id, DROP ligne_commande_id');
    }
}
