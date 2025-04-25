<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613141240 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE area (id INT AUTO_INCREMENT NOT NULL, area_cod VARCHAR(10) NOT NULL, area_nombre VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE equipos');
        $this->addSql('DROP TABLE homologacion');
        $this->addSql('ALTER TABLE orden_estudio DROP test, DROP fech_ingre_estudio, CHANGE imp imp INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE paciente DROP correo');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE equipos (cod_equipo VARCHAR(12) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, cod_analito_equipo VARCHAR(20) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, cod_analito_sw VARCHAR(10) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, cod_cups VARCHAR(20) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, des_analito_equipo VARCHAR(50) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, des_analito_sw VARCHAR(50) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, observaciones TEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, fechdigi DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, horadigi DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(cod_equipo, cod_analito_equipo, cod_analito_sw)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE homologacion (Cod_equipo VARCHAR(12) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, Cod_analito_equipo VARCHAR(20) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, Des_analito_equipo VARCHAR(50) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, Cod_analito_sw VARCHAR(20) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, Des_analito_sw VARCHAR(50) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, Observaciones TEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, Fechdigi DATE DEFAULT NULL, Horadigi DATE DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE area');
        $this->addSql('ALTER TABLE orden_estudio ADD test INT DEFAULT NULL, ADD fech_ingre_estudio DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE imp imp INT DEFAULT 0');
        $this->addSql('ALTER TABLE paciente ADD correo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
