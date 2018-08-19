<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170406044637 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seeker_educations ADD degree_id INT DEFAULT NULL, DROP degree');
        $this->addSql('ALTER TABLE ys_job_seeker_educations ADD CONSTRAINT FK_72FD0C42B35C5756 FOREIGN KEY (degree_id) REFERENCES ys_education_degrees (id)');
        $this->addSql('CREATE INDEX IDX_72FD0C42B35C5756 ON ys_job_seeker_educations (degree_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seeker_educations DROP FOREIGN KEY FK_72FD0C42B35C5756');
        $this->addSql('DROP INDEX IDX_72FD0C42B35C5756 ON ys_job_seeker_educations');
        $this->addSql('ALTER TABLE ys_job_seeker_educations ADD degree VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP degree_id');
    }
}
