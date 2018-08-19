<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170120094344 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_notices (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, status INT NOT NULL, is_deleted TINYINT(1) NOT NULL, created_date DATETIME NOT NULL, updated_date DATETIME DEFAULT NULL, user_id INT DEFAULT NULL, user_type INT DEFAULT NULL, UNIQUE INDEX UNIQ_5FBE66F2989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_testimonials (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, status INT NOT NULL, is_deleted TINYINT(1) NOT NULL, sort_order INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, created_date DATETIME DEFAULT NULL, updated_date DATETIME DEFAULT NULL, user_id INT DEFAULT NULL, user_type INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_notices');
        $this->addSql('DROP TABLE ys_testimonials');
    }
}
