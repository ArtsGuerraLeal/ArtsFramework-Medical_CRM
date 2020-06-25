<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191022031805 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $create = <<<SQL
CREATE TABLE state (
id INT AUTO_INCREMENT NOT NULL, 
name VARCHAR(255) NOT NULL, 
PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
SQL;
        $this->addSql($create);

        $insert = <<<SQL
INSERT INTO state (name) VALUES
('Aguascalientes'),
('Baja California'),
('Baja California Sur'),
('Campeche'),
('Chiapas'),
('Chihuahua'),
('Ciudad de México'),
('Coahuila de Zaragoza'),
('Colima'),
('Durango'),
('Estado de México'),
('Guanajuato'),
('Guerrero'),
('Hidalgo'),
('Jalisco'),
('Michoacán'),
('Morelos'),
('Nayarit'),
('Nuevo León'),
('Oaxaca'),
('Puebla'),
('Querétaro'),
('Quintana Roo'),
('San Luis Potosí'),
('Sinaloa'),
('Sonora'),
('Tabasco'),
('Tamaulipas'),
('Tlaxcala'),
('Veracruz'),
('Yucatán'),
('Zacatecas')
SQL;
        $this->addSql($insert);
    }

    public function down(Schema $schema) : void
    {
        $drop = <<<SQL
DROP TABLE state
SQL;
        $this->addSql($drop);
    }
}
