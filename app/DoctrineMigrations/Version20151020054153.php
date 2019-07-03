<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151020054153 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $calculation_query = <<<CDATA
DROP PROCEDURE IF EXISTS `IssueCalculation`;
CREATE PROCEDURE `IssueCalculation`()
BEGIN
#INSERT INTO messages (message,created_at) values('21. --> CreateTemptables',now()) ;
call IssueCalcTempTables();
#INSERT INTO messages (message,created_at) values('22. --> get all the staff id and orgId',now()) ;

SELECT person_id into @staffId FROM org_person_faculty where deleted_at is null 
and person_id not in (select staff_id from issues_temp_calc_done) limit 1;
SELECT organization_id into @orgId FROM org_person_faculty where deleted_at is null 
and person_id=@staffId limit 1;

#INSERT INTO messages (message,created_at) values(concat('23. --> calc start for staff id - ', convert( @staffId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
#INSERT INTO messages (message,created_at) values(concat('24. --> calc start for Org id - ', convert( @orgId,char(10))),now()) ;
WHILE (@staffId IS NOT NULL) dO
#INSERT INTO messages (message,created_at) values('25. --> IssueCalcPermissions',now()) ;
call IssueCalcPermissions(@orgId,@staffId);
#INSERT INTO messages (message,created_at) values('26. --> IssueCalcDenominator',now()) ;
call IssueCalcDenominator(@orgId,@staffId);
#INSERT INTO messages (message,created_at) values('27. --> IssueCalcNumerator',now()) ;
call IssueCalcNumerator(@orgId,@staffId);
#INSERT INTO messages (message,created_at) values('28. --> insert into issues_temp_calc_done',now()) ;
#insert into tmp table to capture org and staff id
insert into issues_temp_calc_done(org_id,staff_id) values(@orgId,@staffId);

#To fetch org_id
set @orgId := null;
set @staffId := null;
SELECT person_id into @staffId FROM org_person_faculty where deleted_at is null 
and person_id not in (select staff_id from issues_temp_calc_done) limit 1;
SELECT organization_id into @orgId FROM org_person_faculty where deleted_at is null 
and person_id=@staffId limit 1;

#if @staffId is not null then
#INSERT INTO messages (message,created_at) values(concat('30. --> calc start for staff id - ', convert( @staffId,char(10)),' and Org id - ', convert( @orgId,char(10))),now()) ;
#else
#INSERT INTO messages (message,created_at) values('30. NO MORE Org_Ids for issue calc, so quitting', now()) ;
#end if;
END WHILE;
#INSERT INTO messages (message,created_at) values('29. --> IssueCalcSet',now()) ;
call IssueCalcSet();
END;
CDATA;
        $this->addSql($calculation_query);
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
