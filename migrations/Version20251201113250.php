<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201113250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Check if tables exist before creating them (they may have been created in previous migrations)
        $tableExists = $this->connection->executeQuery(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='activity_log'"
        )->fetchOne();
        
        if (!$tableExists) {
            $this->addSql('CREATE TABLE activity_log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "action" VARCHAR(50) NOT NULL, entity_type VARCHAR(255) DEFAULT NULL, entity_id INTEGER DEFAULT NULL, description CLOB DEFAULT NULL, created_at DATETIME NOT NULL, user_id INTEGER DEFAULT NULL, CONSTRAINT FK_FD06F647A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
            $this->addSql('CREATE INDEX IDX_FD06F647A76ED395 ON activity_log (user_id)');
        }
        
        // Note: board_template, tag, and task_tag tables were already created in previous migrations
        // Only create activity_log table if it doesn't exist
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // Only drop activity_log table (other tables are handled by their respective migrations)
        $this->addSql('DROP TABLE IF EXISTS activity_log');
    }
}
