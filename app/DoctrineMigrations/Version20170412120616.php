<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170412120616 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_articles DROP userType');
        $this->addSql('ALTER TABLE ys_articles ADD CONSTRAINT FK_848283A7A76ED395 FOREIGN KEY (user_id) REFERENCES ys_admin_users (id)');
        $this->addSql('CREATE INDEX IDX_848283A7A76ED395 ON ys_articles (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_articles DROP FOREIGN KEY FK_848283A7A76ED395');
        $this->addSql('DROP INDEX IDX_848283A7A76ED395 ON ys_articles');
        $this->addSql('ALTER TABLE ys_articles ADD userType INT DEFAULT NULL');
    }
}
