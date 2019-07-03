<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script for ESPRJ-9313 - modification to Student_Data_Dump Sp for getting cohort value from org_person_student_cohort table  
 */
class Version20160322105325 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        
        $this->addSql('DROP procedure IF EXISTS `Student_Data_Dump`;');
        $sp = <<<HEREDOC
DROP procedure IF EXISTS `Student_Data_Dump`;

CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Student_Data_Dump`(
IN orgId int(11),
IN slimit int(11),
IN soffset int(11)
)
BEGIN
    SET group_concat_max_len = 1000000;
    DROP TEMPORARY TABLE IF EXISTS temppivot;

    DROP TEMPORARY TABLE IF EXISTS listofmetacols;
    CREATE TEMPORARY TABLE listofmetacols (`meta_key` VARCHAR(50) NOT NULL, `em_id` int(11) NULL, `om_id` int(11) NULL, PRIMARY KEY (`meta_key`));
    DROP TEMPORARY TABLE IF EXISTS listofpeeps;
    CREATE TEMPORARY TABLE listofpeeps (`person_id` int(11) NOT NULL,PRIMARY KEY (`person_id`));

    #--This can populate first without actually having panaramic photo errors
    INSERT INTO listofmetacols
        SELECT    meta_key, em.id AS em_id, NULL AS om_id
        FROM      ebi_metadata em
        WHERE     deleted_at IS NULL
        UNION ALL
        SELECT    meta_key, NULL AS em_id, om.id AS om_id
        FROM      org_metadata om
        WHERE     organization_id = orgId and deleted_at IS NULL
    ;

    #--This can also populate first without actually having panaramic photo errors
    INSERT IGNORE  INTO listofpeeps
    (
    select op.person_id AS person_id
    from org_person_student op
    where op.organization_id = orgId AND op.deleted_at IS NULL
    limit slimit offset soffset
    );

    SET @tempsql = NULL;
    SELECT CONCAT("CREATE TEMPORARY TABLE `temppivot` (`person_id` int(11) NOT NULL,`year_id` CHAR(6) NOT NULL,`term_id` varchar(12) NOT NULL,", Group_concat("`", meta_key, "` text COLLATE utf8_unicode_ci DEFAULT NULL"), ",PRIMARY KEY (`person_id`, `year_id`, `term_id`))")
    INTO   @tempsql
    FROM   listofmetacols AS md;
    PREPARE stmt FROM @tempsql;
    EXECUTE stmt;

    START TRANSACTION READ ONLY;

    SELECT Group_concat(DISTINCT Concat( '
        MAX(IF(md.meta_key = ''', meta_key, ''', md.metadata_value, "")) AS `', meta_key , '`' ) )
    INTO   @sql
    FROM listofmetacols AS md;
    SET @sql = concat('INSERT INTO `temppivot` SELECT person_id, year_id, term_id, ',
    @sql, '
    FROM (
    (SELECT STRAIGHT_JOIN
        LP.person_id,
        IFNULL(year_id,"") AS year_id,
        IFNULL(term_code,"") AS term_id,
        LM.meta_key,
        IFNULL(COALESCE(pem.metadata_value, pom.metadata_value),"") AS metadata_value
    #--Construct the base product
    FROM listofpeeps AS LP
    CROSS JOIN listofmetacols AS LM
    #--Attach EBI metadata
    LEFT JOIN person_ebi_metadata AS pem
        ON pem.person_id = LP.person_id
        AND pem.ebi_metadata_id = LM.em_id
    #--Attach ORG metadata
    LEFT JOIN person_org_metadata AS pom
        ON pom.person_id = LP.person_id
        AND pom.org_metadata_id = LM.om_id
    #--Add bonus data
    LEFT JOIN org_academic_year AS oay
        ON oay.id = COALESCE(pem.org_academic_year_id, pom.org_academic_year_id)
    LEFT JOIN org_academic_terms AS oat
        ON oat.id = COALESCE(pem.org_academic_terms_id, pom.org_academic_periods_id)
    #--Order it...on second thought...order last so we can use names
    #--ORDER BY person_id, oay.year_id, oat.term_code ASC
	)
	union
		( select person_id ,oay.year_id,"" as term_id,"" as meta_key,"" as meta_val from org_person_student_cohort as opc
left join org_academic_year as oay ON opc.org_academic_year_id = oay.id )
    )
    AS
      md GROUP BY 1,2,3 #--the numbers designate columns in the result'
    );


    PREPARE stmt FROM @sql;
    EXECUTE stmt;

 
    
    SET @metalist = NULL;
    SELECT CONCAT("ExternalId,AuthUsername,Firstname,Lastname,Title,RecordType,StudentPhoto,IsActive,SurveyCohort,ReceiveSurvey,YearId,TermId,PrimaryConnect,RiskGroupId,StudentAuthKey,Address1,Address2,City,State,Country,Zip,PrimaryMobile,AlternateMobile,HomePhone,PrimaryEmail,AlternateEmail,PrimaryMobileProvider,AlternateMobileProvider,", Group_concat("`", meta_key, "`"))
    INTO   @metalist
    FROM   listofmetacols AS md;

    SET @resultsql = CONCAT('
    SELECT ',@metalist,' FROM (SELECT
        IFNULL(p.external_id,"") AS ExternalId,
        IFNULL(p.auth_username,"") AS AuthUsername,
        IFNULL(p.firstname,"") AS Firstname,
        IFNULL(p.lastname,"") AS Lastname,
        IFNULL(pinfo.title,"") AS Title,
        IFNULL(pinfo.record_type,"") AS RecordType,
        IFNULL(op.photo_url, "") AS StudentPhoto,
        IFNULL(NULLIF(op.status, 0), "") AS IsActive,
        IFNULL(NULLIF(opsc.cohort, 0), "") AS SurveyCohort,
        IFNULL(NULLIF(op.receivesurvey, 0), "") AS ReceiveSurvey,
        innie.year_id AS YearId,
        innie.term_id AS TermId,
        IFNULL(pc.external_id, "") AS PrimaryConnect,
        IFNULL(rh.risk_group_id, "") AS RiskGroupId,
        "" AS StudentAuthKey,
        IFNULL(ci.address_1,"") AS Address1,
        IFNULL(ci.address_2,"") AS Address2,
        IFNULL(ci.city,"") AS City,
        IFNULL(ci.state,"") AS State,
        IFNULL(ci.country,"") AS Country,
        IFNULL(ci.zip,"") AS Zip,
        IFNULL(ci.primary_mobile,"") AS PrimaryMobile,
        IFNULL(ci.alternate_mobile,"") AS AlternateMobile,
        IFNULL(ci.home_phone,"") AS HomePhone,
        IFNULL(cinfo.primary_email,"") AS PrimaryEmail,
        IFNULL(ci.alternate_email,"") AS AlternateEmail,
        IFNULL(ci.primary_mobile_provider,"") AS PrimaryMobileProvider,
        IFNULL(ci.alternate_mobile_provider,"") AS AlternateMobileProvider,
        innie.*
    from listofpeeps l
     join person p on p.id = l.person_id
     join temppivot AS innie on innie.person_id = p.id
     join org_person_student op on op.person_id = l.person_id

	 left join org_academic_year as oay on ((innie.year_id = oay.year_id) and (oay.organization_id = op.organization_id ))
	 left join org_person_student_cohort as opsc ON ((p.id  = opsc.person_id) and (p.organization_id =  opsc.organization_id) and (oay.id = opsc.org_academic_year_id ) and (innie.term_id = ""))

     left join person pinfo on pinfo.id = innie.person_id AND innie.year_id="" AND innie.term_id="" #--We do this so that some facts only appear on the non-year/non-term row
     left join person_contact_info pci on pci.person_id = pinfo.id
     left join contact_info ci on ci.id = pci.contact_id
     left join person_contact_info pcinfo on pcinfo.person_id = p.id
     left join contact_info cinfo on cinfo.id = pcinfo.contact_id
     left join person as pc on pc.id = op.person_id_primary_connect
     left join risk_group_person_history as rh on rh.person_id = (SELECT rgh.person_id FROM risk_group_person_history as rgh WHERE rgh.person_id = p.id ORDER BY rgh.assignment_date DESC LIMIT 1)
     ORDER BY p.firstname, p.lastname, innie.year_id, innie.term_id ASC
    ) AS results
    ');

    PREPARE stmt FROM @resultsql;
    EXECUTE stmt;

    COMMIT;

    SET group_concat_max_len = 1024;

END ;

HEREDOC;
        $this->addSql($sp);
    }

    /**
     *
     * @param Schema $schema            
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP procedure IF EXISTS `Student_Data_Dump`');
        
        $sp = <<<HEREDOC

        CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Student_Data_Dump`(
        IN orgId int(11),
        IN slimit int(11),
        IN soffset int(11)
        )
        BEGIN
        SET group_concat_max_len = 1000000;
        DROP TEMPORARY TABLE IF EXISTS temppivot;
        
        DROP TEMPORARY TABLE IF EXISTS listofmetacols;
        CREATE TEMPORARY TABLE listofmetacols (`meta_key` VARCHAR(50) NOT NULL, `em_id` int(11) NULL, `om_id` int(11) NULL, PRIMARY KEY (`meta_key`));
        DROP TEMPORARY TABLE IF EXISTS listofpeeps;
        CREATE TEMPORARY TABLE listofpeeps (`person_id` int(11) NOT NULL,PRIMARY KEY (`person_id`));
        
        #--This can populate first without actually having panaramic photo errors
        INSERT INTO listofmetacols
        SELECT    meta_key, em.id AS em_id, NULL AS om_id
        FROM      ebi_metadata em
        WHERE     deleted_at IS NULL
        UNION ALL
        SELECT    meta_key, NULL AS em_id, om.id AS om_id
        FROM      org_metadata om
        WHERE     organization_id = orgId and deleted_at IS NULL
        ;
        
        #--This can also populate first without actually having panaramic photo errors
        INSERT INTO listofpeeps
        (
        select op.person_id AS person_id
        from org_person_student op
        where op.organization_id = orgId AND op.deleted_at IS NULL
        limit slimit offset soffset
        );
        
        SET @tempsql = NULL;
        SELECT CONCAT("CREATE TEMPORARY TABLE `temppivot` (`person_id` int(11) NOT NULL,`year_id` CHAR(6) NOT NULL,`term_id` varchar(12) NOT NULL,", Group_concat("`", meta_key, "` text COLLATE utf8_unicode_ci DEFAULT NULL"), ",PRIMARY KEY (`person_id`, `year_id`, `term_id`))")
    INTO   @tempsql
    FROM   listofmetacols AS md;
            PREPARE stmt FROM @tempsql;
            EXECUTE stmt;
        
            START TRANSACTION READ ONLY;
        
            SELECT Group_concat(DISTINCT Concat( '
            MAX(IF(md.meta_key = ''', meta_key, ''', md.metadata_value, "")) AS `', meta_key , '`' ) )
    INTO   @sql
    FROM listofmetacols AS md;
            SET @sql = concat('INSERT INTO `temppivot` SELECT person_id, year_id, term_id, ',
    @sql, '
    FROM (
    SELECT STRAIGHT_JOIN
            LP.person_id,
        IFNULL(year_id,"") AS year_id,
        IFNULL(term_code,"") AS term_id,
        LM.meta_key,
        IFNULL(COALESCE(pem.metadata_value, pom.metadata_value),"") AS metadata_value
    #--Construct the base product
    FROM listofpeeps AS LP
    CROSS JOIN listofmetacols AS LM
    #--Attach EBI metadata
    LEFT JOIN person_ebi_metadata AS pem
        ON pem.person_id = LP.person_id
        AND pem.ebi_metadata_id = LM.em_id
    #--Attach ORG metadata
    LEFT JOIN person_org_metadata AS pom
        ON pom.person_id = LP.person_id
        AND pom.org_metadata_id = LM.om_id
    #--Add bonus data
    LEFT JOIN org_academic_year AS oay
        ON oay.id = COALESCE(pem.org_academic_year_id, pom.org_academic_year_id)
    LEFT JOIN org_academic_terms AS oat
        ON oat.id = COALESCE(pem.org_academic_terms_id, pom.org_academic_periods_id)
    #--Order it...on second thought...order last so we can use names
    #--ORDER BY person_id, oay.year_id, oat.term_code ASC
    )
    AS
      md GROUP BY 1,2,3 #--the numbers designate columns in the result'
    );
        
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
        
            SET @metalist = NULL;
            SELECT CONCAT("ExternalId,AuthUsername,Firstname,Lastname,Title,RecordType,StudentPhoto,IsActive,SurveyCohort,ReceiveSurvey,YearId,TermId,PrimaryConnect,RiskGroupId,StudentAuthKey,Address1,Address2,City,State,Country,Zip,PrimaryMobile,AlternateMobile,HomePhone,PrimaryEmail,AlternateEmail,PrimaryMobileProvider,AlternateMobileProvider,", Group_concat("`", meta_key, "`"))
    INTO   @metalist
            FROM   listofmetacols AS md;
        
            SET @resultsql = CONCAT('
            SELECT ',@metalist,' FROM (SELECT
            IFNULL(p.external_id,"") AS ExternalId,
        IFNULL(p.auth_username,"") AS AuthUsername,
        IFNULL(p.firstname,"") AS Firstname,
        IFNULL(p.lastname,"") AS Lastname,
        IFNULL(pinfo.title,"") AS Title,
        IFNULL(pinfo.record_type,"") AS RecordType,
        IFNULL(op.photo_url, "") AS StudentPhoto,
        IFNULL(NULLIF(op.status, 0), "") AS IsActive,
        IFNULL(NULLIF(op.surveycohort, 0), "") AS SurveyCohort,
        IFNULL(NULLIF(op.receivesurvey, 0), "") AS ReceiveSurvey,
        innie.year_id AS YearId,
        innie.term_id AS TermId,
        IFNULL(pc.external_id, "") AS PrimaryConnect,
        IFNULL(rh.risk_group_id, "") AS RiskGroupId,
        "" AS StudentAuthKey,
        IFNULL(ci.address_1,"") AS Address1,
        IFNULL(ci.address_2,"") AS Address2,
        IFNULL(ci.city,"") AS City,
        IFNULL(ci.state,"") AS State,
        IFNULL(ci.country,"") AS Country,
        IFNULL(ci.zip,"") AS Zip,
        IFNULL(ci.primary_mobile,"") AS PrimaryMobile,
        IFNULL(ci.alternate_mobile,"") AS AlternateMobile,
        IFNULL(ci.home_phone,"") AS HomePhone,
        IFNULL(cinfo.primary_email,"") AS PrimaryEmail,
        IFNULL(ci.alternate_email,"") AS AlternateEmail,
        IFNULL(ci.primary_mobile_provider,"") AS PrimaryMobileProvider,
        IFNULL(ci.alternate_mobile_provider,"") AS AlternateMobileProvider,
        innie.*
    from listofpeeps l
     join person p on p.id = l.person_id
     join temppivot AS innie on innie.person_id = p.id
     join org_person_student op on op.person_id = l.person_id
     left join person pinfo on pinfo.id = innie.person_id AND innie.year_id="" AND innie.term_id="" #--We do this so that some facts only appear on the non-year/non-term row
     left join person_contact_info pci on pci.person_id = pinfo.id
     left join contact_info ci on ci.id = pci.contact_id
     left join person_contact_info pcinfo on pcinfo.person_id = p.id
     left join contact_info cinfo on cinfo.id = pcinfo.contact_id
     left join person as pc on pc.id = op.person_id_primary_connect
     left join risk_group_person_history as rh on rh.person_id = (SELECT rgh.person_id FROM risk_group_person_history as rgh WHERE rgh.person_id = p.id ORDER BY rgh.assignment_date DESC LIMIT 1)
     ORDER BY p.firstname, p.lastname, innie.year_id, innie.term_id ASC
    ) AS results
    ');
        
    PREPARE stmt FROM @resultsql;
    EXECUTE stmt;
        
            COMMIT;
        
            SET group_concat_max_len = 1024;
        
            END;

HEREDOC;
        $this->addSql($sp);
    }
}
