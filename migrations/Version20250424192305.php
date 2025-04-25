<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424192305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
{
    // 1. colonne author_id nullable + FK
    $this->addSql('ALTER TABLE task ADD author_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_TASK_AUTHOR FOREIGN KEY (author_id) REFERENCES user (id)');
    $this->addSql('CREATE INDEX IDX_TASK_AUTHOR ON task (author_id)');

    // 2. créer / récupérer l’utilisateur "anonyme"
    $anonId = $this->connection->fetchOne("SELECT id FROM user WHERE username = 'anonyme'");
    if (!$anonId) {
        $this->addSql(
            "INSERT INTO user (username, password, email, roles) 
             VALUES ('anonyme', '', 'anonyme@example.com', '[]')"
        );
        $anonId = $this->connection->lastInsertId();
    }

    // 3. rattacher les anciennes tâches
    $this->addSql('UPDATE task SET author_id = :anon WHERE author_id IS NULL', ['anon' => $anonId]);

    // 4. rendre NOT NULL (syntaxe MySQL)
    if ($this->connection->getDatabasePlatform()->getName() === 'mysql') {
        $this->addSql('ALTER TABLE task MODIFY author_id INT NOT NULL');
    } else {                    // PostgreSQL, SQLite, etc.
        $this->addSql('ALTER TABLE task ALTER COLUMN author_id SET NOT NULL');
    }
}



public function down(Schema $schema): void
{
    $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_TASK_AUTHOR');
    $this->addSql('DROP INDEX IDX_TASK_AUTHOR ON task');
    $this->addSql('ALTER TABLE task DROP author_id');
}

}
