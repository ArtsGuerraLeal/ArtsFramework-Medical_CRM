<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191022035147 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $create = <<<SQL
CREATE TABLE patient (
id INT AUTO_INCREMENT NOT NULL, 
marital_status_id INT DEFAULT NULL, 
first_name VARCHAR(255) NOT NULL, 
last_name VARCHAR(255) NOT NULL, 
gender CHAR(1) NOT NULL, 
birthdate DATE NOT NULL, 
email VARCHAR(255) NOT NULL, 
phone VARCHAR(255) NOT NULL, 
religion VARCHAR(255) NOT NULL, 
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
SQL;
        $this->addSql($create);

        $alter = <<<SQL
ALTER TABLE patient ADD CONSTRAINT FK_patient_marital_status FOREIGN KEY (marital_status_id) REFERENCES marital_status (id)
SQL;
        $this->addSql($alter);
    }

    public function down(Schema $schema) : void
    {
        $drop = <<<SQL
DROP TABLE patient
SQL;
        $this->addSql('DROP TABLE patient');
    }
}
