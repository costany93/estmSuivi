<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210130131650 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE president (id INT AUTO_INCREMENT NOT NULL, club_id INT NOT NULL, etudiant_id INT NOT NULL, UNIQUE INDEX UNIQ_6E8BD21461190A32 (club_id), UNIQUE INDEX UNIQ_6E8BD214DDEAB1A3 (etudiant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE president ADD CONSTRAINT FK_6E8BD21461190A32 FOREIGN KEY (club_id) REFERENCES club (id)');
        $this->addSql('ALTER TABLE president ADD CONSTRAINT FK_6E8BD214DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE president');
    }
}
