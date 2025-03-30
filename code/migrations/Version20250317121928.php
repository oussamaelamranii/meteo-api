<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317121928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advice ADD min_humidity DOUBLE PRECISION NOT NULL, ADD max_humidity DOUBLE PRECISION NOT NULL, ADD min_precipitation DOUBLE PRECISION NOT NULL, ADD max_precipitation DOUBLE PRECISION NOT NULL, ADD min_wind_speed DOUBLE PRECISION NOT NULL, ADD max_wind_speed DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advice DROP min_humidity, DROP max_humidity, DROP min_precipitation, DROP max_precipitation, DROP min_wind_speed, DROP max_wind_speed');
    }
}
