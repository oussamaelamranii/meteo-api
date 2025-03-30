<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318114601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advice DROP FOREIGN KEY FK_64820E8D1994904A');
        $this->addSql('ALTER TABLE advice ADD CONSTRAINT FK_64820E8D1994904A FOREIGN KEY (land_id) REFERENCES land (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE land_plant DROP FOREIGN KEY FK_4928835D1994904A');
        $this->addSql('ALTER TABLE land_plant DROP FOREIGN KEY FK_4928835D62091EAB');
        $this->addSql('ALTER TABLE land_plant ADD CONSTRAINT FK_4928835D1994904A FOREIGN KEY (land_id) REFERENCES land (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE land_plant ADD CONSTRAINT FK_4928835D62091EAB FOREIGN KEY (plants_id) REFERENCES plants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plants ADD safe_min_humidity DOUBLE PRECISION DEFAULT NULL, ADD safe_max_humidity DOUBLE PRECISION DEFAULT NULL, ADD safe_min_precipitation DOUBLE PRECISION DEFAULT NULL, ADD safe_max_precipitation DOUBLE PRECISION DEFAULT NULL, ADD safe_min_wind_speed DOUBLE PRECISION DEFAULT NULL, ADD safe_max_wind_speed DOUBLE PRECISION DEFAULT NULL, CHANGE safe_min_temp_c safe_min_temp_c DOUBLE PRECISION DEFAULT NULL, CHANGE safe_max_temp_c safe_max_temp_c DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advice DROP FOREIGN KEY FK_64820E8D1994904A');
        $this->addSql('ALTER TABLE advice ADD CONSTRAINT FK_64820E8D1994904A FOREIGN KEY (land_id) REFERENCES land (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plants DROP safe_min_humidity, DROP safe_max_humidity, DROP safe_min_precipitation, DROP safe_max_precipitation, DROP safe_min_wind_speed, DROP safe_max_wind_speed, CHANGE safe_min_temp_c safe_min_temp_c INT NOT NULL, CHANGE safe_max_temp_c safe_max_temp_c INT NOT NULL');
        $this->addSql('ALTER TABLE land_plant DROP FOREIGN KEY FK_4928835D1994904A');
        $this->addSql('ALTER TABLE land_plant DROP FOREIGN KEY FK_4928835D62091EAB');
        $this->addSql('ALTER TABLE land_plant ADD CONSTRAINT FK_4928835D1994904A FOREIGN KEY (land_id) REFERENCES land (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE land_plant ADD CONSTRAINT FK_4928835D62091EAB FOREIGN KEY (plants_id) REFERENCES plants (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
