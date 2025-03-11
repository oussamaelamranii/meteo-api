<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311123940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advice DROP FOREIGN KEY FK_64820E8D273E1BE8');
        $this->addSql('CREATE TABLE land_plant (land_id INT NOT NULL, plants_id INT NOT NULL, INDEX IDX_4928835D1994904A (land_id), INDEX IDX_4928835D62091EAB (plants_id), PRIMARY KEY(land_id, plants_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE land_plant ADD CONSTRAINT FK_4928835D1994904A FOREIGN KEY (land_id) REFERENCES land (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE land_plant ADD CONSTRAINT FK_4928835D62091EAB FOREIGN KEY (plants_id) REFERENCES plants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE land_plants DROP FOREIGN KEY FK_E9DCAC11D935652');
        $this->addSql('DROP TABLE land_plants');
        $this->addSql('DROP INDEX IDX_64820E8D273E1BE8 ON advice');
        $this->addSql('ALTER TABLE advice ADD land_id INT NOT NULL, ADD plant_id INT NOT NULL, DROP land_plant_id');
        $this->addSql('ALTER TABLE advice ADD CONSTRAINT FK_64820E8D1994904A FOREIGN KEY (land_id) REFERENCES land (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE advice ADD CONSTRAINT FK_64820E8D1D935652 FOREIGN KEY (plant_id) REFERENCES plants (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_64820E8D1994904A ON advice (land_id)');
        $this->addSql('CREATE INDEX IDX_64820E8D1D935652 ON advice (plant_id)');
        $this->addSql('ALTER TABLE land DROP user_id, DROP name, DROP latitude, DROP longitude');
        $this->addSql('ALTER TABLE land ADD CONSTRAINT FK_A800D5D865FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('CREATE INDEX IDX_A800D5D865FCFA0D ON land (farm_id)');
        $this->addSql('ALTER TABLE plants DROP ideal_temp_min, DROP ideal_temp_max, DROP ideal_moisture_min, DROP ideal_moisture_max, DROP ideal_ph_min, DROP ideal_ph_max, DROP sunlight_requirement');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE land_plants (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, land_id INT NOT NULL, current_temp DOUBLE PRECISION NOT NULL, soil_moisture DOUBLE PRECISION NOT NULL, soil_ph DOUBLE PRECISION NOT NULL, growth_stage VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, last_updated DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E9DCAC11D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE land_plants ADD CONSTRAINT FK_E9DCAC11D935652 FOREIGN KEY (plant_id) REFERENCES plants (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE land_plant DROP FOREIGN KEY FK_4928835D1994904A');
        $this->addSql('ALTER TABLE land_plant DROP FOREIGN KEY FK_4928835D62091EAB');
        $this->addSql('DROP TABLE land_plant');
        $this->addSql('ALTER TABLE plants ADD ideal_temp_min DOUBLE PRECISION NOT NULL, ADD ideal_temp_max DOUBLE PRECISION NOT NULL, ADD ideal_moisture_min DOUBLE PRECISION NOT NULL, ADD ideal_moisture_max DOUBLE PRECISION NOT NULL, ADD ideal_ph_min DOUBLE PRECISION NOT NULL, ADD ideal_ph_max DOUBLE PRECISION NOT NULL, ADD sunlight_requirement VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE advice DROP FOREIGN KEY FK_64820E8D1994904A');
        $this->addSql('ALTER TABLE advice DROP FOREIGN KEY FK_64820E8D1D935652');
        $this->addSql('DROP INDEX IDX_64820E8D1994904A ON advice');
        $this->addSql('DROP INDEX IDX_64820E8D1D935652 ON advice');
        $this->addSql('ALTER TABLE advice ADD land_plant_id INT DEFAULT NULL, DROP land_id, DROP plant_id');
        $this->addSql('ALTER TABLE advice ADD CONSTRAINT FK_64820E8D273E1BE8 FOREIGN KEY (land_plant_id) REFERENCES land_plants (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_64820E8D273E1BE8 ON advice (land_plant_id)');
        $this->addSql('ALTER TABLE land DROP FOREIGN KEY FK_A800D5D865FCFA0D');
        $this->addSql('DROP INDEX IDX_A800D5D865FCFA0D ON land');
        $this->addSql('ALTER TABLE land ADD user_id INT NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD latitude NUMERIC(10, 6) NOT NULL, ADD longitude NUMERIC(10, 6) NOT NULL');
    }
}
