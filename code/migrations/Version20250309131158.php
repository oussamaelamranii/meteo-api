<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250309131158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE farm (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, lands LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE land (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, latitude NUMERIC(10, 6) NOT NULL, longitude NUMERIC(10, 6) NOT NULL, farm_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE land_plants (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, land_id INT NOT NULL, current_temp DOUBLE PRECISION NOT NULL, soil_moisture DOUBLE PRECISION NOT NULL, soil_ph DOUBLE PRECISION NOT NULL, growth_stage VARCHAR(255) NOT NULL, last_updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E9DCAC11D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE land_plants ADD CONSTRAINT FK_E9DCAC11D935652 FOREIGN KEY (plant_id) REFERENCES plants (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE user_plants');
        $this->addSql('ALTER TABLE advice ADD min_temp_c INT NOT NULL, ADD max_temp_c INT NOT NULL, ADD red_alert TINYINT(1) NOT NULL, CHANGE user_plant_id land_plant_id INT NOT NULL');
        $this->addSql('ALTER TABLE advice ADD CONSTRAINT FK_64820E8D273E1BE8 FOREIGN KEY (land_plant_id) REFERENCES land_plants (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_64820E8D273E1BE8 ON advice (land_plant_id)');
        $this->addSql('ALTER TABLE plants ADD safe_min_temp_c INT NOT NULL, ADD safe_max_temp_c INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advice DROP FOREIGN KEY FK_64820E8D273E1BE8');
        $this->addSql('CREATE TABLE user_plants (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, plant_id INT NOT NULL, current_temp DOUBLE PRECISION NOT NULL, soil_moisture DOUBLE PRECISION NOT NULL, soil_ph DOUBLE PRECISION NOT NULL, growth_stage VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, last_updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE land_plants DROP FOREIGN KEY FK_E9DCAC11D935652');
        $this->addSql('DROP TABLE farm');
        $this->addSql('DROP TABLE land');
        $this->addSql('DROP TABLE land_plants');
        $this->addSql('ALTER TABLE plants DROP safe_min_temp_c, DROP safe_max_temp_c');
        $this->addSql('DROP INDEX IDX_64820E8D273E1BE8 ON advice');
        $this->addSql('ALTER TABLE advice ADD user_plant_id INT NOT NULL, DROP land_plant_id, DROP min_temp_c, DROP max_temp_c, DROP red_alert');
    }
}
