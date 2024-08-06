<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240806084243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE measurement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sensor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wine_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE measurement (id INT NOT NULL, sensor_id_id INT NOT NULL, wine_id_id INT NOT NULL, year INT NOT NULL, color VARCHAR(255) NOT NULL, temperature DOUBLE PRECISION NOT NULL, graduation DOUBLE PRECISION NOT NULL, ph DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2CE0D8113900C4BF ON measurement (sensor_id_id)');
        $this->addSql('CREATE INDEX IDX_2CE0D8115F8EC3CB ON measurement (wine_id_id)');
        $this->addSql('CREATE TABLE sensor (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE wine (id INT NOT NULL, name VARCHAR(255) NOT NULL, year INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE measurement ADD CONSTRAINT FK_2CE0D8113900C4BF FOREIGN KEY (sensor_id_id) REFERENCES sensor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE measurement ADD CONSTRAINT FK_2CE0D8115F8EC3CB FOREIGN KEY (wine_id_id) REFERENCES wine (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE measurement_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sensor_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE wine_id_seq CASCADE');
        $this->addSql('ALTER TABLE measurement DROP CONSTRAINT FK_2CE0D8113900C4BF');
        $this->addSql('ALTER TABLE measurement DROP CONSTRAINT FK_2CE0D8115F8EC3CB');
        $this->addSql('DROP TABLE measurement');
        $this->addSql('DROP TABLE sensor');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE wine');
    }
}
