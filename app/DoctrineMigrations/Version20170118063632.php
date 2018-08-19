<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170118063632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_categories (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, section VARCHAR(255) DEFAULT NULL, published TINYINT(1) NOT NULL, deleted TINYINT(1) NOT NULL, level INT NOT NULL, sort_order INT NOT NULL, created_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_53FE6416989D9B62 (slug), INDEX IDX_53FE6416B03A8386 (created_by_id), INDEX IDX_53FE6416727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_categories ADD CONSTRAINT FK_53FE6416B03A8386 FOREIGN KEY (created_by_id) REFERENCES ys_admin_users (id)');
        $this->addSql('ALTER TABLE ys_categories ADD CONSTRAINT FK_53FE6416727ACA70 FOREIGN KEY (parent_id) REFERENCES ys_categories (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_categories DROP FOREIGN KEY FK_53FE6416727ACA70');
        $this->addSql('DROP TABLE ys_categories');
    }
}
