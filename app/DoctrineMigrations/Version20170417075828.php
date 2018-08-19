<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170417075828 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_organization_followers (organization_id INT NOT NULL, follower_id INT NOT NULL, INDEX IDX_AA8AFF1232C8A3DE (organization_id), INDEX IDX_AA8AFF12AC24F853 (follower_id), PRIMARY KEY(organization_id, follower_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_organization_followers ADD CONSTRAINT FK_AA8AFF1232C8A3DE FOREIGN KEY (organization_id) REFERENCES ys_organizations (id)');
        $this->addSql('ALTER TABLE ys_organization_followers ADD CONSTRAINT FK_AA8AFF12AC24F853 FOREIGN KEY (follower_id) REFERENCES ys_job_seekers (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_organization_followers');
    }
}
