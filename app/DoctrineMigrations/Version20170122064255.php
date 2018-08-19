<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170122064255 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_job_levels (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, sortOrder INT DEFAULT NULL, UNIQUE INDEX UNIQ_A6236BE6989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_setting (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, applyOnline TINYINT(1) NOT NULL, applyEmail TINYINT(1) NOT NULL, applyPost TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_59B0F03BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_jobs (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, industry_id INT DEFAULT NULL, level_id INT DEFAULT NULL, education_degree_id INT DEFAULT NULL, salary_unit_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, availability VARCHAR(20) NOT NULL, minimum_experience_year INT DEFAULT NULL, maximum_experience_year INT DEFAULT NULL, number_of_vacancies INT NOT NULL, vacancy_code VARCHAR(255) DEFAULT NULL, deadline DATE NOT NULL, description LONGTEXT DEFAULT NULL, specification LONGTEXT DEFAULT NULL, education_description LONGTEXT DEFAULT NULL, salary_negotiable TINYINT(1) DEFAULT NULL, minimum_salary NUMERIC(10, 3) DEFAULT NULL, maximum_salary NUMERIC(10, 3) DEFAULT NULL, preferred_gender VARCHAR(10) NOT NULL, minimum_age INT DEFAULT NULL, maximum_age INT DEFAULT NULL, specific_requirement LONGTEXT DEFAULT NULL, specific_instruction LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_5ABEDF2A989D9B62 (slug), INDEX IDX_5ABEDF2A12469DE2 (category_id), INDEX IDX_5ABEDF2A2B19A734 (industry_id), INDEX IDX_5ABEDF2A5FB14BA7 (level_id), INDEX IDX_5ABEDF2A27B078AC (education_degree_id), INDEX IDX_5ABEDF2A6FB9EAC1 (salary_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_location (job_id INT NOT NULL, location_id INT NOT NULL, INDEX IDX_39D92CB0BE04EA9 (job_id), INDEX IDX_39D92CB064D218E (location_id), PRIMARY KEY(job_id, location_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_currencies (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, symbol VARCHAR(5) DEFAULT NULL, status TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_locations (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_1C06CA29F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_education_degrees (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9BBB402F989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_setting ADD CONSTRAINT FK_59B0F03BE04EA9 FOREIGN KEY (job_id) REFERENCES ys_jobs (id)');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2A12469DE2 FOREIGN KEY (category_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2A2B19A734 FOREIGN KEY (industry_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2A5FB14BA7 FOREIGN KEY (level_id) REFERENCES ys_job_levels (id)');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2A27B078AC FOREIGN KEY (education_degree_id) REFERENCES ys_education_degrees (id)');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2A6FB9EAC1 FOREIGN KEY (salary_unit_id) REFERENCES ys_currencies (id)');
        $this->addSql('ALTER TABLE job_location ADD CONSTRAINT FK_39D92CB0BE04EA9 FOREIGN KEY (job_id) REFERENCES ys_jobs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_location ADD CONSTRAINT FK_39D92CB064D218E FOREIGN KEY (location_id) REFERENCES ys_locations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_locations ADD CONSTRAINT FK_1C06CA29F92F3E70 FOREIGN KEY (country_id) REFERENCES ys_countries (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_jobs DROP FOREIGN KEY FK_5ABEDF2A5FB14BA7');
        $this->addSql('ALTER TABLE job_setting DROP FOREIGN KEY FK_59B0F03BE04EA9');
        $this->addSql('ALTER TABLE job_location DROP FOREIGN KEY FK_39D92CB0BE04EA9');
        $this->addSql('ALTER TABLE ys_jobs DROP FOREIGN KEY FK_5ABEDF2A6FB9EAC1');
        $this->addSql('ALTER TABLE job_location DROP FOREIGN KEY FK_39D92CB064D218E');
        $this->addSql('ALTER TABLE ys_jobs DROP FOREIGN KEY FK_5ABEDF2A27B078AC');
        $this->addSql('DROP TABLE ys_job_levels');
        $this->addSql('DROP TABLE job_setting');
        $this->addSql('DROP TABLE ys_jobs');
        $this->addSql('DROP TABLE job_location');
        $this->addSql('DROP TABLE ys_currencies');
        $this->addSql('DROP TABLE ys_locations');
        $this->addSql('DROP TABLE ys_education_degrees');
    }
}
