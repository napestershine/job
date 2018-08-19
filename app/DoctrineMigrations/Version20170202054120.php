<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170202054120 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_applications CHANGE status status INT NOT NULL');
        $this->addSql('ALTER TABLE ys_jobs CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2A2B19A734 FOREIGN KEY (industry_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2AA76ED395 FOREIGN KEY (user_id) REFERENCES ys_employers (id)');
        $this->addSql('CREATE INDEX IDX_5ABEDF2AA76ED395 ON ys_jobs (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_applications CHANGE status status VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE ys_jobs DROP FOREIGN KEY FK_5ABEDF2A2B19A734');
        $this->addSql('ALTER TABLE ys_jobs DROP FOREIGN KEY FK_5ABEDF2AA76ED395');
        $this->addSql('DROP INDEX IDX_5ABEDF2AA76ED395 ON ys_jobs');
        $this->addSql('ALTER TABLE ys_jobs CHANGE user_id user_id LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
