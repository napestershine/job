<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170525082232 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

//        $this->addSql('CREATE TABLE ys_job_seeker_call_record (id INT AUTO_INCREMENT NOT NULL, seeker_id INT DEFAULT NULL, remark VARCHAR(255) DEFAULT NULL, feedback LONGTEXT NOT NULL, admin_id INT NOT NULL, created DATETIME NOT NULL, INDEX IDX_6E58442057555B2 (seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
//        $this->addSql('ALTER TABLE ys_job_seeker_call_record ADD CONSTRAINT FK_6E58442057555B2 FOREIGN KEY (seeker_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('ALTER TABLE ys_job_seekers CHANGE created_at created_at DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_job_seeker_call_record');
        $this->addSql('DROP INDEX UNIQ_1202D7A5CAB86C7B ON ys_job_seekers');
        $this->addSql('ALTER TABLE ys_job_seekers CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
