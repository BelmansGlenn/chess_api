<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426081300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_score (id INT AUTO_INCREMENT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, victories INT NOT NULL, defeats INT NOT NULL, ties INT NOT NULL, score DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_match (id INT AUTO_INCREMENT NOT NULL, white_id INT NOT NULL, black_id INT NOT NULL, tournament_id INT NOT NULL, round INT NOT NULL, result VARCHAR(255) NOT NULL, INDEX IDX_BB0D551CCDBF46EC (white_id), INDEX IDX_BB0D551CD3E7E37C (black_id), INDEX IDX_BB0D551C33D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tournament_match ADD CONSTRAINT FK_BB0D551CCDBF46EC FOREIGN KEY (white_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE tournament_match ADD CONSTRAINT FK_BB0D551CD3E7E37C FOREIGN KEY (black_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE tournament_match ADD CONSTRAINT FK_BB0D551C33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE player_score');
        $this->addSql('DROP TABLE tournament_match');
    }
}
