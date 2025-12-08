<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251208091525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // 1. Ajouter la colonne en nullable
    $this->addSql('ALTER TABLE commandes ADD created_at DATETIME DEFAULT NULL');

    // 2. Remplir une date valide pour les anciennes commandes
    $this->addSql('UPDATE commandes SET created_at = NOW() WHERE created_at IS NULL');

    // 3. Rendre la colonne NOT NULL seulement après
    $this->addSql('ALTER TABLE commandes MODIFY created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes DROP created_at');
    }
}
