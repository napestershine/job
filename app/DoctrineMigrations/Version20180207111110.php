<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180207111110 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_agency_jobs (id INT AUTO_INCREMENT NOT NULL, job_reference VARCHAR(255) DEFAULT NULL, job_title VARCHAR(255) NOT NULL, job_type VARCHAR(255) NOT NULL, job_duration VARCHAR(255) NOT NULL, job_start_date VARCHAR(255) NOT NULL, job_skills TINYTEXT NOT NULL, job_description TINYTEXT NOT NULL, job_location VARCHAR(255) NOT NULL, job_industry VARCHAR(255) NOT NULL, salary_currency VARCHAR(255) NOT NULL, salary_from VARCHAR(255) NOT NULL, salary_to VARCHAR(255) NOT NULL, salary_per VARCHAR(255) NOT NULL, salary_benefits TINYTEXT NOT NULL, salary VARCHAR(255) NOT NULL, created_date DATETIME DEFAULT NULL, updated_date DATETIME DEFAULT NULL, deleted TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C51063D1989D9B62 ON ys_organizations (slug)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_agency_jobs');
        $this->addSql('DROP INDEX UNIQ_C51063D1989D9B62 ON ys_organizations');
    }
}
