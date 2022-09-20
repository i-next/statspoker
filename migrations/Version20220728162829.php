<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220728162829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main ADD id_player3_id INT DEFAULT NULL, ADD id_player4_id INT DEFAULT NULL, ADD id_player5_id INT DEFAULT NULL, ADD id_player6_id INT DEFAULT NULL, ADD id_player7_id INT DEFAULT NULL, ADD id_player8_id INT DEFAULT NULL, ADD id_player9_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64CF1A4C7F FOREIGN KEY (id_player3_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD6452CD74C6 FOREIGN KEY (id_player4_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64EA7113A3 FOREIGN KEY (id_player5_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64F8C4BC4D FOREIGN KEY (id_player6_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD644078DB28 FOREIGN KEY (id_player7_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64181BCB7E FOREIGN KEY (id_player8_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE main ADD CONSTRAINT FK_BF28CD64A0A7AC1B FOREIGN KEY (id_player9_id) REFERENCES joueur (id)');
        $this->addSql('CREATE INDEX IDX_BF28CD64CF1A4C7F ON main (id_player3_id)');
        $this->addSql('CREATE INDEX IDX_BF28CD6452CD74C6 ON main (id_player4_id)');
        $this->addSql('CREATE INDEX IDX_BF28CD64EA7113A3 ON main (id_player5_id)');
        $this->addSql('CREATE INDEX IDX_BF28CD64F8C4BC4D ON main (id_player6_id)');
        $this->addSql('CREATE INDEX IDX_BF28CD644078DB28 ON main (id_player7_id)');
        $this->addSql('CREATE INDEX IDX_BF28CD64181BCB7E ON main (id_player8_id)');
        $this->addSql('CREATE INDEX IDX_BF28CD64A0A7AC1B ON main (id_player9_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD64CF1A4C7F');
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD6452CD74C6');
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD64EA7113A3');
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD64F8C4BC4D');
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD644078DB28');
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD64181BCB7E');
        $this->addSql('ALTER TABLE main DROP FOREIGN KEY FK_BF28CD64A0A7AC1B');
        $this->addSql('DROP INDEX IDX_BF28CD64CF1A4C7F ON main');
        $this->addSql('DROP INDEX IDX_BF28CD6452CD74C6 ON main');
        $this->addSql('DROP INDEX IDX_BF28CD64EA7113A3 ON main');
        $this->addSql('DROP INDEX IDX_BF28CD64F8C4BC4D ON main');
        $this->addSql('DROP INDEX IDX_BF28CD644078DB28 ON main');
        $this->addSql('DROP INDEX IDX_BF28CD64181BCB7E ON main');
        $this->addSql('DROP INDEX IDX_BF28CD64A0A7AC1B ON main');
        $this->addSql('ALTER TABLE main DROP id_player3_id, DROP id_player4_id, DROP id_player5_id, DROP id_player6_id, DROP id_player7_id, DROP id_player8_id, DROP id_player9_id');
    }
}
