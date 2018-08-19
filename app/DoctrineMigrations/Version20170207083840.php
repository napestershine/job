<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170207083840 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_job_seeker_applied_jobs (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, employee_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, createdDate DATETIME NOT NULL, INDEX IDX_A5F34A7ABE04EA9 (job_id), INDEX IDX_A5F34A7A8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_job_seeker_jobs_basket (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, employee_id INT DEFAULT NULL, createdDate DATETIME NOT NULL, INDEX IDX_DFF77DE9BE04EA9 (job_id), INDEX IDX_DFF77DE98C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_job_seeker_applied_jobs ADD CONSTRAINT FK_A5F34A7ABE04EA9 FOREIGN KEY (job_id) REFERENCES ys_jobs (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_applied_jobs ADD CONSTRAINT FK_A5F34A7A8C03F15C FOREIGN KEY (employee_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_jobs_basket ADD CONSTRAINT FK_DFF77DE9BE04EA9 FOREIGN KEY (job_id) REFERENCES ys_jobs (id)');
        $this->addSql('ALTER TABLE ys_job_seeker_jobs_basket ADD CONSTRAINT FK_DFF77DE98C03F15C FOREIGN KEY (employee_id) REFERENCES ys_job_seekers (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_job_seeker_applied_jobs');
        $this->addSql('DROP TABLE ys_job_seeker_jobs_basket');
    }
}
