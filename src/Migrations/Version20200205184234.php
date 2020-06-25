<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200205184234 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE staff_positions_staff');
        $this->addSql('ALTER TABLE appointment ADD status VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE staff_positions_staff (staff_positions_id INT NOT NULL, staff_id INT NOT NULL, INDEX IDX_BB358DA9D4D57CD (staff_id), INDEX IDX_BB358DA9FD87DFC6 (staff_positions_id), PRIMARY KEY(staff_positions_id, staff_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE staff_positions_staff ADD CONSTRAINT FK_BB358DA9D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE staff_positions_staff ADD CONSTRAINT FK_BB358DA9FD87DFC6 FOREIGN KEY (staff_positions_id) REFERENCES staff_positions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment DROP status');
    }
}
