<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251221141358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_destination (activity_id INT NOT NULL, destination_id INT NOT NULL, INDEX IDX_304854A681C06096 (activity_id), INDEX IDX_304854A6816C6140 (destination_id), PRIMARY KEY (activity_id, destination_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE destination (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, climate VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity_destination ADD CONSTRAINT FK_304854A681C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_destination ADD CONSTRAINT FK_304854A6816C6140 FOREIGN KEY (destination_id) REFERENCES destination (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hotel ADD destination_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hotel ADD CONSTRAINT FK_3535ED9816C6140 FOREIGN KEY (destination_id) REFERENCES destination (id)');
        $this->addSql('CREATE INDEX IDX_3535ED9816C6140 ON hotel (destination_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_destination DROP FOREIGN KEY FK_304854A681C06096');
        $this->addSql('ALTER TABLE activity_destination DROP FOREIGN KEY FK_304854A6816C6140');
        $this->addSql('DROP TABLE activity_destination');
        $this->addSql('DROP TABLE destination');
        $this->addSql('ALTER TABLE hotel DROP FOREIGN KEY FK_3535ED9816C6140');
        $this->addSql('DROP INDEX IDX_3535ED9816C6140 ON hotel');
        $this->addSql('ALTER TABLE hotel DROP destination_id');
    }
}
