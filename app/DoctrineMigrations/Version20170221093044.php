<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170221093044 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_job_seeker_trainings (id INT AUTO_INCREMENT NOT NULL, job_seeker_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, institution VARCHAR(255) DEFAULT NULL, duration VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, objective LONGTEXT DEFAULT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX IDX_48D7AA71C2C5BAA3 (job_seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_job_seeker_trainings ADD CONSTRAINT FK_48D7AA71C2C5BAA3 FOREIGN KEY (job_seeker_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_job_seekers ADD middle_name VARCHAR(20) DEFAULT NULL, ADD contact_email VARCHAR(255) DEFAULT NULL, CHANGE first_name first_name VARCHAR(20) DEFAULT NULL, CHANGE last_name last_name VARCHAR(20) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_job_seeker_trainings');
        $this->addSql('ALTER TABLE ys_job_seekers DROP middle_name, DROP contact_email, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
