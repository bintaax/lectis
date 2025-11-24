<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124110929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livres ADD photo VARCHAR(255) NOT NULL, CHANGE date_publication date_publication DATETIME NOT NULL, CHANGE age_autorise age_autorise SMALLINT NOT NULL, CHANGE genre_id genre_id INT DEFAULT NULL, CHANGE nombre_pages nb_pages INT NOT NULL, CHANGE is_best_seller disponibilite TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE livres RENAME INDEX idx_927187a4c2428192 TO IDX_927187A44296D31F');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livres DROP photo, CHANGE date_publication date_publication DATE NOT NULL, CHANGE age_autorise age_autorise SMALLINT DEFAULT NULL, CHANGE genre_id genre_id INT NOT NULL, CHANGE nb_pages nombre_pages INT NOT NULL, CHANGE disponibilite is_best_seller TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE livres RENAME INDEX idx_927187a44296d31f TO IDX_927187A4C2428192');
    }
}
