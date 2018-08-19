<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170402113749 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    //    $this->addSql('DROP TABLE employee_seeker_basket');
     //   $this->addSql('CREATE UNIQUE INDEX UNIQ_C51063D1989D9B62 ON ys_organizations (slug)');
        $this->addSql('ALTER TABLE ys_organizayions_banner_images DROP FOREIGN KEY FK_ADB29ADA41CD9E7A');
        $this->addSql('ALTER TABLE ys_organizayions_banner_images ADD CONSTRAINT FK_ADB29ADA41CD9E7A FOREIGN KEY (employer_id) REFERENCES ys_organizations (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE employee_seeker_basket (id INT AUTO_INCREMENT NOT NULL, employeeId INT NOT NULL, employerId VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, createdDate DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP INDEX UNIQ_C51063D1989D9B62 ON ys_organizations');
        $this->addSql('ALTER TABLE ys_organizayions_banner_images DROP FOREIGN KEY FK_ADB29ADA41CD9E7A');
        $this->addSql('ALTER TABLE ys_organizayions_banner_images ADD CONSTRAINT FK_ADB29ADA41CD9E7A FOREIGN KEY (employer_id) REFERENCES ys_employers (id)');
    }
}
