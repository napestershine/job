<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170409084205 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seeker_educations ADD country_id INT DEFAULT NULL, DROP address');
        $this->addSql('ALTER TABLE ys_job_seeker_educations ADD CONSTRAINT FK_72FD0C42F92F3E70 FOREIGN KEY (country_id) REFERENCES ys_countries (id)');
        $this->addSql('CREATE INDEX IDX_72FD0C42F92F3E70 ON ys_job_seeker_educations (country_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seeker_educations DROP FOREIGN KEY FK_72FD0C42F92F3E70');
        $this->addSql('DROP INDEX IDX_72FD0C42F92F3E70 ON ys_job_seeker_educations');
        $this->addSql('ALTER TABLE ys_job_seeker_educations ADD address VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP country_id');
    }
}
