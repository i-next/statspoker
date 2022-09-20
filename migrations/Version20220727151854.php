<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220727151854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(255) NOT NULL, hand_win BIGINT DEFAULT NULL, tour_win BIGINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main (id INT AUTO_INCREMENT NOT NULL, id_tournoi_id INT NOT NULL, id_card1_id INT NOT NULL, id_card2_id INT NOT NULL, id_flop1_id INT DEFAULT NULL, id_flop2_id INT DEFAULT NULL, id_flop3_id INT DEFAULT NULL, id_turn_id INT DEFAULT NULL, id_river_id INT DEFAULT NULL, id_player1_id INT NOT NULL, id_player2_id INT NOT NULL, INDEX IDX_BF28CD64538DF7DD (id_tournoi_id), INDEX IDX_BF28CD64BD932D54 (id_card1_id), INDEX IDX_BF28CD64AF2682BA (id_card2_id), INDEX IDX_BF28CD64BE8C62F7 (id_flop1_id), INDEX IDX_BF28CD64AC39CD19 (id_flop2_id), INDEX IDX_BF28CD641485AA7C (id_flop3_id), INDEX IDX_BF28CD64C1D231F9 (id_turn_id), INDEX IDX_BF28CD64113DCEF3 (id_river_id), INDEX IDX_BF28CD64651384F4 (id_player1_id), INDEX IDX_BF28CD6477A62B1A (id_player2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64538DF7DD FOREIGN KEY (id_tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64BD932D54 FOREIGN KEY (id_card1_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64AF2682BA FOREIGN KEY (id_card2_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64BE8C62F7 FOREIGN KEY (id_flop1_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64AC39CD19 FOREIGN KEY (id_flop2_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD641485AA7C FOREIGN KEY (id_flop3_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64C1D231F9 FOREIGN KEY (id_turn_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64113DCEF3 FOREIGN KEY (id_river_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64651384F4 FOREIGN KEY (id_player1_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD6477A62B1A FOREIGN KEY (id_player2_id) REFERENCES joueur (id)');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD64651384F4');
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD6477A62B1A');
        $this->addSql('DROP TABLE joueur');
        $this->addSql('DROP TABLE main');

    }
}
