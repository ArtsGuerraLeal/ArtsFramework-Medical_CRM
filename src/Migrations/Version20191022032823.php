<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191022032823 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $create = <<<SQL
CREATE TABLE address (
id INT AUTO_INCREMENT NOT NULL, 
state_id INT NOT NULL, 
line1 VARCHAR(255) NOT NULL, 
line2 VARCHAR(255) NOT NULL, 
postal_code CHAR(5) NOT NULL, 
city VARCHAR(255) NOT NULL, 
PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
SQL;
        $this->addSql($create);

        $alter = <<<SQL
ALTER TABLE address ADD CONSTRAINT FK_address_state FOREIGN KEY (state_id) REFERENCES state (id)
SQL;
        $this->addSql($alter);
    }

    public function down(Schema $schema) : void
    {
        $drop = <<<SQL
DROP TABLE address
SQL;
        $this->addSql('DROP TABLE address');
    }
}
