<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220427080143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_score DROP FOREIGN KEY FK_8DEB4C1733D1A3E7');
        $this->addSql('ALTER TABLE player_score DROP FOREIGN KEY FK_8DEB4C1799E6F5DF');
        $this->addSql('DROP INDEX IDX_8DEB4C1733D1A3E7 ON player_score');
        $this->addSql('DROP INDEX IDX_8DEB4C1799E6F5DF ON player_score');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_score ADD CONSTRAINT FK_8DEB4C1733D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE player_score ADD CONSTRAINT FK_8DEB4C1799E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('CREATE INDEX IDX_8DEB4C1733D1A3E7 ON player_score (tournament_id)');
        $this->addSql('CREATE INDEX IDX_8DEB4C1799E6F5DF ON player_score (player_id)');
    }
}
