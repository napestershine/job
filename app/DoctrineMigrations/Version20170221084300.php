<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170221084300 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_job_seeker_profile_completion (id INT AUTO_INCREMENT NOT NULL, personal NUMERIC(10, 2) DEFAULT NULL, general NUMERIC(10, 2) DEFAULT NULL, education NUMERIC(10, 2) DEFAULT NULL, training NUMERIC(10, 2) DEFAULT NULL, professional NUMERIC(10, 2) DEFAULT NULL, others NUMERIC(10, 2) DEFAULT NULL, overall NUMERIC(10, 2) DEFAULT NULL, is_cv_uploaded TINYINT(1) DEFAULT NULL, can_apply TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_job_seekers ADD profile_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A5D768E9C6 FOREIGN KEY (profile_status_id) REFERENCES ys_job_seeker_profile_completion (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1202D7A5D768E9C6 ON ys_job_seekers (profile_status_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A5D768E9C6');
        $this->addSql('DROP TABLE ys_job_seeker_profile_completion');
        $this->addSql('DROP INDEX UNIQ_1202D7A5D768E9C6 ON ys_job_seekers');
        $this->addSql('ALTER TABLE ys_job_seekers DROP profile_status_id');
    }
}
