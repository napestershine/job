<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170309103754 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cover_letter DROP FOREIGN KEY FK_EBE6B47A76ED395');
        $this->addSql('ALTER TABLE cover_letter CHANGE `default` default_letter TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE cover_letter ADD CONSTRAINT FK_EBE6B47A76ED395 FOREIGN KEY (user_id) REFERENCES ys_job_seekers (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cover_letter DROP FOREIGN KEY FK_EBE6B47A76ED395');
        $this->addSql('ALTER TABLE cover_letter CHANGE default_letter `default` TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE cover_letter ADD CONSTRAINT FK_EBE6B47A76ED395 FOREIGN KEY (user_id) REFERENCES cover_letter (id)');
    }
}
