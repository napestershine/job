<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170302064242 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

     //   $this->addSql('CREATE TABLE ys_job_seeker_references (id INT AUTO_INCREMENT NOT NULL, job_seeker_id INT DEFAULT NULL, organization VARCHAR(255) NOT NULL, designation VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, homePhone VARCHAR(255) DEFAULT NULL, officePhone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) NOT NULL, INDEX IDX_9EEB5773C2C5BAA3 (job_seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
      //  $this->addSql('CREATE TABLE ys_job_location (job_id INT NOT NULL, location_id INT NOT NULL, INDEX IDX_5D055021BE04EA9 (job_id), INDEX IDX_5D05502164D218E (location_id), PRIMARY KEY(job_id, location_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
      ////  $this->addSql('CREATE TABLE ys_job_setting (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, applyOnline TINYINT(1) NOT NULL, applyEmail TINYINT(1) NOT NULL, applyPost TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_B24F5E1ABE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
      //  $this->addSql('ALTER TABLE ys_job_seeker_references ADD CONSTRAINT FK_9EEB5773C2C5BAA3 FOREIGN KEY (job_seeker_id) REFERENCES ys_job_seekers (id)');
      //  $this->addSql('ALTER TABLE ys_job_location ADD CONSTRAINT FK_5D055021BE04EA9 FOREIGN KEY (job_id) REFERENCES ys_jobs (id) ON DELETE CASCADE');
      //  $this->addSql('ALTER TABLE ys_job_location ADD CONSTRAINT FK_5D05502164D218E FOREIGN KEY (location_id) REFERENCES ys_locations (id) ON DELETE CASCADE');
     //   $this->addSql('ALTER TABLE ys_job_setting ADD CONSTRAINT FK_B24F5E1ABE04EA9 FOREIGN KEY (job_id) REFERENCES ys_jobs (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_job_seeker_references');
        $this->addSql('DROP TABLE ys_job_location');
        $this->addSql('DROP TABLE ys_job_setting');
    }
}
