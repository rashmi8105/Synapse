<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007165835 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $query = <<<'CDATA'
        UPDATE ebi_search SET `query` =  'SELECT R.id AS activity_id, AL.id AS activity_log_id, R.referral_date AS activity_date, R.person_id_faculty AS activity_created_by_id, P.firstname AS activity_created_by_first_name, P.lastname AS activity_created_by_last_name, AC.id AS activity_reason_id, AC.short_name AS activity_reason_text, R.note AS activity_description, R.status AS activity_referral_status FROM activity_log AS AL LEFT JOIN referrals AS R ON AL.referrals_id = R.id LEFT JOIN person AS P ON R.person_id_faculty = P.id LEFT JOIN activity_category AS AC ON R.activity_category_id = AC.id LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id LEFT JOIN organization_role as orgr on orgr.organization_id = AL.organization_id LEFT JOIN referral_routing_rules as rr on rr.activity_category_id = R.activity_category_id WHERE R.person_id_student = $$studentId$$ AND R.organization_id = $$orgId$$ AND R.deleted_at IS NULL AND (CASE WHEN access_team = 1 THEN RT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $$faculty$$ AND teams_id IN (SELECT teams_id from referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL)) AND $$teamAccess$$ = 1 ELSE CASE WHEN access_private = 1 THEN R.person_id_faculty = $$faculty$$ ELSE R.access_public = 1 AND $$publicAccess$$ = 1 END END AND R.person_id_assigned_to = $$faculty$$ OR R.person_id_faculty = $$faculty$$) AND orgr.role_id IN ($$roleIds$$) AND (rr.is_primary_coordinator = 1 AND rr.person_id IS NULL) OR (R.id IN (select rip.referrals_id from referrals_interested_parties as rip left join referrals as R2 ON R2.id = rip.referrals_id where rip.person_id = $$faculty$$ and R2.person_id_student = $$studentId$$ and rip.deleted_at is null)) GROUP BY R.id order by R.referral_date desc' WHERE `query_key` = 'Activity_Referral';
CDATA;
        $this->addSql($query);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
