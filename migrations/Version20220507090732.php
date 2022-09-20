<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220507090732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $colors = array('h','d','c','s');
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cards (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(1) NOT NULL, color VARCHAR(1) NOT NULL, count INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        foreach ($colors as $color){
            for($i=2;$i<10;$i++){
                $this->addSql('INSERT INTO cards(id,value,color,count) VALUES(NULL,'.$i.',"'.$color.'",0)');
            }
            $this->addSql('INSERT INTO cards(id,value,color,count) VALUES(NULL,"J","'.$color.'",0)');
            $this->addSql('INSERT INTO cards(id,value,color,count) VALUES(NULL,"Q","'.$color.'",0)');
            $this->addSql('INSERT INTO cards(id,value,color,count) VALUES(NULL,"K","'.$color.'",0)');
            $this->addSql('INSERT INTO cards(id,value,color,count) VALUES(NULL,"A","'.$color.'",0)');
            $this->addSql('INSERT INTO cards(id,value,color,count) VALUES(NULL,"T","'.$color.'",0)');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cards');
    }
}
