<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508084727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hands (id INT AUTO_INCREMENT NOT NULL, tournament_id INT NOT NULL, card1_id INT NOT NULL, card2_id INT NOT NULL, turn_id INT DEFAULT NULL, river_id INT DEFAULT NULL, hand_id INT NOT NULL, flop LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', players LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', win TINYINT(1) NOT NULL, INDEX IDX_662E33F833D1A3E7 (tournament_id), INDEX IDX_662E33F8ED48C1C1 (card1_id), INDEX IDX_662E33F8FFFD6E2F (card2_id), INDEX IDX_662E33F81F4F9889 (turn_id), INDEX IDX_662E33F841E62266 (river_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(255) NOT NULL, win_tour INT NOT NULL, win_hand INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hands ADD CONSTRAINT FK_662E33F833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE hands ADD CONSTRAINT FK_662E33F8ED48C1C1 FOREIGN KEY (card1_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE hands ADD CONSTRAINT FK_662E33F8FFFD6E2F FOREIGN KEY (card2_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE hands ADD CONSTRAINT FK_662E33F81F4F9889 FOREIGN KEY (turn_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE hands ADD CONSTRAINT FK_662E33F841E62266 FOREIGN KEY (river_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE cards CHANGE value value VARCHAR(2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE hands');
        $this->addSql('DROP TABLE players');
        $this->addSql('ALTER TABLE cards CHANGE value value VARCHAR(1) NOT NULL');
    }
}
