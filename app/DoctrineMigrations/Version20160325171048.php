<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160325171048 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
		/**
		 * Creates Org_Cohort_Name table to allow for future custom cohort names and allow for cohort name extensibility.
		 * Includes Columns for organization_id, cohort, cohort_name.  It also populates cohorts 1, 2, 3, 4 into
		 * cohort name with default values of Survey Cohort #
		 *
		 */
        $this->addSQL("CREATE TABLE `synapse`.`org_cohort_name` (
					  `id` INT(11) NOT NULL AUTO_INCREMENT,
					  `organization_id` INT(11) NOT NULL,
					  `org_academic_year_id` INT(11) NOT NULL,
					  `cohort` INT(11) NOT NULL,
					  `cohort_name` VARCHAR(255) NULL,
					  `created_by` INT(11) NULL,
					  `modified_by` INT(11) NULL,
					  `deleted_by` INT(11) NULL,
					  `created_at` DATETIME NULL,
					  `modified_at` DATETIME NULL,
					  `deleted_at` DATETIME NULL,
					  PRIMARY KEY (`id`),
					  INDEX `fk_organization_id_idx` (`organization_id` ASC),
					  INDEX `fk_org_academic_year_id_idx` (`org_academic_year_id` ASC),
					  UNIQUE INDEX `unique_cohort` (`organization_id` ASC, `org_academic_year_id` ASC, `cohort` ASC),
					  CONSTRAINT `fk_organization_id`
					    FOREIGN KEY (`organization_id`)
					    REFERENCES `synapse`.`organization` (`id`)
					    ON DELETE NO ACTION
					    ON UPDATE NO ACTION,
					  CONSTRAINT `fk_org_academic_year_id`
					    FOREIGN KEY (`org_academic_year_id`)
					    REFERENCES `synapse`.`org_academic_year` (`id`)
					    ON DELETE NO ACTION
					    ON UPDATE NO ACTION);");

		$this->addSQL("INSERT INTO org_cohort_name(organization_id, org_academic_year_id, cohort, cohort_name, created_at, modified_at, created_by, modified_by)
						SELECT organization_id, org_academic_year_id, cohort, list_value AS cohort_name, NOW(), NOW(), -6, -6 
						FROM 
							(SELECT oay.organization_id, oay.id as org_academic_year_id, cohort_list.cohort
							FROM org_academic_year oay
                            CROSS JOIN
								(SELECT 1 AS cohort UNION SELECT 2 AS cohort UNION SELECT 3 AS cohort UNION SELECT 4 AS cohort) AS cohort_list
							WHERE oay.deleted_at IS NULL
							)
							AS cohorts
						INNER JOIN
							(SELECT list_name, list_value 
							FROM ebi_metadata_list_values
							WHERE ebi_metadata_id IN 
								(SELECT id FROM ebi_metadata WHERE meta_key = 'Cohort Names'))
							AS cohortNames
						 ON cohortNames.list_name = cohorts.cohort;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSQL('DROP TABLE `synapse`.`org_cohort_name`');

    }
}
