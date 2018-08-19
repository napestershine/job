<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170124064927 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seeker_experiences DROP FOREIGN KEY FK_B2B604E389E04D0');
        $this->addSql('ALTER TABLE ys_job_seeker_experiences ADD CONSTRAINT FK_B2B604E389E04D0 FOREIGN KEY (organization_type_id) REFERENCES ys_organizations (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seeker_experiences DROP FOREIGN KEY FK_B2B604E389E04D0');
        $this->addSql('ALTER TABLE ys_job_seeker_experiences ADD CONSTRAINT FK_B2B604E389E04D0 FOREIGN KEY (organization_type_id) REFERENCES ys_organization_types (id)');
    }
}
