<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-13222; update the faculty data dump
 */
class Version20180320170651 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("DROP procedure IF EXISTS `Faculty_Data_Dump`;
");
        $facultyDataDumpSql = <<<HEREDOC
CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Faculty_Data_Dump`(
IN orgId int(11),
IN slimit int(11),
IN soffset int(11)
)
BEGIN
START TRANSACTION;
        SET group_concat_max_len = 1000000;


SET @selectSTMT = NULL;
 SELECT
    group_concat(upload_column_name ORDER BY sort_order ASC, tie_breaker ASC)
FROM
    (SELECT
        uch.upload_column_name,
        sort_order,
        1 AS tie_breaker
    FROM
        upload u
    INNER JOIN upload_column_header_download_map uchdm ON u.id = uchdm.upload_id
    INNER JOIN upload_column_header uch ON uch.id = uchdm.upload_column_header_id
    INNER JOIN ebi_download_type edt ON edt.id = uchdm.ebi_download_type_id
    WHERE
        u.upload_name = 'faculty'
            AND edt.download_type = 'data_dump'
            AND u.deleted_at IS NULL
            AND uch.deleted_at IS NULL
            AND edt.deleted_at IS NULL
            AND uchdm.deleted_at IS NULL
        UNION
    SELECT
        em.meta_key,
        sort_order,
        0 AS tie_breaker
    FROM
        upload u
    INNER JOIN upload_ebi_metadata_column_header_download_map uemchd ON uemchd.upload_id = u.id
    INNER JOIN ebi_metadata em ON em.id = uemchd.ebi_metadata_id
    INNER JOIN ebi_download_type edt ON edt.id = uemchd.ebi_download_type_id
    WHERE
        u.upload_name = 'faculty'
            AND edt.download_type = 'data_dump'
            AND u.deleted_at IS NULL
            AND em.deleted_at IS NULL
            AND edt.deleted_at IS NULL
            AND uemchd.deleted_at IS NULL) AS upload_headers
INTO  @selectSTMT;






SET @facultyDataDump = concat('
SELECT ',
 @selectSTMT,
 ' FROM (
SELECT
        IFNULL(p.external_id,"") AS ExternalId,
        IFNULL(p.auth_username,"") AS AuthUsername,
        IFNULL(p.firstname,"") AS Firstname,
        IFNULL(p.lastname,"") AS Lastname,
        IFNULL(p.title,"") AS Title,
        IFNULL(op.status, 1) AS IsActive,
        IFNULL(op.auth_key,"") AS FacultyAuthKey,
        IFNULL(ci.address_1,"") AS Address1,
        IFNULL(ci.address_2,"") AS Address2,
        IFNULL(ci.city,"") AS City,
        IFNULL(ci.state,"") AS State,
        IFNULL(ci.country,"") AS Country,
        IFNULL(ci.zip,"") AS Zip,
        IFNULL(ci.primary_mobile,"") AS PrimaryMobile,
        IFNULL(ci.alternate_mobile,"") AS AlternateMobile,
        IFNULL(ci.home_phone,"") AS OfficePhone,
        IFNULL(ci.primary_email,"") AS PrimaryEmail,
        IFNULL(ci.alternate_email,"") AS AlternateEmail,
        IFNULL(ci.primary_mobile_provider,"") AS PrimaryMobileProvider,
        IFNULL(ci.alternate_mobile_provider,"") AS AlternateMobileProvider
FROM org_person_faculty AS op
JOIN person as p on p.id = op.person_id
LEFT JOIN person_contact_info pci on pci.person_id = p.id
LEFT JOIN contact_info ci on ci.id = pci.contact_id
WHERE op.organization_id =', orgId, ' AND op.deleted_at IS NULL) as faculty_data_dump
LIMIT ',  slimit, ' OFFSET ',  soffset, ';');

PREPARE stmt FROM @facultyDataDump;
EXECUTE stmt;

SET group_concat_max_len = 1024;


COMMIT;

END

HEREDOC;

        $this->addSql($facultyDataDumpSql);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
