<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to create auditing persons, and update receivesurvey created / modified at
 */
class Version20160412132542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("INSERT INTO synapse.person (id, firstname, lastname, username, external_id, created_at, modified_at) VALUES
                        (-10, 'Talend', 'User', 'joshua.oryall@macmillan.com', 'skyfactor.joryall', NOW(), NOW()),
                        (-11, 'FactorCalc', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.factor_calc', NOW(), NOW()),
                        (-13, 'FixDatumSrcTs', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.fix_datum_src_ts', NOW(), NOW()),
                        (-14, 'IntentLeaveCalcAll', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.intent_leave_calc_all', NOW(), NOW()),
                        (-15, 'IntentLeaveCalc', 'StoredProcedure','joshua.stark@macmillan.com', 'skyfactor.sproc.intent_leave_calc', NOW(), NOW()),
                        (-16, 'IntentLeaveNullFixer', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.intent_leave', NOW(), NOW()),
                        (-17, 'ISQDataTransfer', 'StoredProcedure', 'joshua.oryall@macmillan.com', 'skyfactor.sproc.isq_data_transfer', NOW(), NOW()),
                        (-18, 'IssuesCalcTempTables', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.issues_calc_temp_tables', NOW(), NOW()),
                        (-19, 'OrgRiskFactorCalculation', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.org_risk_factor_calculation', NOW(), NOW()),
                        (-20, 'ReportCalc', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.report_calc', NOW(), NOW()),
                        (-22, 'SuccessMarkerCalc', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.success_marker_calc', NOW(), NOW()),
                        (-23, 'SurveyDataTransfer', 'StoredProcedure', 'joshua.oryall@macmillan.com', 'skyfactor.sproc.survey_data_transfer', NOW(), NOW()),
                        (-24, 'TalkingPointCalc', 'StoredProcedure', 'joshua.stark@macmillan.com', 'skyfactor.sproc.talking_point_calc', NOW(), NOW()),
                        (-25, 'Migration', 'Scripts', 'hai.deng@macmillan.com', 'skyfactor.migration_scripts', NOW(), NOW())");

        $this->addSql("UPDATE synapse.org_person_student_survey SET created_by = -25, modified_by = -25, created_at = NOW(), modified_at = NOW()");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
