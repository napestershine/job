<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170119061824 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_countries (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, iso2 VARCHAR(2) DEFAULT NULL, iso3 VARCHAR(3) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, nationality VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_organization_settings (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, show_name TINYINT(1) DEFAULT NULL, show_address TINYINT(1) DEFAULT NULL, show_logo TINYINT(1) DEFAULT NULL, auto_email_responder LONGTEXT DEFAULT NULL, auto_email_status TINYINT(1) NOT NULL, INDEX IDX_D34A94B832C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_organizations (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, category_id INT DEFAULT NULL, ownership_type_id INT DEFAULT NULL, size_id INT DEFAULT NULL, country_id INT DEFAULT NULL, approved_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, profile LONGTEXT DEFAULT NULL, address LONGTEXT DEFAULT NULL, fax VARCHAR(50) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, post_box VARCHAR(50) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, secondary_email VARCHAR(255) DEFAULT NULL, profile_status INT DEFAULT NULL, status INT DEFAULT NULL, created_date DATETIME DEFAULT NULL, sort_order INT DEFAULT NULL, access_code VARCHAR(100) DEFAULT NULL, external_link VARCHAR(255) DEFAULT NULL, updated_date DATETIME DEFAULT NULL, visit INT DEFAULT NULL, nature VARCHAR(255) DEFAULT NULL, `label` INT DEFAULT NULL, INDEX IDX_C51063D1C54C8C93 (type_id), INDEX IDX_C51063D112469DE2 (category_id), INDEX IDX_C51063D19338D186 (ownership_type_id), INDEX IDX_C51063D1498DA827 (size_id), INDEX IDX_C51063D1F92F3E70 (country_id), INDEX IDX_C51063D12D234F6A (approved_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_organization_settings ADD CONSTRAINT FK_D34A94B832C8A3DE FOREIGN KEY (organization_id) REFERENCES ys_organizations (id)');
        $this->addSql('ALTER TABLE ys_organizations ADD CONSTRAINT FK_C51063D1C54C8C93 FOREIGN KEY (type_id) REFERENCES ys_organization_types (id)');
        $this->addSql('ALTER TABLE ys_organizations ADD CONSTRAINT FK_C51063D112469DE2 FOREIGN KEY (category_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_organizations ADD CONSTRAINT FK_C51063D19338D186 FOREIGN KEY (ownership_type_id) REFERENCES ys_organization_ownership (id)');
        $this->addSql('ALTER TABLE ys_organizations ADD CONSTRAINT FK_C51063D1498DA827 FOREIGN KEY (size_id) REFERENCES ys_organization_sizes (id)');
        $this->addSql('ALTER TABLE ys_organizations ADD CONSTRAINT FK_C51063D1F92F3E70 FOREIGN KEY (country_id) REFERENCES ys_countries (id)');
        $this->addSql('ALTER TABLE ys_organizations ADD CONSTRAINT FK_C51063D12D234F6A FOREIGN KEY (approved_by_id) REFERENCES ys_admin_users (id)');
        $this->addSql('ALTER TABLE ys_organization_sizes CHANGE status status TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_organization_contact_persons ADD organization_id INT DEFAULT NULL, ADD contact_type VARCHAR(20) NOT NULL, CHANGE status status TINYINT(1) NOT NULL, CHANGE deleted deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ys_organization_contact_persons ADD CONSTRAINT FK_7D3722AE32C8A3DE FOREIGN KEY (organization_id) REFERENCES ys_organizations (id)');
        $this->addSql('CREATE INDEX IDX_7D3722AE32C8A3DE ON ys_organization_contact_persons (organization_id)');
        $this->addSql('ALTER TABLE ys_organization_types CHANGE position_left position_left INT DEFAULT NULL, CHANGE position_right position_right INT DEFAULT NULL, CHANGE status status TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_organization_ownership CHANGE status status TINYINT(1) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_organizations DROP FOREIGN KEY FK_C51063D1F92F3E70');
        $this->addSql('ALTER TABLE ys_organization_contact_persons DROP FOREIGN KEY FK_7D3722AE32C8A3DE');
        $this->addSql('ALTER TABLE ys_organization_settings DROP FOREIGN KEY FK_D34A94B832C8A3DE');
        $this->addSql('DROP TABLE ys_countries');
        $this->addSql('DROP TABLE ys_organization_settings');
        $this->addSql('DROP TABLE ys_organizations');
        $this->addSql('DROP INDEX IDX_7D3722AE32C8A3DE ON ys_organization_contact_persons');
        $this->addSql('ALTER TABLE ys_organization_contact_persons DROP organization_id, DROP contact_type, CHANGE status status TINYINT(1) DEFAULT \'1\' NOT NULL, CHANGE deleted deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE ys_organization_ownership CHANGE status status TINYINT(1) DEFAULT \'1\'');
        $this->addSql('ALTER TABLE ys_organization_sizes CHANGE status status TINYINT(1) DEFAULT \'1\'');
        $this->addSql('ALTER TABLE ys_organization_types CHANGE position_left position_left INT DEFAULT 0, CHANGE position_right position_right INT DEFAULT 0, CHANGE status status TINYINT(1) DEFAULT \'1\'');
    }
}
