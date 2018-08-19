<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170226055452 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_organizations ADD industry_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_organizations ADD CONSTRAINT FK_C51063D12B19A734 FOREIGN KEY (industry_id) REFERENCES ys_categories (id)');
        $this->addSql('CREATE INDEX IDX_C51063D12B19A734 ON ys_organizations (industry_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_organizations DROP FOREIGN KEY FK_C51063D12B19A734');
        $this->addSql('DROP INDEX IDX_C51063D12B19A734 ON ys_organizations');
        $this->addSql('ALTER TABLE ys_organizations DROP industry_id');
    }
}
