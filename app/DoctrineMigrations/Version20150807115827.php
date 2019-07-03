<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150807115827 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $personId = '$$personId$$';
        $ebi_query = <<<CDATA
UPDATE ebi_search SET query='SELECT
    	RL.id AS risk_level,
    	count(DISTINCT P.id) as totalStudentsHighPriority,
        RL.risk_text,
        RL.image_name,
        RL.color_hex
    FROM risk_level AS RL
    INNER JOIN person AS P
    	ON P.risk_level=RL.id
        AND P.deleted_at IS NULL
    INNER JOIN
    (
    		#-- Associated group with this advisor
    		SELECT
    			S.person_id AS person_id,
                #--F.person_id AS faculty_id, #--removed due to pushdown
    			F.org_permissionset_id AS permissionset_id
    		FROM org_group_students AS S
    		INNER JOIN org_group_faculty AS F
    			ON F.org_group_id = S.org_group_id
    			and F.deleted_at is null
    		WHERE
    			S.deleted_at is null
                AND F.person_id=$personId #--We manually push down the faculty criteria for performance
        
    		UNION ALL
        
    		#-- Associated course with this advisor
    		SELECT
    			S.person_id AS student_id,
                #--F.person_id AS faculty_id, #--removed due to pushdown
                F.org_permissionset_id AS permissionset_id
    		FROM org_course_student AS S
    		INNER JOIN org_courses AS C
    			ON C.id = S.org_courses_id
    			AND C.deleted_at is null
    		INNER JOIN org_course_faculty AS F
    			ON F.org_courses_id = S.org_courses_id
    			AND F.deleted_at is null
    		INNER JOIN org_academic_terms AS OAT
    			ON OAT.id = C.org_academic_terms_id
    			AND OAT.end_date >= now()
    			AND OAT.deleted_at is null
    		WHERE
    			S.deleted_at is null
    			AND F.person_id=$personId #--We manually push down the faculty criteria for performance
    ) AS merged
    	ON merged.person_id=P.id
    INNER JOIN org_permissionset OPS
    	ON merged.permissionset_id = OPS.id
    	AND (OPS.accesslevel_ind_agg = 1 or OPS.accesslevel_agg = 1)
        AND OPS.risk_indicator = 1
    GROUP BY RL.id'
WHERE
    query_key = 'My_Total_Students_Count_Groupby_Risk';
CDATA;
        $this->addSql($ebi_query);        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
