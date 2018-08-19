<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170119104412 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_employers ADD organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_employers ADD CONSTRAINT FK_B4E1C70532C8A3DE FOREIGN KEY (organization_id) REFERENCES ys_organizations (id)');
        $this->addSql('CREATE INDEX IDX_B4E1C70532C8A3DE ON ys_employers (organization_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_employers DROP FOREIGN KEY FK_B4E1C70532C8A3DE');
        $this->addSql('DROP INDEX IDX_B4E1C70532C8A3DE ON ys_employers');
        $this->addSql('ALTER TABLE ys_employers DROP organization_id');
    }
}
