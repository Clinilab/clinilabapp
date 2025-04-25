<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231010143211 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('reportes_sql');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('nombre', 'string', ['length' => 255]);
        $table->addColumn('descripcion', 'text', ['notnull' => false]);
        $table->addColumn('consulta_sql', 'text',['notnull' => false]);
        $table->addColumn('fecha_creacion', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema) : void
    {
        $schema->dropTable('reportes_sql');
    }
}
