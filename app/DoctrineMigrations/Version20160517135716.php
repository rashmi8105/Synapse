<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Creates new success-marker-related tables needed for the Student Survey Dashboard redesign.
 * Note the tables are dropped in reverse order of their creation to prevent issues with foreign key checks.
 */
class Version20160517135716 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_color;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_topic_detail_color;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_topic_representative;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_topic_detail;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_topic;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker;');

        $this->addSql('CREATE TABLE synapse.success_marker (
                            id smallint NOT NULL AUTO_INCREMENT,
                            `name` varchar(64) NOT NULL,
                            sequence smallint NOT NULL,
                            needs_color_calculated tinyint(1) NOT NULL,
                            created_at datetime DEFAULT NULL,
                            modified_at datetime DEFAULT NULL,
                            deleted_at datetime DEFAULT NULL,
                            created_by int DEFAULT NULL,
                            modified_by int DEFAULT NULL,
                            deleted_by int DEFAULT NULL,
                            PRIMARY KEY (id),
                            CONSTRAINT fk_success_marker_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id)
                        );');

        $this->addSql('CREATE TABLE synapse.success_marker_topic (
                            id smallint NOT NULL AUTO_INCREMENT,
                            `name` varchar(64) NOT NULL,
                            success_marker_id smallint NOT NULL,
                            created_at datetime DEFAULT NULL,
                            modified_at datetime DEFAULT NULL,
                            deleted_at datetime DEFAULT NULL,
                            created_by int DEFAULT NULL,
                            modified_by int DEFAULT NULL,
                            deleted_by int DEFAULT NULL,
                            PRIMARY KEY (id),
                            CONSTRAINT fk_success_marker_topic_success_marker_id FOREIGN KEY (success_marker_id) REFERENCES synapse.success_marker (id),
                            CONSTRAINT fk_success_marker_topic_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_topic_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_topic_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id)
                        );');

        $this->addSql('CREATE TABLE synapse.success_marker_topic_detail (
                            id smallint NOT NULL AUTO_INCREMENT,
                            topic_id smallint NOT NULL,
                            factor_id int DEFAULT NULL,
                            ebi_question_id int DEFAULT NULL,
                            created_at datetime DEFAULT NULL,
                            modified_at datetime DEFAULT NULL,
                            deleted_at datetime DEFAULT NULL,
                            created_by int DEFAULT NULL,
                            modified_by int DEFAULT NULL,
                            deleted_by int DEFAULT NULL,
                            PRIMARY KEY (id),
                            CONSTRAINT fk_success_marker_topic_detail_topic_id FOREIGN KEY (topic_id) REFERENCES synapse.success_marker_topic (id),
                            CONSTRAINT fk_success_marker_topic_detail_factor_id FOREIGN KEY (factor_id) REFERENCES synapse.factor (id),
                            CONSTRAINT fk_success_marker_topic_detail_ebi_question_id FOREIGN KEY (ebi_question_id) REFERENCES synapse.ebi_question (id),
                            CONSTRAINT fk_success_marker_topic_detail_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_topic_detail_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_topic_detail_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id)
                        );');

        $this->addSql('CREATE TABLE synapse.success_marker_topic_representative (
                            id smallint NOT NULL AUTO_INCREMENT,
                            topic_id smallint NOT NULL,
                            representative_detail_id smallint NOT NULL,
                            created_at datetime DEFAULT NULL,
                            modified_at datetime DEFAULT NULL,
                            deleted_at datetime DEFAULT NULL,
                            created_by int DEFAULT NULL,
                            modified_by int DEFAULT NULL,
                            deleted_by int DEFAULT NULL,
                            PRIMARY KEY (id),
                            CONSTRAINT fk_success_marker_topic_representative_topic_id FOREIGN KEY (topic_id) REFERENCES synapse.success_marker_topic (id),
                            CONSTRAINT fk_success_marker_topic_representative_detail_id FOREIGN KEY (representative_detail_id) REFERENCES synapse.success_marker_topic_detail (id),
                            CONSTRAINT fk_success_marker_topic_representative_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_topic_representative_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_topic_representative_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id),
                            UNIQUE INDEX smtr_unique (topic_id)
                        );');

        $this->addSql('CREATE TABLE synapse.success_marker_topic_detail_color (
                            id smallint NOT NULL AUTO_INCREMENT,
                            topic_detail_id smallint NOT NULL,
                            color enum("red", "yellow", "green") NOT NULL,
                            min_value decimal(6,3) NOT NULL,
                            max_value decimal(6,3) NOT NULL,
                            created_at datetime DEFAULT NULL,
                            modified_at datetime DEFAULT NULL,
                            deleted_at datetime DEFAULT NULL,
                            created_by int DEFAULT NULL,
                            modified_by int DEFAULT NULL,
                            deleted_by int DEFAULT NULL,
                            PRIMARY KEY (id),
                            CONSTRAINT fk_success_marker_topic_detail_color_topic_detail_id FOREIGN KEY (topic_detail_id) REFERENCES synapse.success_marker_topic_detail (id),
                            CONSTRAINT fk_success_marker_topic_detail_color_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_topic_detail_color_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_topic_detail_color_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id)
                        );');

        $this->addSql('CREATE TABLE synapse.success_marker_color (
                            id smallint NOT NULL AUTO_INCREMENT,
                            color enum("red", "yellow", "green") NOT NULL,
                            base_value smallint NOT NULL,
                            min_value decimal(6,3) NOT NULL,
                            max_value decimal(6,3) NOT NULL,
                            created_at datetime DEFAULT NULL,
                            modified_at datetime DEFAULT NULL,
                            deleted_at datetime DEFAULT NULL,
                            created_by int DEFAULT NULL,
                            modified_by int DEFAULT NULL,
                            deleted_by int DEFAULT NULL,
                            PRIMARY KEY (id),
                            CONSTRAINT fk_success_marker_color_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_color_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
                            CONSTRAINT fk_success_marker_color_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id)
                        );');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_color;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_topic_detail_color;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_topic_representative;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_topic_detail;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker_topic;');

        $this->addSql('DROP TABLE IF EXISTS synapse.success_marker;');
    }
}
