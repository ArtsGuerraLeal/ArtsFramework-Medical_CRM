<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191022030208 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $create = <<<SQL
CREATE TABLE equipment (
id INT AUTO_INCREMENT NOT NULL,
name VARCHAR(255) NOT NULL,
cost DECIMAL(10, 2) DEFAULT NULL,
purchased_on DATE DEFAULT NULL,
last_used_on DATE DEFAULT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
SQL;
        $this->addSql($create);
    }

    public function down(Schema $schema) : void
    {
        $drop = <<<SQL
DROP TABLE equipment
SQL;
        $this->addSql($drop);
    }
}
