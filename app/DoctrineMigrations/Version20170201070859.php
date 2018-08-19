<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170201070859 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_article_tag_relation (article_id INT NOT NULL, article_tag_id INT NOT NULL, INDEX IDX_465848EA7294869C (article_id), INDEX IDX_465848EAD015F491 (article_tag_id), PRIMARY KEY(article_id, article_tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_article_tag_relation ADD CONSTRAINT FK_465848EA7294869C FOREIGN KEY (article_id) REFERENCES ys_articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_article_tag_relation ADD CONSTRAINT FK_465848EAD015F491 FOREIGN KEY (article_tag_id) REFERENCES ys_article_tags (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ys_article_tag');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_article_tag (article_id INT NOT NULL, article_tag_id INT NOT NULL, INDEX IDX_2642C5E07294869C (article_id), INDEX IDX_2642C5E0D015F491 (article_tag_id), PRIMARY KEY(article_id, article_tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_article_tag ADD CONSTRAINT FK_2642C5E07294869C FOREIGN KEY (article_id) REFERENCES ys_articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_article_tag ADD CONSTRAINT FK_2642C5E0D015F491 FOREIGN KEY (article_tag_id) REFERENCES ys_article_tags (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ys_article_tag_relation');
    }
}
