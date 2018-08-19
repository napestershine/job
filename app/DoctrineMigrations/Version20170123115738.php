<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170123115738 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ysjob_seeker_references (id INT AUTO_INCREMENT NOT NULL, job_seeker_id INT DEFAULT NULL, organization VARCHAR(255) NOT NULL, designation VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, homePhone VARCHAR(255) DEFAULT NULL, officePhone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) NOT NULL, INDEX IDX_3B912C70C2C5BAA3 (job_seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_job_seeker_experiences (id INT AUTO_INCREMENT NOT NULL, organization_type_id INT DEFAULT NULL, job_level_id INT DEFAULT NULL, country_id INT DEFAULT NULL, job_seeker_id INT DEFAULT NULL, organization_name VARCHAR(255) NOT NULL, employment_type VARCHAR(255) NOT NULL, designation VARCHAR(255) NOT NULL, from_year INT NOT NULL, from_month INT NOT NULL, to_year INT DEFAULT NULL, to_month INT DEFAULT NULL, roles LONGTEXT DEFAULT NULL, INDEX IDX_B2B604E389E04D0 (organization_type_id), INDEX IDX_B2B604E338F6EEDC (job_level_id), INDEX IDX_B2B604E3F92F3E70 (country_id), INDEX IDX_B2B604E3C2C5BAA3 (job_seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_job_seeker_educations (id INT AUTO_INCREMENT NOT NULL, job_seeker_id INT DEFAULT NULL, degree VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, institution VARCHAR(255) DEFAULT NULL, board VARCHAR(255) DEFAULT NULL, mark_system VARCHAR(255) DEFAULT NULL, percentage VARCHAR(255) DEFAULT NULL, specification VARCHAR(255) DEFAULT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, INDEX IDX_72FD0C42C2C5BAA3 (job_seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_job_seeker_languages (id INT AUTO_INCREMENT NOT NULL, job_seeker_id INT DEFAULT NULL, language VARCHAR(255) NOT NULL, reading VARCHAR(255) NOT NULL, writting VARCHAR(255) NOT NULL, speaking VARCHAR(255) NOT NULL, INDEX IDX_8EDABA38C2C5BAA3 (job_seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_job_seeker_settings (id INT AUTO_INCREMENT NOT NULL, job_seeker_id INT DEFAULT NULL, travel_for_job TINYINT(1) NOT NULL, have_license TINYINT(1) NOT NULL, have_license_of VARCHAR(255) NOT NULL, willing_to_relocation TINYINT(1) NOT NULL, have_vehicle TINYINT(1) NOT NULL, vehicle_type VARCHAR(255) NOT NULL, profile_searchable TINYINT(1) NOT NULL, profile_confidential TINYINT(1) NOT NULL, job_alert_table TINYINT(1) NOT NULL, facebook_alert TINYINT(1) NOT NULL, INDEX IDX_5AA160B8C2C5BAA3 (job_seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ysjob_seeker_references ADD CONSTRAINT FK_3B912C70C2C5BAA3 FOREIGN KEY (job_seeker_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_experiences ADD CONSTRAINT FK_B2B604E389E04D0 FOREIGN KEY (organization_type_id) REFERENCES ys_organization_types (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_experiences ADD CONSTRAINT FK_B2B604E338F6EEDC FOREIGN KEY (job_level_id) REFERENCES ys_job_levels (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_experiences ADD CONSTRAINT FK_B2B604E3F92F3E70 FOREIGN KEY (country_id) REFERENCES ys_countries (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_experiences ADD CONSTRAINT FK_B2B604E3C2C5BAA3 FOREIGN KEY (job_seeker_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_educations ADD CONSTRAINT FK_72FD0C42C2C5BAA3 FOREIGN KEY (job_seeker_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_languages ADD CONSTRAINT FK_8EDABA38C2C5BAA3 FOREIGN KEY (job_seeker_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_settings ADD CONSTRAINT FK_5AA160B8C2C5BAA3 FOREIGN KEY (job_seeker_id) REFERENCES ys_job_seekers (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ysjob_seeker_references');
        $this->addSql('DROP TABLE ys_job_seeker_experiences');
        $this->addSql('DROP TABLE ys_job_seeker_educations');
        $this->addSql('DROP TABLE ys_job_seeker_languages');
        $this->addSql('DROP TABLE ys_job_seeker_settings');
    }
}
