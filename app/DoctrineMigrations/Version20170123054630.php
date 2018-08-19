<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170123054630 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A54EF52204');
        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A55F9BA070');
        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A563AE339');
        $this->addSql('DROP INDEX IDX_1202D7A563AE339 ON ys_job_seekers');
        $this->addSql('DROP INDEX IDX_1202D7A55F9BA070 ON ys_job_seekers');
        $this->addSql('DROP INDEX IDX_1202D7A54EF52204 ON ys_job_seekers');
        $this->addSql('ALTER TABLE ys_job_seekers DROP preferenced_industries_id, DROP preferenced_categories_id, DROP preferenced_locations_id');
        $this->addSql('ALTER TABLE ys_locations ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_locations ADD CONSTRAINT FK_1C06CA2964D218E FOREIGN KEY (location_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('CREATE INDEX IDX_1C06CA2964D218E ON ys_locations (location_id)');
        $this->addSql('ALTER TABLE ys_categories ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_categories ADD CONSTRAINT FK_53FE641612469DE2 FOREIGN KEY (category_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('CREATE INDEX IDX_53FE641612469DE2 ON ys_categories (category_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ys_categories DROP FOREIGN KEY FK_53FE641612469DE2');
        $this->addSql('DROP INDEX IDX_53FE641612469DE2 ON ys_categories');
        $this->addSql('ALTER TABLE ys_categories DROP category_id');
        $this->addSql('ALTER TABLE ys_job_seekers ADD preferenced_industries_id INT DEFAULT NULL, ADD preferenced_categories_id INT DEFAULT NULL, ADD preferenced_locations_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A54EF52204 FOREIGN KEY (preferenced_industries_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A55F9BA070 FOREIGN KEY (preferenced_categories_id) REFERENCES ys_categories (id)');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A563AE339 FOREIGN KEY (preferenced_locations_id) REFERENCES ys_locations (id)');
        $this->addSql('CREATE INDEX IDX_1202D7A563AE339 ON ys_job_seekers (preferenced_locations_id)');
        $this->addSql('CREATE INDEX IDX_1202D7A55F9BA070 ON ys_job_seekers (preferenced_categories_id)');
        $this->addSql('CREATE INDEX IDX_1202D7A54EF52204 ON ys_job_seekers (preferenced_industries_id)');
        $this->addSql('ALTER TABLE ys_locations DROP FOREIGN KEY FK_1C06CA2964D218E');
        $this->addSql('DROP INDEX IDX_1C06CA2964D218E ON ys_locations');
        $this->addSql('ALTER TABLE ys_locations DROP location_id');
    }
}
