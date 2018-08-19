<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170131113818 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_articles (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, type INT NOT NULL, category INT NOT NULL, content LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, link_text VARCHAR(255) DEFAULT NULL, link_url VARCHAR(255) DEFAULT NULL, status INT NOT NULL, deleted TINYINT(1) NOT NULL, hits INT DEFAULT NULL, created_date DATETIME DEFAULT NULL, updated_date DATETIME DEFAULT NULL, user_id INT DEFAULT NULL, userType INT NOT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, meta_descriptions VARCHAR(255) DEFAULT NULL, meta_tags VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_848283A78A918066 (userType), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_article_tag (article_id INT NOT NULL, article_tag_id INT NOT NULL, INDEX IDX_2642C5E07294869C (article_id), INDEX IDX_2642C5E0D015F491 (article_tag_id), PRIMARY KEY(article_id, article_tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_article_tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_article_tag ADD CONSTRAINT FK_2642C5E07294869C FOREIGN KEY (article_id) REFERENCES ys_articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_article_tag ADD CONSTRAINT FK_2642C5E0D015F491 FOREIGN KEY (article_tag_id) REFERENCES ys_article_tags (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_article_tag DROP FOREIGN KEY FK_2642C5E07294869C');
        $this->addSql('ALTER TABLE ys_article_tag DROP FOREIGN KEY FK_2642C5E0D015F491');
        $this->addSql('DROP TABLE ys_articles');
        $this->addSql('DROP TABLE ys_article_tag');
        $this->addSql('DROP TABLE ys_article_tags');
    }
}
