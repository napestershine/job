<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170118062727 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_organization_types (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, position_left INT DEFAULT 0, position_right INT DEFAULT 0, created_date DATETIME DEFAULT NULL, updated_date DATETIME DEFAULT NULL, status TINYINT(1) DEFAULT 1, INDEX IDX_1EF3C202727ACA70 (parent_id), INDEX IDX_1EF3C202B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_organization_types ADD CONSTRAINT FK_1EF3C202727ACA70 FOREIGN KEY (parent_id) REFERENCES ys_organization_types (id)');
        $this->addSql('ALTER TABLE ys_organization_types ADD CONSTRAINT FK_1EF3C202B03A8386 FOREIGN KEY (created_by_id) REFERENCES ys_admin_users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_organization_types DROP FOREIGN KEY FK_1EF3C202727ACA70');
        $this->addSql('DROP TABLE ys_organization_types');
    }
}
