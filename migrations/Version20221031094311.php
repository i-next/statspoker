<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221031094311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE seed (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, duration INT NOT NULL, quantity INT NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plant ADD seed_id INT NOT NULL, ADD date_updated DATE NOT NULL, ADD status VARCHAR(255) DEFAULT NULL, DROP name, DROP duration, DROP dateflo, DROP daterec');
        $this->addSql('ALTER TABLE plant ADD CONSTRAINT FK_AB030D7264430F6A FOREIGN KEY (seed_id) REFERENCES seed (id)');
        $this->addSql('CREATE INDEX IDX_AB030D7264430F6A ON plant (seed_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plant DROP FOREIGN KEY FK_AB030D7264430F6A');
        $this->addSql('DROP TABLE seed');
        $this->addSql('DROP INDEX IDX_AB030D7264430F6A ON plant');
        $this->addSql('ALTER TABLE plant ADD name VARCHAR(255) NOT NULL, ADD duration SMALLINT NOT NULL, ADD daterec DATE NOT NULL, DROP seed_id, DROP status, CHANGE date_updated dateflo DATE NOT NULL');
    }
}
