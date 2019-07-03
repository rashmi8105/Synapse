/*
The SP is used for generating student dump for organization.
Please refer to migration file Version20180320203712.php where this SP is executed.
https://bitbucket.org/mnv_tech/synapse-backend/pull-requests/1551/320-esprj-9572-student-data-dump-sp/diff
https://bitbucket.org/mnv_tech/synapse-backend/pull-requests/1788/32-nr-esprj-10178/diff
*/

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


        DROP TEMPORARY TABLE IF EXISTS listofcols;
        CREATE TEMPORARY TABLE listofcols (`column_name` VARCHAR(50) NOT NULL);
        INSERT into listofcols
            SELECT
                upload_column_name
            FROM
                (SELECT
                     uch.upload_column_name, sort_order, 0 AS tiebreaker
                 FROM
                     upload u
                     INNER JOIN upload_column_header_download_map uchdm ON u.id = uchdm.upload_id
                     INNER JOIN upload_column_header uch ON uch.id = uchdm.upload_column_header_id
                     INNER JOIN ebi_download_type edt ON edt.id = uchdm.ebi_download_type_id
                 WHERE
                     u.upload_name = 'student'
                     AND edt.download_type = 'data_dump'
                     AND u.deleted_at IS NULL
                     AND uch.deleted_at IS NULL
                     AND edt.deleted_at IS NULL
                     AND uchdm.deleted_at IS NULL
                 UNION
                 SELECT
                    em.meta_key,
                    IFNULL(sort_order,
                                        (SELECT
                                                MAX(sort_order)
                                            FROM
                                                upload u
                                                    INNER JOIN
                                                upload_ebi_metadata_column_header_download_map uemchd ON uemchd.upload_id = u.id
                                                    INNER JOIN
                                                ebi_download_type edt ON edt.id = uemchd.ebi_download_type_id
                                            WHERE
                                                u.upload_name = 'student'
                                                    AND edt.download_type = 'data_dump'
                                                    AND u.deleted_at IS NULL
                                                    AND edt.deleted_at IS NULL
                                                    AND uemchd.deleted_at IS NULL) + em.sequence + 1) AS sort_order,
                    1
                FROM
                    ebi_metadata em
                        LEFT JOIN
                    (SELECT
                        uemchd.ebi_metadata_id, uemchd.sort_order, uemchd.id
                    FROM
                        upload u
                    INNER JOIN upload_ebi_metadata_column_header_download_map uemchd ON uemchd.upload_id = u.id
                    INNER JOIN ebi_download_type edt ON edt.id = uemchd.ebi_download_type_id
                    WHERE
                        u.upload_name = 'student'
                            AND edt.download_type = 'data_dump'
                            AND u.deleted_at IS NULL
                            AND edt.deleted_at IS NULL
                            AND uemchd.deleted_at IS NULL) AS upload_information ON em.id = upload_information.ebi_metadata_id
                            WHERE
                         em.deleted_at IS NULL
                        AND (em.status = 'active'
                        OR em.status IS NULL)

                UNION
                SELECT
                    meta_key,
                    (SELECT
                        MAX(sort_order)
                     FROM
                        upload_ebi_metadata_column_header_download_map) + (SELECT
                                                                                MAX(sequence)
                                                                            FROM
                                                                                ebi_metadata) + sequence + 2,
                    2
                FROM
                    org_metadata om
                WHERE
                    organization_id = orgId
                    AND deleted_at IS NULL
                 ORDER BY sort_order ASC , tiebreaker ASC) AS column_headers;


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
            WHERE     organization_id = orgId AND deleted_at IS NULL
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
        (SELECT STRAIGHT_JOIN
        LP.person_id,
        IFNULL(oay.year_id,"") AS year_id,
        IFNULL(oat.term_code,"") AS term_id,
        LM.meta_key,
        IFNULL(COALESCE(pem.metadata_value, pom.metadata_value),"") AS metadata_value
    #--Construct the base product
    FROM listofpeeps AS LP
    CROSS JOIN listofmetacols AS LM
    #--Attach EBI metadata
    LEFT JOIN person_ebi_metadata AS pem
        ON pem.person_id = LP.person_id
        AND pem.deleted_at IS NULL
    AND pem.ebi_metadata_id = LM.em_id
    #--Attach ORG metadata
    LEFT JOIN person_org_metadata AS pom
        ON pom.person_id = LP.person_id
        AND pom.deleted_at IS NULL
    AND pom.org_metadata_id = LM.om_id
    #--Add bonus data
    LEFT JOIN org_academic_year AS oay
        ON oay.id = COALESCE(pem.org_academic_year_id, pom.org_academic_year_id)
    LEFT JOIN org_academic_terms AS oat
        ON oat.id = COALESCE(pem.org_academic_terms_id, pom.org_academic_periods_id)
    #--Order it...on second thought...order last so we can use names
    #--ORDER BY person_id, oay.year_id, oat.term_code ASC
    )
    UNION
    ( select person_id ,oay.year_id,"" AS term_id,"" AS meta_key,"" AS meta_val from org_person_student_cohort AS opc
LEFT JOIN org_academic_year AS oay ON opc.org_academic_year_id = oay.id where opc.organization_id = ',orgId,')
    )
    AS
      md GROUP BY 1,2,3 #--the numbers designate columns in the result'
        );


        PREPARE stmt FROM @sql;
        EXECUTE stmt;



        SET @metalist = NULL;
        SELECT group_concat("`", `column_name`, "`")
        INTO   @metalist
        FROM   listofcols AS md;

        SET @resultsql = CONCAT('
    SELECT distinct ',@metalist,' FROM (SELECT

    distinct
        IFNULL(p.external_id,"") AS ExternalId,
        IFNULL(p.auth_username,"") AS AuthUsername,
        IFNULL(p.firstname,"") AS Firstname,
        IFNULL(p.lastname,"") AS Lastname,
        IFNULL(pinfo.title,"") AS Title,
        IFNULL(pinfo.record_type,"") AS RecordType,
        IFNULL(op.photo_url, "") AS StudentPhoto,
		    IFNULL(opsy.is_active, if(surveyResponse.year_id AND surveyResponse.term_id = "", "0", "")) AS IsActive,
        IF(opsy.id, 1, if(surveyResponse.year_id AND surveyResponse.term_id = "", "0", "")) AS Participating,
        IF(opsrtg.id,1,"") AS RetentionTrack,
        IFNULL(opsr.is_enrolled_beginning_year,"") AS EnrolledAtBeginningOfAcademicYear,
        IFNULL(opsr.is_enrolled_midyear,"") AS EnrolledAtMidpointOfAcademicYear,
        IFNULL(opsr.is_degree_completed,"") AS CompletedADegree,
        IFNULL(surveyResponse.cohort, "") AS SurveyCohort,

        TransitiononeReceiveSurvey,
        CheckuponeReceiveSurvey,
        TransitiontwoReceiveSurvey,
        CheckuptwoReceiveSurvey,

        surveyResponse.year_id AS YearId,
        surveyResponse.term_id AS TermId,
        IFNULL(pc.external_id, "") AS PrimaryConnect,
        IFNULL(rh.risk_group_id, "") AS RiskGroupId,
        IFNULL(opinfo.auth_key,"") AS StudentAuthKey,
        IFNULL(op.is_privacy_policy_accepted,"") AS IsPrivacyPolicyAccepted,
        IFNULL(op.privacy_policy_accepted_date, "") AS PrivacyPolicyAcceptedDate,
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
    from

     (SELECT
        TransitiononeReceiveSurvey,
            CheckuponeReceiveSurvey,
            TransitiontwoReceiveSurvey,
            CheckuptwoReceiveSurvey,
            person_id,
            year_id,
            organization_id,
            "" AS term_id,
            ifnull(cohort, "") AS cohort
    FROM
        (SELECT
        SUM(TransitiononeReceiveSurvey) AS TransitiononeReceiveSurvey,
            SUM(CheckuponeReceiveSurvey) AS CheckuponeReceiveSurvey,
            SUM(TransitiontwoReceiveSurvey) AS TransitiontwoReceiveSurvey,
            SUM(CheckuptwoReceiveSurvey) AS CheckuptwoReceiveSurvey,
            person_id,
            year_id,
            survey_Response.organization_id,
            cohort
    FROM
        (SELECT
        IF(SL.survey_id IN (SELECT
                    MIN(id)
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS TransitiononeReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MIN(id) + 1
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS CheckuponeReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MAX(id) - 1
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS TransitiontwoReceiveSurvey,
            IF(SL.survey_id IN (SELECT
                    MAX(id)
                FROM
                    survey
                WHERE
                    year_id IS NOT NULL
                GROUP BY year_id), opss1.receive_survey, NULL) AS CheckuptwoReceiveSurvey,
            ops.person_id,
            org_academic_year.year_id,
            org_academic_year.organization_id AS organization_id,
            opsc.cohort
    FROM
        org_person_student AS ops
    INNER JOIN org_academic_year ON org_academic_year.organization_id = ops.organization_id  AND org_academic_year.deleted_at IS NULL
    LEFT JOIN org_person_student_survey AS opss1 ON opss1.person_id = ops.person_id
        AND opss1.deleted_at IS NULL
    LEFT JOIN survey AS s ON (opss1.survey_id = s.id)
        AND org_academic_year.year_id = s.year_id
    LEFT JOIN survey_lang AS SL ON s.id = SL.survey_id
    LEFT JOIN org_person_student_cohort opsc ON opsc.person_id = ops.person_id
        AND opsc.org_academic_year_id = org_academic_year.id AND opsc.deleted_at IS NULL
    WHERE
        ops.organization_id = ',orgId,'
        ) AS survey_Response
    GROUP BY person_id , year_id) AS SR UNION SELECT
        NULL AS a,
            NULL AS b,
            NULL AS c,
            NULL AS d,
            person_id,
            "" AS e,
            organization_id,
            "" AS f,
            "" AS g
    FROM
        org_person_student
    WHERE
        organization_id = ',orgId,'
        AND org_person_student.deleted_at IS NULL
        UNION SELECT
        NULL,
            NULL,
            NULL,
            NULL,
            person_id,
            org_academic_year.year_id,
            org_person_student.organization_id,
            org_academic_terms.term_code,
            ""
    FROM
        org_person_student
    INNER JOIN org_academic_terms ON org_academic_terms.organization_id = org_person_student.organization_id
    INNER JOIN org_academic_year ON org_academic_terms.org_academic_year_id = org_academic_year.id
    WHERE
        org_person_student.organization_id = ',orgId,'
        AND org_academic_terms.deleted_at IS NULL
        AND org_academic_year.deleted_at IS NULL
-- ORDER BY person_id , year_id

) AS surveyResponse

     LEFT JOIN listofpeeps l ON l.person_id = surveyResponse.person_id
     LEFT JOIN person p ON p.deleted_at IS NULL AND p.id = l.person_id AND p.id = surveyResponse.person_id
     LEFT JOIN temppivot AS innie ON innie.person_id = p.id AND surveyResponse.term_id = innie.term_id AND surveyResponse.year_id = innie.year_id
     LEFT JOIN org_person_student op ON op.deleted_at IS NULL and
 op.person_id = l.person_id

     LEFT JOIN org_academic_year AS oay ON    oay.deleted_at IS NULL AND ((surveyResponse.year_id = oay.year_id) AND (oay.organization_id = op.organization_id ))
	   LEFT JOIN org_person_student_year AS opsy ON opsy.person_id = p.id AND oay.id = opsy.org_academic_year_id AND opsy.deleted_at IS NULL AND surveyResponse.term_id = ""
     LEFT JOIN org_person_student_retention_tracking_group opsrtg ON opsrtg.person_id = p.id AND   opsrtg.org_academic_year_id = oay.id  AND opsrtg.deleted_at IS NULL AND surveyResponse.term_id = ""
     LEFT JOIN org_person_student_retention opsr ON opsr.person_id = p.id  AND   opsr.org_academic_year_id = oay.id AND opsr.deleted_at IS NULL AND surveyResponse.term_id = ""
     LEFT JOIN org_person_student_cohort AS opsc ON       opsc.deleted_at IS NULL AND ((p.id  = opsc.person_id) AND (p.organization_id =  opsc.organization_id) AND (oay.id = opsc.org_academic_year_id ) AND (surveyResponse.term_id = "")) AND surveyResponse.cohort = opsc.cohort

     LEFT JOIN person pinfo ON pinfo.deleted_at IS NULL AND pinfo.id = innie.person_id AND innie.year_id="" AND innie.term_id="" AND (innie.year_id="" or innie.year_id IS null) #--We do this so that some facts only appear on the non-year/non-term row
     LEFT JOIN person_contact_info pci ON     pci.deleted_at IS NULL AND pci.person_id = pinfo.id
     LEFT JOIN contact_info ci ON ci.id = pci.contact_id
     LEFT JOIN person_contact_info pcinfo ON pcinfo.deleted_at IS NULL AND pcinfo.person_id = p.id
     LEFT JOIN contact_info cinfo ON cinfo.id = pcinfo.contact_id
     LEFT JOIN org_person_student opinfo ON opinfo.person_id = pinfo.id
     LEFT JOIN person AS pc ON    pc.deleted_at IS NULL AND pc.id = op.person_id_primary_connect
     LEFT JOIN risk_group_person_history AS rh ON rh.person_id = (SELECT rgh.person_id FROM risk_group_person_history AS rgh WHERE rgh.person_id = p.id ORDER BY rgh.assignment_date DESC LIMIT 1)
      where
p.external_id != "" #-- remove rows that do not have external_ids

AND
(
!(TransitiononeReceiveSurvey IS NULL AND CheckuponeReceiveSurvey IS NULL AND TransitiontwoReceiveSurvey IS NULL AND CheckuptwoReceiveSurvey IS NULL
AND innie.person_id IS NULL AND opsy.id IS NULL AND opsrtg.id IS NULL AND opsr.id IS NULL)
)
     ORDER BY p.firstname, p.lastname, ExternalId, YearId, TermId ASC
    ) AS results
    ');

        PREPARE stmt FROM @resultsql;
        EXECUTE stmt;


        COMMIT;

        SET group_concat_max_len = 1024;

        END$$

        DELIMITER ;