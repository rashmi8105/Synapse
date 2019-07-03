<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-15425 - migration script to perform database changes in "reports" table and create a new table "report_view"
 */
class Version20170810055508 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("DROP TABLE IF EXISTS synapse.report_view");
        $this->addSql("CREATE TABLE synapse.report_view (
                            id INT(11) NOT NULL AUTO_INCREMENT,
                            view_name VARCHAR(100) NOT NULL,
                            created_at DATETIME DEFAULT NULL,
                            created_by INT(11) DEFAULT NULL,
                            modified_at DATETIME DEFAULT NULL,
                            modified_by INT(11) DEFAULT NULL,
                            deleted_at DATETIME DEFAULT NULL,
                            deleted_by INT(11) DEFAULT NULL,
                            PRIMARY KEY (id),
                            UNIQUE KEY view_name (view_name)
                    );"
        );
        $this->addSql("ALTER TABLE synapse.reports ADD report_view_id int(11) default NULL AFTER is_active;");
        $this->addSql("ALTER TABLE synapse.reports ADD CONSTRAINT fk_report_view_id FOREIGN KEY (report_view_id) REFERENCES report_view(id);");
        $this->addSql("ALTER TABLE synapse.reports ADD CONSTRAINT FK_22555508DE12AB01 FOREIGN KEY (created_by) REFERENCES person (id)");
        $this->addSql("ALTER TABLE synapse.reports ADD CONSTRAINT FK_22555508DE12CD02 FOREIGN KEY (modified_by) REFERENCES person (id)");
        $this->addSql("ALTER TABLE synapse.reports ADD CONSTRAINT FK_22555508DE12EF03 FOREIGN KEY (deleted_by) REFERENCES person (id)");
        // insert data to report view table
        $this->addSql("INSERT INTO `report_view` (`view_name`, `created_at`, `created_by`, `modified_at`, `modified_by`, `deleted_at`, `deleted_by`) VALUES ('Activity', NOW(), -25, NOW(), -25, NULL, NULL);");
        $this->addSql("INSERT INTO `report_view` (`view_name`, `created_at`, `created_by`, `modified_at`, `modified_by`, `deleted_at`, `deleted_by`) VALUES ('Outcomes', NOW(), -25, NOW(), -25, NULL, NULL);");
        $this->addSql("INSERT INTO `report_view` (`view_name`, `created_at`, `created_by`, `modified_at`, `modified_by`, `deleted_at`, `deleted_by`) VALUES ('Survey and Profile', NOW(), -25, NOW(), -25, NULL, NULL);");
        // update report view id in table report
        $this->addSql("UPDATE `reports` set report_view_id=1 WHERE reports.name IN('Executive Summary Report','Our Mapworks Activity','All Academic Updates Report','Faculty/Staff Usage Report');");
        $this->addSql("UPDATE `reports` set report_view_id=2 WHERE reports.name IN('Completion Report','Persistence and Retention Report','GPA Report','Compare');");
        $this->addSql("UPDATE `reports` set report_view_id=3 WHERE reports.name IN('Our Students Report','Survey Snapshot Report','Survey Factors Report','Group Response Report','Profile Snapshot Report','Individual Response Report');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("ALTER TABLE synapse.reports DROP FOREIGN KEY fk_report_view_id");
        $this->addSql("ALTER TABLE synapse.reports DROP FOREIGN KEY FK_22555508DE12AB01");
        $this->addSql("ALTER TABLE synapse.reports DROP FOREIGN KEY FK_22555508DE12CD02");
        $this->addSql("ALTER TABLE synapse.reports DROP FOREIGN KEY FK_22555508DE12EF03");
        $this->addSql("ALTER TABLE synapse.reports DROP report_view_id");
        $this->addSql("DROP TABLE IF EXISTS synapse.report_view");
    }
}
