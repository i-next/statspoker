<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220724093448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi CHANGE buyin buyin DOUBLE PRECISION NOT NULL, CHANGE prizepool prizepool DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE tournoi_result CHANGE buyin buyin DOUBLE PRECISION NOT NULL, CHANGE prizepool prizepool DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi CHANGE buyin buyin SMALLINT NOT NULL, CHANGE prizepool prizepool SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE tournoi_result CHANGE buyin buyin SMALLINT NOT NULL, CHANGE prizepool prizepool SMALLINT NOT NULL');
    }
}
