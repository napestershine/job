<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170329094449 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_organizations ADD account_manager_id INT DEFAULT NULL, ADD deleted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_organizations ADD CONSTRAINT FK_C51063D184A5C6C7 FOREIGN KEY (account_manager_id) REFERENCES ys_employers (id)');
        $this->addSql('CREATE INDEX IDX_C51063D184A5C6C7 ON ys_organizations (account_manager_id)');
        $this->addSql('UPDATE ys_organizations SET deleted = 0');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_organizations DROP FOREIGN KEY FK_C51063D184A5C6C7');
        $this->addSql('DROP INDEX IDX_C51063D184A5C6C7 ON ys_organizations');
        $this->addSql('ALTER TABLE ys_organizations DROP account_manager_id, DROP deleted');
    }
}
