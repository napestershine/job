<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170309090127 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cover_letter ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cover_letter ADD CONSTRAINT FK_EBE6B47A76ED395 FOREIGN KEY (user_id) REFERENCES cover_letter (id)');
        $this->addSql('CREATE INDEX IDX_EBE6B47A76ED395 ON cover_letter (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cover_letter DROP FOREIGN KEY FK_EBE6B47A76ED395');
        $this->addSql('DROP INDEX IDX_EBE6B47A76ED395 ON cover_letter');
        $this->addSql('ALTER TABLE cover_letter DROP user_id');
    }
}
