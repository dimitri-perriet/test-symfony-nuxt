<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250925212536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        // Ajout de la colonne nullable d'abord
        $this->addSql('ALTER TABLE livre ADD proprietaire_id INT DEFAULT NULL');
        
        // Assigner tous les livres existants au premier utilisateur (admin)
        $this->addSql('UPDATE livre SET proprietaire_id = (SELECT id FROM "user" ORDER BY id LIMIT 1) WHERE proprietaire_id IS NULL');
        
        // Rendre la colonne NOT NULL
        $this->addSql('ALTER TABLE livre ALTER COLUMN proprietaire_id SET NOT NULL');
        
        // Ajouter les contraintes
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F9976C50E4A FOREIGN KEY (proprietaire_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AC634F9976C50E4A ON livre (proprietaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE livre DROP CONSTRAINT FK_AC634F9976C50E4A');
        $this->addSql('DROP INDEX IDX_AC634F9976C50E4A');
        $this->addSql('ALTER TABLE livre DROP proprietaire_id');
    }
}
