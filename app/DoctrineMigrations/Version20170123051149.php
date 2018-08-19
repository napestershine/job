<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170123051149 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seekers ADD preferenced_locations_id INT DEFAULT NULL, ADD preferenced_categories_id INT DEFAULT NULL, ADD preferenced_industries_id INT DEFAULT NULL, ADD preferenced_position_id INT DEFAULT NULL, ADD salutation VARCHAR(25) DEFAULT NULL, ADD father_name VARCHAR(255) DEFAULT NULL, ADD mother_name VARCHAR(255) DEFAULT NULL, ADD current_address VARCHAR(255) DEFAULT NULL, ADD permanent_address VARCHAR(255) DEFAULT NULL, ADD dob VARCHAR(255) DEFAULT NULL, ADD marital_status VARCHAR(50) DEFAULT NULL, ADD nationality VARCHAR(50) DEFAULT NULL, ADD religion VARCHAR(50) DEFAULT NULL, ADD mobile VARCHAR(50) DEFAULT NULL, ADD phone VARCHAR(50) DEFAULT NULL, ADD office_phone VARCHAR(50) DEFAULT NULL, ADD available_for VARCHAR(100) DEFAULT NULL, ADD expected_salary NUMERIC(10, 0) DEFAULT NULL, ADD present_salary NUMERIC(10, 0) DEFAULT NULL, ADD has_experienced TINYINT(1) DEFAULT NULL, ADD no_of_year INT DEFAULT NULL, ADD no_of_month INT DEFAULT NULL, ADD photo VARCHAR(255) DEFAULT NULL, ADD profile_completed TINYINT(1) DEFAULT NULL, ADD profile_completed_percentage NUMERIC(10, 0) DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A563AE339 FOREIGN KEY (preferenced_locations_id) REFERENCES ys_locations (id)');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A55F9BA070 FOREIGN KEY (preferenced_categories_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A54EF52204 FOREIGN KEY (preferenced_industries_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A59E0F0B83 FOREIGN KEY (preferenced_position_id) REFERENCES ys_job_levels (id)');
        $this->addSql('CREATE INDEX IDX_1202D7A563AE339 ON ys_job_seekers (preferenced_locations_id)');
        $this->addSql('CREATE INDEX IDX_1202D7A55F9BA070 ON ys_job_seekers (preferenced_categories_id)');
        $this->addSql('CREATE INDEX IDX_1202D7A54EF52204 ON ys_job_seekers (preferenced_industries_id)');
        $this->addSql('CREATE INDEX IDX_1202D7A59E0F0B83 ON ys_job_seekers (preferenced_position_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A563AE339');
        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A55F9BA070');
        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A54EF52204');
        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A59E0F0B83');
        $this->addSql('DROP INDEX IDX_1202D7A563AE339 ON ys_job_seekers');
        $this->addSql('DROP INDEX IDX_1202D7A55F9BA070 ON ys_job_seekers');
        $this->addSql('DROP INDEX IDX_1202D7A54EF52204 ON ys_job_seekers');
        $this->addSql('DROP INDEX IDX_1202D7A59E0F0B83 ON ys_job_seekers');
        $this->addSql('ALTER TABLE ys_job_seekers DROP preferenced_locations_id, DROP preferenced_categories_id, DROP preferenced_industries_id, DROP preferenced_position_id, DROP salutation, DROP father_name, DROP mother_name, DROP current_address, DROP permanent_address, DROP dob, DROP marital_status, DROP nationality, DROP religion, DROP mobile, DROP phone, DROP office_phone, DROP available_for, DROP expected_salary, DROP present_salary, DROP has_experienced, DROP no_of_year, DROP no_of_month, DROP photo, DROP profile_completed, DROP profile_completed_percentage');
    }
}
