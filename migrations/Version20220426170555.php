<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426170555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_score ADD player_id INT NOT NULL, ADD tournament_id INT NOT NULL, DROP lastname, DROP firstname, DROP image');
        $this->addSql('ALTER TABLE player_score ADD CONSTRAINT FK_8DEB4C1799E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE player_score ADD CONSTRAINT FK_8DEB4C1733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('CREATE INDEX IDX_8DEB4C1799E6F5DF ON player_score (player_id)');
        $this->addSql('CREATE INDEX IDX_8DEB4C1733D1A3E7 ON player_score (tournament_id)');
        $this->addSql('ALTER TABLE tournament_match CHANGE result result VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_score DROP FOREIGN KEY FK_8DEB4C1799E6F5DF');
        $this->addSql('ALTER TABLE player_score DROP FOREIGN KEY FK_8DEB4C1733D1A3E7');
        $this->addSql('DROP INDEX IDX_8DEB4C1799E6F5DF ON player_score');
        $this->addSql('DROP INDEX IDX_8DEB4C1733D1A3E7 ON player_score');
        $this->addSql('ALTER TABLE player_score ADD lastname VARCHAR(255) NOT NULL, ADD firstname VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, DROP player_id, DROP tournament_id');
        $this->addSql('ALTER TABLE tournament_match CHANGE result result LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}
