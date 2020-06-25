<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191021021112 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $create = <<<SQL
CREATE TABLE marital_status (
id INT AUTO_INCREMENT NOT NULL, 
name VARCHAR(255) NOT NULL, 
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
SQL;
        $this->addSql($create);

        $insert = <<<SQL
INSERT INTO marital_status (name) VALUES ('Soltero'), ('Casado'), ('Separado'), ('Divorciado'), ('Viudo')
SQL;
        $this->addSql($insert);
    }

    public function down(Schema $schema): void
    {
        $drop = <<<SQL
DROP TABLE marital_status
SQL;
        $this->addSql($drop);
    }
}
