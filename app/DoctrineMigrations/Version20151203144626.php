<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151203144626 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('CREATE OR REPLACE 
		ALGORITHM = MERGE
		DEFINER = `synapsemaster`@`%` 
		SQL SECURITY DEFINER
		VIEW org_group_faculty_student_permission_map AS
		SELECT 
			OG.id AS group_id, 
	        OG.organization_id AS org_id, 
	        OGF.person_id as faculty_id, 
	        OGS.person_id AS student_id, 
	        OGF.org_permissionset_id AS permissionset_id
		FROM org_group_faculty AS OGF FORCE INDEX (`PG_perm`)#--, `person-group`)
	    INNER JOIN org_group AS OG
			ON OG.id = OGF.org_group_id
			AND OG.organization_id = OGF.organization_id
			AND OG.deleted_at IS NULL
		INNER JOIN org_group_students AS OGS FORCE INDEX (`group-student`, `student-group`)
			ON OGS.org_group_id = OG.id
			AND OGS.organization_id = OG.organization_id
			AND OGS.deleted_at IS NULL
		WHERE OGF.deleted_at IS NULL
		;');

		$this->addSQL('CREATE OR REPLACE 
		ALGORITHM = MERGE
		DEFINER = `synapsemaster`@`%` 
		SQL SECURITY DEFINER
		VIEW org_course_faculty_student_permission_map AS
		SELECT #--STRAIGHT_JOIN
			OC.id AS course_id, 
	        OC.organization_id AS org_id, 
	        OCF.person_id as faculty_id, 
	        OCS.person_id AS student_id, 
	        OCF.org_permissionset_id AS permissionset_id
		FROM org_course_faculty AS OCF USE INDEX (`person-course`)
	    INNER JOIN org_courses AS OC #--USE INDEX (PRIMARY)
			ON OC.id = OCF.org_courses_id
			AND OC.organization_id = OCF.organization_id
			AND OC.deleted_at IS NULL
		INNER JOIN org_academic_terms AS OAT FORCE INDEX (last_term, PRIMARY)
			ON OAT.id = OC.org_academic_terms_id
			AND OAT.organization_id = OC.organization_id
			AND OAT.end_date >= DATE(now())
			AND OAT.start_date <= DATE(now())
			AND OAT.deleted_at IS NULL
		INNER JOIN org_course_student AS OCS FORCE INDEX (`course-person`, `person-course`)
			ON OCS.org_courses_id = OC.id
			AND OCS.organization_id = OC.organization_id
			AND OCS.deleted_at IS NULL
		WHERE OCF.deleted_at IS NULL
		;');

		$this->addSQL("CREATE OR REPLACE
		ALGORITHM = MERGE
		DEFINER = `synapsemaster`@`%`
		SQL SECURITY DEFINER
		VIEW `group_course_discriminator` AS
	    SELECT 'group' AS association UNION SELECT 'course' AS association;");

	    $this->addSQL('CREATE OR REPLACE
		DEFINER = `synapsemaster`@`%` 
		SQL SECURITY DEFINER
		VIEW `org_faculty_student_permission_map` AS
		SELECT
			OPF.organization_id AS org_id,
			OPF.person_id AS faculty_id,
			COALESCE(OGM.student_id, OCM.student_id) AS student_id,
	        OGM.group_id,
	        OCM.course_id,
	        COALESCE(OGM.permissionset_id, OCM.permissionset_id) AS permissionset_id
		FROM org_person_faculty AS OPF
	    CROSS JOIN group_course_discriminator AS GCD
		LEFT JOIN org_group_faculty_student_permission_map AS OGM
			ON (OGM.org_id, OGM.faculty_id)=(OPF.organization_id, OPF.person_id)
	        AND GCD.association = \'group\'
		LEFT JOIN org_course_faculty_student_permission_map AS OCM
			ON (OCM.org_id, OCM.faculty_id)=(OPF.organization_id, OPF.person_id)
			AND GCD.association = \'course\'
		WHERE
	        (OGM.group_id IS NOT NULL OR OCM.course_id IS NOT NULL)
			AND OPF.deleted_at IS NULL
		;');

		$this->addSQL("CREATE OR REPLACE 
		ALGORITHM = MERGE
		DEFINER = `synapsemaster`@`%` 
		SQL SECURITY DEFINER
		VIEW `Issues_Survey_Questions` AS
		SELECT 
				sr.org_id,
		        sr.person_id as student_id,
				opssl.survey_id,
				iss.id AS issue_id,
				opssl.cohort as cohort,
				sq.id as survey_question_id,
				sq.ebi_question_id,
				ISFS.faculty_id AS faculty_id,
		        sr.decimal_value AS permitted_value,
		        sr.modified_at
		        
			FROM org_faculty_student_permission_map ISFS
		    INNER JOIN org_person_student_survey_link opssl On ISFS.org_id = opssl.org_id AND opssl.person_id = ISFS.student_id
		    INNER JOIN survey_response sr FORCE INDEX (`fk_survey_response_organization1`) ON ISFS.student_id = sr.person_id
		        AND ISFS.org_id = sr.org_id and opssl.survey_id = sr.survey_id And sr.deleted_at is null
		    INNER JOIN issue AS iss ON iss.survey_questions_id = sr.survey_questions_id AND iss.survey_id = sr.survey_id AND iss.deleted_at is null
		    INNER JOIN survey_questions sq  ON sr.survey_questions_id = sq.id and sq.survey_id = sr.survey_id AND sq.deleted_at is null
		    INNER JOIN ebi_question eq ON sq.ebi_question_id = eq.id and eq.deleted_at is null
		    INNER JOIN datablock_questions AS dq USE INDEX (`permfunc`) On dq.ebi_question_id = eq.id and dq.deleted_at is null
			INNER JOIN org_permissionset_datablock AS opd
				ON opd.organization_id = sr.org_id 
				AND opd.datablock_id = dq.datablock_id
				AND opd.org_permissionset_id = ISFS.permissionset_id
				AND opd.deleted_at is null
		    INNER JOIN wess_link as wl On wl.survey_id = opssl.survey_id AND opssl.cohort = wl.cohort_code AND wl.org_id = sr.org_id And wl.status = 'closed';");

		$this->addSQL("CREATE OR REPLACE 
	    ALGORITHM = MERGE
	    DEFINER = `synapsemaster`@`%` 
	    SQL SECURITY DEFINER
		VIEW `Issues_Factors` AS
		    SELECT 
					pfc.organization_id as org_id,
		            pfc.person_id as student_id,
		            pfc.survey_id,
		            iss.id AS issue_id,
		            opssl.cohort AS cohort,
		            pfc.factor_id AS factor_id,
		            ISFS.faculty_id AS faculty_id,
		            pfc.mean_value AS permitted_value,
		            pfc.modified_at
		         
		    
			FROM
				org_faculty_student_permission_map ISFS
			INNER JOIN org_person_student_survey_link opssl On opssl.org_id = ISFS.org_id AND opssl.person_id = ISFS.student_id 
		    INNER JOIN person_factor_calculated pfc ON ISFS.student_id = pfc.person_id
		        AND ISFS.org_id = pfc.organization_id and opssl.survey_id = pfc.survey_id and pfc.deleted_at is null
		    INNER JOIN issue AS iss ON iss.factor_id = pfc.factor_id AND iss.survey_id = pfc.survey_id and iss.deleted_at is null
			INNER JOIN wess_link as wl On wl.survey_id = pfc.survey_id and wl.org_id = pfc.organization_id and wl.cohort_code = opssl.cohort And wl.status = 'closed' 
			INNER JOIN datablock_questions AS dq On dq.factor_id = pfc.factor_id AND dq.deleted_at is null
			INNER JOIN org_permissionset_datablock AS opd
				ON opd.organization_id = pfc.organization_id 
				AND opd.datablock_id = dq.datablock_id
				AND opd.org_permissionset_id = ISFS.permissionset_id
				AND opd.deleted_at IS NULL
		    WHERE 
				pfc.id = (
					select id from person_factor_calculated as fc
					where
						fc.organization_id = pfc.organization_id 
						AND fc.person_id = pfc.person_id 
						AND fc.factor_id = pfc.factor_id 
						AND fc.survey_id = pfc.survey_id 
						ORDER BY modified_at DESC LIMIT 1
				)
		;");

		$this->addSQL("CREATE OR REPLACE 
		ALGORITHM = MERGE
		DEFINER = `synapsemaster`@`%` 
		SQL SECURITY DEFINER
		VIEW `Factor_Question_Constants` AS
	    SELECT 'Factor' AS datum_type UNION SELECT 'Question' AS datum_type;");

	    $this->addSQL("CREATE OR REPLACE 
			DEFINER = `synapsemaster`@`%` 
			SQL SECURITY DEFINER
			VIEW `Issues_Datum` AS
		select 
			ofs.organization_id as org_id,
			ofs.person_id as faculty_id,
			COALESCE(ISQ.survey_id, ISF.survey_id) as survey_id,
			COALESCE(ISQ.student_id, ISF.student_id) as student_id,
			COALESCE(ISQ.issue_id, ISF.issue_id) as issue_id,
			COALESCE(ISQ.cohort, ISF.cohort) as cohort,
			CU.datum_type as type,
			COALESCE(ISQ.survey_question_id, ISF.factor_id) as source_id,
			COALESCE(ISQ.permitted_value, ISF.permitted_value) as source_value, #--placeholder
			COALESCE(ISQ.modified_at, ISF.modified_at) as modified_at
		FROM org_person_faculty ofs
		CROSS JOIN Factor_Question_Constants AS CU
		LEFT JOIN Issues_Survey_Questions ISQ 
			On CU.datum_type='Question'
			AND ofs.person_id = ISQ.faculty_id 
			AND ofs.organization_id = ISQ.org_id 
		LEFT JOIN Issues_Factors ISF 
			On CU.datum_type='Factor'
			AND ofs.person_id = ISF.faculty_id 
			AND ofs.organization_id = ISF.org_id 
		WHERE (ISQ.permitted_value is not null OR ISF.permitted_value is not null) 
		;");

		$this->addSQL("CREATE OR REPLACE 
		  ALGORITHM = UNDEFINED 
		  DEFINER = `synapsemaster`@`%` 
		  SQL SECURITY DEFINER
		 VIEW `Issues_Calculation` AS 
		    select 
		 theID.org_id,
		 theID.faculty_id,
		    theID.survey_id,
		    theID.issue_id,
		    theID.cohort,
		    theID.student_id,
		    IFNULL((theID.source_value between iss.min and iss.max OR CAST(theID.source_value as unsigned) = eqo.option_value),0) as has_issue,
		    issl.name as name,
		    iss.icon as icon
		    FROM
		  Issues_Datum theID
		    INNER JOIN issue iss On iss.id = theID.issue_id
		    LEFT JOIN issue_lang issl On iss.id = issl.issue_id
		    LEFT JOIN issue_options issO On iss.id = issO.issue_id
		    LEFT JOIN ebi_question_options eqo On eqo.id = issO.ebi_question_options_id
		;
		");

		
		$this->addSQL("update issue iss SET iss.icon = 'large-report-icon-study.png' WHERE iss.id in (1, 9, 28, 32, 36, 42, 62, 82)");
		$this->addSQL("update issue iss SET iss.icon = 'large-report-icon-academic.png' WHERE iss.id in (6,7,8,11,12,13,14,15,19,26,27,40,41,44,45,46,47,48,51,58,59,60,61,64,65,66,67,68,71,78,79,80,81,84,85,86,87,88,91,98,99)");
		$this->addSQL("update issue iss SET iss.icon = 'large-report-icon-courses.png' WHERE iss.id in (2,5,29,33,37);");
		$this->addSQL("update issue iss SET iss.icon = 'large-report-icon-finances.png' WHERE iss.id in (10,43,63,83);");
		$this->addSQL("update issue iss SET iss.icon = 'large-report-icon-homesick.png' WHERE iss.id in (4,16,17,18,20,21,22,23,24,25,31,35,39,49,50,52,53,54,55,56,57,69,70,72,73,74,75,76,77,89,90,92,93,94,95,96,97);");
		$this->addSQL("update issue iss SET iss.icon = 'large-report-icon-missedclasses.png' WHERE iss.id in (3,30,34,38);");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');


    }
}
