<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204163225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE advice (id INT AUTO_INCREMENT NOT NULL, user_plant_id INT NOT NULL, advice_text LONGTEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plants (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, ideal_temp_min DOUBLE PRECISION NOT NULL, ideal_temp_max DOUBLE PRECISION NOT NULL, ideal_moisture_min DOUBLE PRECISION NOT NULL, ideal_moisture_max DOUBLE PRECISION NOT NULL, ideal_ph_min DOUBLE PRECISION NOT NULL, ideal_ph_max DOUBLE PRECISION NOT NULL, sunlight_requirement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_plants (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, plant_id INT NOT NULL, current_temp DOUBLE PRECISION NOT NULL, soil_moisture DOUBLE PRECISION NOT NULL, soil_ph DOUBLE PRECISION NOT NULL, growth_stage VARCHAR(255) NOT NULL, last_updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE advice');
        $this->addSql('DROP TABLE plants');
        $this->addSql('DROP TABLE user_plants');
    }
}
