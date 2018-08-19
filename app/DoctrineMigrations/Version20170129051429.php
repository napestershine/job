<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170129051429 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE ys_jobs ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_jobs ADD CONSTRAINT FK_5ABEDF2AA76ED395 FOREIGN KEY (user_id) REFERENCES ys_employers (id)');
        $this->addSql('CREATE INDEX IDX_5ABEDF2AA76ED395 ON ys_jobs (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_jobs DROP FOREIGN KEY FK_5ABEDF2AA76ED395');
        $this->addSql('DROP INDEX IDX_5ABEDF2AA76ED395 ON ys_jobs');
        $this->addSql('ALTER TABLE ys_jobs DROP user_id');
    }
}
