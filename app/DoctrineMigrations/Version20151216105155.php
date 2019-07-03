<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151216105155 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $loggedUserId = '"$$loggedUserId$$"';
        $organizationId = '"$$organizationId$$"';
        $fromDate = '"$$fromDate$$"';
        $toDate = '"$$toDate$$"';
        $activity = '"R" , "C", "N", "A"';
        $academicStartDate = '"$$academicStartDate$$"';
        $academicEndDate =  '"$$academicEndDate$$"';
        
       
        $query = <<<CDATA
          UPDATE `synapse`.`ebi_search`
        SET
        `query` ='
    select 
    tm.teams_id,
    t.team_name,
    count(al.id) as numbers,
    "interaction" as activity
from
    Teams t,
    team_members tm,
    activity_log al
where
    tm.teams_id = t.id
        and tm.organization_id = t.organization_id
        and al.organization_id = tm.organization_id
        and al.person_id_faculty = tm.person_id
        and al.activity_type in ($activity)
        and al.activity_date between $fromDate and $toDate
        and al.activity_date between $academicStartDate and $academicEndDate
        and tm.teams_id in (SELECT 
            teams_id
        FROM
            team_members
        where
            is_team_leader = 1
                and person_id = $loggedUserId
                and deleted_at IS NULL)
        and tm.organization_id = $organizationId
        and t.deleted_at IS NULL
        and tm.deleted_at IS NULL
        and al.deleted_at IS NULL
group by t.team_name ' WHERE
        `query_key`="My_Team_Interactions_count_Groupby_Teams"
CDATA;
        $this->addSql($query);
        
        
        
        $query = <<<CDATA
          UPDATE `synapse`.`ebi_search`
        SET
        `query` ='
    select 
    tm.teams_id,
    t.team_name,
    count(al.id) as numbers,
    "openreferrals" as activity
from
    Teams t,
    team_members tm,
    activity_log al,
    referrals r
where
    tm.teams_id = t.id
        and tm.organization_id = t.organization_id
        and al.organization_id = tm.organization_id
        and al.person_id_faculty = tm.person_id
        and al.activity_type = "R"
        and r.id = al.referrals_id
        and r.status = "O"
         and al.activity_date between $fromDate and $toDate
        and al.activity_date between $academicStartDate and $academicEndDate
        and tm.teams_id in (SELECT 
            teams_id
        FROM
            team_members
        where
            is_team_leader = 1
                 and person_id = $loggedUserId
                and deleted_at IS NULL)
        and tm.organization_id = $organizationId
        and t.deleted_at IS NULL
        and tm.deleted_at IS NULL
        and al.deleted_at IS NULL
group by t.team_name ' WHERE
        `query_key`="My_Team_Open_Referrals_count_Groupby_Teams"
CDATA;
        $this->addSql($query);
        
        
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    
    }
}
