<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220531123153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
       /* $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(50) NOT NULL, win INT DEFAULT NULL, loose INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('INSERT INTO result ("value") VALUES ("hauteur")');
        $this->addSql('INSERT INTO result ("value") VALUES ("paire")');
        $this->addSql('INSERT INTO result ("value") VALUES ("double paire")');
        $this->addSql('INSERT INTO result ("value") VALUES ("brelan")');
        $this->addSql('INSERT INTO result ("value") VALUES ("suite")');
        $this->addSql('INSERT INTO result ("value") VALUES ("couleur")');
        $this->addSql('INSERT INTO result ("value") VALUES ("full")');
        $this->addSql('INSERT INTO result ("value") VALUES ("carré")');*/
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE result');
    }
}
