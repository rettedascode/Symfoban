<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201103840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        
        // Create a default "System" user for existing tasks
        $this->addSql('INSERT INTO "user" (email, roles, password, name, created_at) VALUES (\'system@symfoban.local\', \'["ROLE_USER"]\', \'$2y$13$dummy\', \'System\', datetime(\'now\'))');
        
        // Get the system user ID (will be 1 if it's the first user)
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, title, description, position, created_at, updated_at, column_id FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, position INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, column_id INTEGER NOT NULL, created_by_id INTEGER NOT NULL, CONSTRAINT FK_527EDB25BE8E8ED5 FOREIGN KEY (column_id) REFERENCES kanban_column (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_527EDB25B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, title, description, position, created_at, updated_at, column_id, created_by_id) SELECT id, title, description, position, created_at, updated_at, column_id, 1 FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25BE8E8ED5 ON task (column_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25B03A8386 ON task (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "user"');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, title, description, position, created_at, updated_at, column_id FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, position INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, column_id INTEGER NOT NULL, CONSTRAINT FK_527EDB25BE8E8ED5 FOREIGN KEY (column_id) REFERENCES kanban_column (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, title, description, position, created_at, updated_at, column_id) SELECT id, title, description, position, created_at, updated_at, column_id FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB25BE8E8ED5 ON task (column_id)');
    }
}
