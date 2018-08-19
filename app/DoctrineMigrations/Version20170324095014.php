<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170324095014 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seekers ADD maximum_expected_salary NUMERIC(10, 0) DEFAULT NULL, CHANGE dob dob DATE DEFAULT NULL, CHANGE expected_salary minimum_expected_salary NUMERIC(10, 0) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C51063D1989D9B62 ON ys_organizations (slug)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seekers ADD expected_salary NUMERIC(10, 0) DEFAULT NULL, DROP minimum_expected_salary, DROP maximum_expected_salary, CHANGE dob dob VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX UNIQ_C51063D1989D9B62 ON ys_organizations');
    }
}
