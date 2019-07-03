<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806073649 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $personId = '$$personid$$';
        $orgId = '$$orgid$$';
        $query = <<<CDATA
UPDATE `ebi_search` SET `query` = "select 
    r.id as 'referral_id',
    r.person_id_student,
    p.firstname,
    p.lastname,
    p.risk_level,
    p.intent_to_leave,
    rml.image_name,
    rml.risk_text,
    lc.cnt as login_cnt,
    p.cohert,
    p.last_activity
FROM
    referrals r
        join
    person p ON (r.person_id_student = p.id)
        left join
    referral_routing_rules rr ON (rr.activity_category_id = r.activity_category_id)
        left join
    organization_role orgr ON (r.organization_id = orgr.organization_id)
        left join
    risk_level rml ON (p.risk_level = rml.id)
        left outer join
    Logins_count lc ON (lc.person_id = r.person_id_student)
where
    r.deleted_at IS NULL AND r.status = 'O'
        and (r.person_id_assigned_to = $personId
        or (rr.person_id = $personId
        and r.person_id_assigned_to is null
        and (rr.is_primary_coordinator = 0
        or rr.is_primary_coordinator is null))
        or (orgr.person_id = $personId
        and r.person_id_assigned_to is null
        and orgr.role_id = 1
        and (rr.is_primary_coordinator = 1
        AND rr.person_id IS null)))
		AND r.organization_id = $orgId
group by r.id"
WHERE `query_key` = 'My_Open_Referrals_Received_List';
CDATA;
        $this->addSql($query);
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
