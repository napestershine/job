<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170205100139 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_tags_description (id INT AUTO_INCREMENT NOT NULL, tag_id INT DEFAULT NULL, entity_id INT NOT NULL, entity_class VARCHAR(255) NOT NULL, INDEX IDX_85CE1373BAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9D9126C9989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_tags_description ADD CONSTRAINT FK_85CE1373BAD26311 FOREIGN KEY (tag_id) REFERENCES ys_tags (id)');
        $this->addSql('DROP TABLE ys_article_tag_relation');
        $this->addSql('ALTER TABLE ys_articles ADD tags VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_tags_description DROP FOREIGN KEY FK_85CE1373BAD26311');
        $this->addSql('CREATE TABLE ys_article_tag_relation (article_id INT NOT NULL, article_tag_id INT NOT NULL, INDEX IDX_465848EA7294869C (article_id), INDEX IDX_465848EAD015F491 (article_tag_id), PRIMARY KEY(article_id, article_tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_article_tag_relation ADD CONSTRAINT FK_465848EA7294869C FOREIGN KEY (article_id) REFERENCES ys_articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_article_tag_relation ADD CONSTRAINT FK_465848EAD015F491 FOREIGN KEY (article_tag_id) REFERENCES ys_article_tags (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ys_tags_description');
        $this->addSql('DROP TABLE ys_tags');
        $this->addSql('ALTER TABLE ys_articles DROP tags');
    }
}
