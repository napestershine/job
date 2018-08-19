<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170130085923 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ys_seeker_preferred_locations (user_id INT NOT NULL, location_id INT NOT NULL, INDEX IDX_35B755EAA76ED395 (user_id), INDEX IDX_35B755EA64D218E (location_id), PRIMARY KEY(user_id, location_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_seeker_preferred_categories (user_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_51BA4683A76ED395 (user_id), INDEX IDX_51BA468312469DE2 (category_id), PRIMARY KEY(user_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ys_seeker_preferred_industries (user_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_D335260A76ED395 (user_id), INDEX IDX_D33526012469DE2 (category_id), PRIMARY KEY(user_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ys_seeker_preferred_locations ADD CONSTRAINT FK_35B755EAA76ED395 FOREIGN KEY (user_id) REFERENCES ys_job_seekers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_seeker_preferred_locations ADD CONSTRAINT FK_35B755EA64D218E FOREIGN KEY (location_id) REFERENCES ys_locations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_seeker_preferred_categories ADD CONSTRAINT FK_51BA4683A76ED395 FOREIGN KEY (user_id) REFERENCES ys_job_seekers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_seeker_preferred_categories ADD CONSTRAINT FK_51BA468312469DE2 FOREIGN KEY (category_id) REFERENCES ys_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_seeker_preferred_industries ADD CONSTRAINT FK_D335260A76ED395 FOREIGN KEY (user_id) REFERENCES ys_job_seekers (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_seeker_preferred_industries ADD CONSTRAINT FK_D33526012469DE2 FOREIGN KEY (category_id) REFERENCES ys_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A59E0F0B83');
        $this->addSql('DROP INDEX IDX_1202D7A59E0F0B83 ON ys_job_seekers');
        $this->addSql('ALTER TABLE ys_job_seekers CHANGE preferenced_position_id preferred_position_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A59FFBBC37 FOREIGN KEY (preferred_position_id) REFERENCES ys_job_levels (id)');
        $this->addSql('CREATE INDEX IDX_1202D7A59FFBBC37 ON ys_job_seekers (preferred_position_id)');
        $this->addSql('ALTER TABLE ys_categories DROP FOREIGN KEY FK_53FE641612469DE2');
        $this->addSql('DROP INDEX IDX_53FE641612469DE2 ON ys_categories');
        $this->addSql('ALTER TABLE ys_categories DROP category_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ys_seeker_preferred_locations');
        $this->addSql('DROP TABLE ys_seeker_preferred_categories');
        $this->addSql('DROP TABLE ys_seeker_preferred_industries');
        $this->addSql('ALTER TABLE ys_categories ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_categories ADD CONSTRAINT FK_53FE641612469DE2 FOREIGN KEY (category_id) REFERENCES ys_job_seekers (id)');
        $this->addSql('CREATE INDEX IDX_53FE641612469DE2 ON ys_categories (category_id)');
        $this->addSql('ALTER TABLE ys_job_seekers DROP FOREIGN KEY FK_1202D7A59FFBBC37');
        $this->addSql('DROP INDEX IDX_1202D7A59FFBBC37 ON ys_job_seekers');
        $this->addSql('ALTER TABLE ys_job_seekers CHANGE preferred_position_id preferenced_position_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ys_job_seekers ADD CONSTRAINT FK_1202D7A59E0F0B83 FOREIGN KEY (preferenced_position_id) REFERENCES ys_job_levels (id)');
        $this->addSql('CREATE INDEX IDX_1202D7A59E0F0B83 ON ys_job_seekers (preferenced_position_id)');
    }
}
