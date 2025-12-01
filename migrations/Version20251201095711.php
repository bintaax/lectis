<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201095711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY `FK_24CC0DF29CCC1BA8`');
        $this->addSql('DROP INDEX UNIQ_24CC0DF29CCC1BA8 ON panier');
        $this->addSql('ALTER TABLE panier ADD utilisateur_id INT NOT NULL, DROP utilisateurs_id_id');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF2FB88E14F ON panier (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2FB88E14F');
        $this->addSql('DROP INDEX IDX_24CC0DF2FB88E14F ON panier');
        $this->addSql('ALTER TABLE panier ADD utilisateurs_id_id INT DEFAULT NULL, DROP utilisateur_id');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT `FK_24CC0DF29CCC1BA8` FOREIGN KEY (utilisateurs_id_id) REFERENCES utilisateurs (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF29CCC1BA8 ON panier (utilisateurs_id_id)');
    }
}
