<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170305081243 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seekers ADD degree_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A5B35C5756 FOREIGN KEY (degree_id) REFERENCES ys_education_degrees (id)');
        $this->addSql('CREATE INDEX IDX_1202D7A5B35C5756 ON ys_job_seekers (degree_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

	$this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A5B35C5756');        
	$this->addSql('DROP INDEX IDX_1202D7A5B35C5756 ON ys_job_seekers');
        $this->addSql('ALTER TABLE ys_job_seekers DROP degree_id');
    }
}
