<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250328125836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE general_advice (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, advice_text_en VARCHAR(255) NOT NULL, advice_text_fr VARCHAR(255) DEFAULT NULL, advice_text_ar VARCHAR(255) DEFAULT NULL, audio_path_ar VARCHAR(255) DEFAULT NULL, audio_path_fr VARCHAR(255) DEFAULT NULL, audio_path_en VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F5AAF3C91D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE general_advice ADD CONSTRAINT FK_F5AAF3C91D935652 FOREIGN KEY (plant_id) REFERENCES plants (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE advice DROP FOREIGN KEY FK_64820E8D1D935652');
        $this->addSql('ALTER TABLE advice CHANGE min_temp_c min_temp_c INT NOT NULL, CHANGE max_temp_c max_temp_c INT NOT NULL');
        $this->addSql('ALTER TABLE advice ADD CONSTRAINT FK_64820E8D1D935652 FOREIGN KEY (plant_id) REFERENCES plants (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE general_advice DROP FOREIGN KEY FK_F5AAF3C91D935652');
        $this->addSql('DROP TABLE general_advice');
        $this->addSql('ALTER TABLE advice DROP FOREIGN KEY FK_64820E8D1D935652');
        $this->addSql('ALTER TABLE advice CHANGE min_temp_c min_temp_c DOUBLE PRECISION NOT NULL, CHANGE max_temp_c max_temp_c DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE advice ADD CONSTRAINT FK_64820E8D1D935652 FOREIGN KEY (plant_id) REFERENCES plants (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
