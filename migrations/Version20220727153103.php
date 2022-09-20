<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220727153103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mes_mains (id INT AUTO_INCREMENT NOT NULL, id_card1_id INT NOT NULL, id_card2_id INT NOT NULL, count BIGINT DEFAULT NULL, win BIGINT DEFAULT NULL, INDEX IDX_643A198CBD932D54 (id_card1_id), INDEX IDX_643A198CAF2682BA (id_card2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mes_mains ADD CONSTRAINT FK_643A198CBD932D54 FOREIGN KEY (id_card1_id) REFERENCES cards (id)');
        $this->addSql('ALTER TABLE mes_mains ADD CONSTRAINT FK_643A198CAF2682BA FOREIGN KEY (id_card2_id) REFERENCES cards (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE mes_mains');
    }
}
