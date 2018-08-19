<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170129103557 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_locations DROP FOREIGN KEY FK_1C06CA2964D218E');
        $this->addSql('DROP INDEX IDX_1C06CA2964D218E ON ys_locations');
        $this->addSql('ALTER TABLE ys_locations DROP location_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_locations ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_locations ADD CONSTRAINT FK_1C06CA2964D218E FOREIGN KEY (location_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('CREATE INDEX IDX_1C06CA2964D218E ON ys_locations (location_id)');
    }
}
