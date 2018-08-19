<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170530071946 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE ys_job_seeker_call_record (id INT AUTO_INCREMENT NOT NULL, seeker_id INT DEFAULT NULL, feedback VARCHAR(255) DEFAULT NULL, remark VARCHAR(255) DEFAULT NULL, called_date DATE DEFAULT NULL, follow_up_date DATE DEFAULT NULL, admin_id INT NOT NULL, created DATETIME NOT NULL, UNIQUE INDEX UNIQ_6E58442057555B2 (seeker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_job_seeker_call_record ADD called_date DATE DEFAULT NULL, ADD follow_up_date DATE DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seeker_call_record DROP INDEX UNIQ_6E58442057555B2, ADD INDEX IDX_6E58442057555B2 (seeker_id)');
        $this->addSql('ALTER TABLE ys_job_seeker_call_record DROP called_date, DROP follow_up_date');
    }
}
