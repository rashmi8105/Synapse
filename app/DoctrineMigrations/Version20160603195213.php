<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Migration Script moves person_factor_calculated table to legacy
 * and creates new person_factor_calculated that no longer takes
 * history, but replaces the old information on update.
 *
 * Old information needed is also migrated from person_factor_
 * calculated_legacy to person_factor_calculated(new table)
 * 
 */
class Version20160603195213 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSQL('ALTER EVENT Survey_Risk_Event disable;');
        $this->addSQL('RENAME TABLE person_factor_calculated TO person_factor_calculated_legacy;');
        $this->addSQL('CREATE TABLE person_factor_calculated (
            id INT AUTO_INCREMENT NOT NULL, 
            organization_id INT NOT NULL, 
            person_id INT NOT NULL, 
            survey_id INT NOT NULL,
            factor_id INT NOT NULL, 
            mean_value NUMERIC(13, 4) NOT NULL, 
            created_at DATETIME DEFAULT NULL, 
            modified_at DATETIME DEFAULT NULL, 
            deleted_at DATETIME DEFAULT NULL, 
            created_by INT DEFAULT NULL, 
            modified_by INT DEFAULT NULL, 
            deleted_by INT DEFAULT NULL,
            PRIMARY KEY (id),
            CONSTRAINT fk_pfc_organization_id FOREIGN KEY (organization_id) REFERENCES synapse.organization (id),
            CONSTRAINT fk_pfc_person_id FOREIGN KEY (person_id) REFERENCES synapse.person (id),
            CONSTRAINT fk_pfc_survey_id FOREIGN KEY (survey_id) REFERENCES synapse.survey (id),
            CONSTRAINT fk_pfc_id FOREIGN KEY (factor_id) REFERENCES synapse.factor (id),
            CONSTRAINT fk_pfc_created_by FOREIGN KEY (created_by) REFERENCES synapse.person (id),
            CONSTRAINT fk_pfc_modified_by FOREIGN KEY (modified_by) REFERENCES synapse.person (id),
            CONSTRAINT fk_pfc_deleted_by FOREIGN KEY (deleted_by) REFERENCES synapse.person (id), 
            UNIQUE INDEX pfc_unique (organization_id, person_id, survey_id, factor_id)
        );');

        $this->addSQL('INSERT INTO person_factor_calculated (organization_id, person_id, survey_id, factor_id, mean_value, created_at, modified_at, deleted_at, created_by, modified_by, deleted_by)
            SELECT pfch1.organization_id, pfch1.person_id, pfch1.survey_id, pfch1.factor_id, pfch1.mean_value, pfch1.created_at, pfch1.modified_at, pfch1.deleted_at, pfch1.created_by, pfch1.modified_by, pfch1.deleted_by
            FROM person_factor_calculated_legacy pfch1
            INNER JOIN
            (
                SELECT person_id, factor_id, survey_id, MAX(modified_at) AS modified_at
                FROM person_factor_calculated_legacy
                WHERE deleted_at IS NULL
                GROUP BY person_id, factor_id, survey_id
            ) AS pfch2
                ON pfch1.person_id = pfch2.person_id
                AND pfch1.factor_id = pfch2.factor_id
                AND pfch1.survey_id = pfch2.survey_id
                AND pfch1.modified_at = pfch2.modified_at;');
        $this->addSQL('ALTER EVENT Survey_Risk_Event enable;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSQL('DROP TABLE person_factor_calculated;');
        $this->addSQL('RENAME TABLE person_factor_calculated_legacy TO person_factor_calculated;');
        

    }
}
