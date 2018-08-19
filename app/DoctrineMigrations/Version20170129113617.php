<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170129113617 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_job_applied (id INT AUTO_INCREMENT NOT NULL, job_seeker_id INT DEFAULT NULL, job_id INT DEFAULT NULL, Type VARCHAR(255) NOT NULL, Status TINYINT(1) DEFAULT NULL, INDEX IDX_BC51114C2C5BAA3 (job_seeker_id), INDEX IDX_BC51114BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_job_applied ADD CONSTRAINT FK_BC51114C2C5BAA3 FOREIGN KEY (job_seeker_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_job_applied ADD CONSTRAINT FK_BC51114BE04EA9 FOREIGN KEY (job_id) REFERENCES ys_jobs (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_job_applied');
    }
}
