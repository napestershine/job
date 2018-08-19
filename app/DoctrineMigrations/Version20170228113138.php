<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170228113138 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_organizayions_banner_images (id INT AUTO_INCREMENT NOT NULL, employer_id INT DEFAULT NULL, caption VARCHAR(255) DEFAULT NULL, path TINYTEXT DEFAULT NULL, is_featured TINYINT(1) DEFAULT NULL, status INT DEFAULT NULL, INDEX IDX_ADB29ADA41CD9E7A (employer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_organizayions_banner_images ADD CONSTRAINT FK_ADB29ADA41CD9E7A FOREIGN KEY (employer_id) REFERENCES ys_employers (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_organizayions_banner_images');
    }
}
