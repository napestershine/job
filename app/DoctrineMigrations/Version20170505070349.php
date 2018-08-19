<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170505070349 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

      //  $this->addSql('CREATE UNIQUE INDEX UNIQ_1202D7A5CAB86C7B ON ys_job_seekers (contact_email)');
        $this->addSql('ALTER TABLE ys_job_seeker_experiences ADD Currently_working TINYINT(1) DEFAULT NULL, CHANGE from_day from_day INT DEFAULT NULL, CHANGE to_day to_day INT DEFAULT NULL');
       // $this->addSql('CREATE UNIQUE INDEX UNIQ_C51063D1989D9B62 ON ys_organizations (slug)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seeker_experiences DROP Currently_working, CHANGE from_day from_day INT NOT NULL, CHANGE to_day to_day INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_1202D7A5CAB86C7B ON ys_job_seekers');
        $this->addSql('DROP INDEX UNIQ_C51063D1989D9B62 ON ys_organizations');
    }
}
