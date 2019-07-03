<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Fix for ESPRJ-10427: faculty
 * This will change the Faculty_Data_Dump and
 * change the isActive to "IFNULL(status, 1) verses
 * IFNULL(NULLIF(status, 0), "") which will return
 * NULL if the person is inactive
 */
class Version20160622211209 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('USE `synapse`;
DROP procedure IF EXISTS `Faculty_Data_Dump`;

CREATE DEFINER=`synapsemaster`@`%` PROCEDURE `Faculty_Data_Dump`(
IN orgId int(11),
IN slimit int(11),
IN soffset int(11)
)
BEGIN
START TRANSACTION;
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
        IFNULL(ci.home_phone,"") AS HomePhone,
        IFNULL(ci.primary_email,"") AS PrimaryEmail,
        IFNULL(ci.alternate_email,"") AS AlternateEmail,
        IFNULL(ci.primary_mobile_provider,"") AS PrimaryMobileProvider,
        IFNULL(ci.alternate_mobile_provider,"") AS AlternateMobileProvider
FROM org_person_faculty as op
JOIN person as p on p.id = op.person_id
LEFT JOIN person_contact_info pci on pci.person_id = p.id
LEFT JOIN contact_info ci on ci.id = pci.contact_id
WHERE op.organization_id = orgId AND op.deleted_at IS NULL
LIMIT slimit OFFSET soffset;
COMMIT;

END


');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
