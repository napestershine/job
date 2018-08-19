<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180207120318 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_C51063D1989D9B62 ON ys_organizations (slug)');
        $this->addSql('ALTER TABLE ys_agency_jobs CHANGE job_type job_type VARCHAR(255) DEFAULT NULL, CHANGE job_duration job_duration VARCHAR(255) DEFAULT NULL, CHANGE job_start_date job_start_date VARCHAR(255) DEFAULT NULL, CHANGE job_skills job_skills TINYTEXT DEFAULT NULL, CHANGE job_description job_description TINYTEXT DEFAULT NULL, CHANGE job_location job_location VARCHAR(255) DEFAULT NULL, CHANGE job_industry job_industry VARCHAR(255) DEFAULT NULL, CHANGE salary_currency salary_currency VARCHAR(255) DEFAULT NULL, CHANGE salary_from salary_from VARCHAR(255) DEFAULT NULL, CHANGE salary_to salary_to VARCHAR(255) DEFAULT NULL, CHANGE salary_per salary_per VARCHAR(255) DEFAULT NULL, CHANGE salary_benefits salary_benefits TINYTEXT DEFAULT NULL, CHANGE salary salary VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_agency_jobs CHANGE job_type job_type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE job_duration job_duration VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE job_start_date job_start_date VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE job_skills job_skills TINYTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE job_description job_description TINYTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE job_location job_location VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE job_industry job_industry VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE salary_currency salary_currency VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE salary_from salary_from VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE salary_to salary_to VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE salary_per salary_per VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE salary_benefits salary_benefits TINYTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE salary salary VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_C51063D1989D9B62 ON ys_organizations');
    }
}
