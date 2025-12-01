<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201101635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE board (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE kanban_column (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, position INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, board_id INTEGER NOT NULL, CONSTRAINT FK_157CF286E7EC5785 FOREIGN KEY (board_id) REFERENCES board (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_157CF286E7EC5785 ON kanban_column (board_id)');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, position INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, column_id INTEGER NOT NULL, CONSTRAINT FK_527EDB25BE8E8ED5 FOREIGN KEY (column_id) REFERENCES kanban_column (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_527EDB25BE8E8ED5 ON task (column_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE board');
        $this->addSql('DROP TABLE kanban_column');
        $this->addSql('DROP TABLE task');
    }
}
