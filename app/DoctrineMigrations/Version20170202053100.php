<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170202053100 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_applications (id INT AUTO_INCREMENT NOT NULL, jobseeker_id INT DEFAULT NULL, job_id INT DEFAULT NULL, created DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_93151A614CF2B5A9 (jobseeker_id), INDEX IDX_93151A61BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_applications ADD CONSTRAINT FK_93151A614CF2B5A9 FOREIGN KEY (jobseeker_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_applications ADD CONSTRAINT FK_93151A61BE04EA9 FOREIGN KEY (job_id) REFERENCES ys_jobs (id)');
        $this->addSql('ALTER TABLE ys_jobs CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2A2B19A734 FOREIGN KEY (industry_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2AA76ED395 FOREIGN KEY (user_id) REFERENCES ys_employers (id)');
        $this->addSql('CREATE INDEX IDX_5ABEDF2AA76ED395 ON ys_jobs (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_applications');
        $this->addSql('ALTER TABLE ys_jobs DROP FOREIGN KEY FK_5ABEDF2A2B19A734');
        $this->addSql('ALTER TABLE ys_jobs DROP FOREIGN KEY FK_5ABEDF2AA76ED395');
        $this->addSql('DROP INDEX IDX_5ABEDF2AA76ED395 ON ys_jobs');
        $this->addSql('ALTER TABLE ys_jobs CHANGE user_id user_id LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
