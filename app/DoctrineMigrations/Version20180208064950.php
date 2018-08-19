<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180208064950 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSql('CREATE UNIQUE INDEX UNIQ_C51063D1989D9B62 ON ys_organizations (slug)');
        $this->addSql('ALTER TABLE ys_agency_jobs ADD agency_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_agency_jobs ADD CONSTRAINT FK_84020D4CDEADB2A FOREIGN KEY (agency_id) REFERENCES ys_agencies (id)');
        $this->addSql('CREATE INDEX IDX_84020D4CDEADB2A ON ys_agency_jobs (agency_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_agency_jobs DROP FOREIGN KEY FK_84020D4CDEADB2A');
        $this->addSql('DROP INDEX IDX_84020D4CDEADB2A ON ys_agency_jobs');
        $this->addSql('ALTER TABLE ys_agency_jobs DROP agency_id');
        $this->addSql('DROP INDEX UNIQ_C51063D1989D9B62 ON ys_organizations');
    }
}
