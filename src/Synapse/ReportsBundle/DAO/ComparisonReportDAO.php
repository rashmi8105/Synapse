<?php

namespace Synapse\ReportsBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;


/**
 * @DI\Service("comparison_report_dao")
 */
class ComparisonReportDAO
{

    const DAO_KEY = 'comparison_report_dao';

    /**
     * @var Connection
     */
    private $connection;


    /**
     * ComparisonReportDAO constructor
     *
     * @param $connection
     *
     * @DI\InjectParams({
     *     "connection" = @DI\Inject("database_connection")
     * })
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * This method execute R-Script and update response JSON in report running status table .
     *
     * @param string $compareReportJsonId
     * @param string $RScriptSystemPath
     * @param string $RScriptPath
     * @return string
     */
    public function executeRscriptJob($compareReportJsonId, $RScriptSystemPath, $RScriptPath)
    {
        if (empty($compareReportJsonId)) {
            return 'NULL';
        }
        $output = exec('ssh -oStrictHostKeyChecking=no -i ' . $RScriptSystemPath . " Rscript $RScriptPath $compareReportJsonId && echo $? ");
        return $output;
    }

    /**
     * This function will create gpa and factor case query for ISP or profile block.
     *
     * @param array $profileData
     * @param string $type
     * @return string
     */

    public function createCaseQueryForIspOrProfileblock($profileData, $type)
    {
        $caseQueryForFactorAndGpa = '';
        if ($type == 'isp') {
            $tableAlias = 'pom';
        } else if ($type == 'profile') {
            $tableAlias = 'pemfilter';
        }

        if ($profileData['item_data_type'] == 'S') {
            $subpopulation1CategoryArray = $profileData['subpopulation1']['category_type'];
            $parameters["subpopulation1_category_values"] = array_column($subpopulation1CategoryArray, 'value');

            $subpopulation2CategoryArray = $profileData['subpopulation2']['category_type'];
            $parameters["subpopulation2_category_values"] = array_column($subpopulation2CategoryArray, 'value');

            $caseQueryForFactorAndGpa['case_query'] = "CASE
                            WHEN $tableAlias.metadata_value IN (:subpopulation1_category_values) THEN 1
                            WHEN $tableAlias.metadata_value IN (:subpopulation2_category_values) THEN 2
                            END";

            $caseQueryForFactorAndGpa['parameters'] = $parameters;


        } elseif ($profileData['item_data_type'] == 'N') {
            if ($profileData['subpopulation1']['is_single']) {
                $parameters['subpopulation1_single_value'] = $profileData['subpopulation1']['single_value'];
                $subPopulation1SubQuery = "= :subpopulation1_single_value";
            } else {
                $parameters['subpopulation1_min_digits'] = $profileData['subpopulation1']['min_digits'];
                $parameters['subpopulation1_max_digits'] = $profileData['subpopulation1']['max_digits'];
                $subPopulation1SubQuery = "BETWEEN :subpopulation1_min_digits AND :subpopulation1_max_digits";
            }

            if ($profileData['subpopulation2']['is_single']) {
                $parameters['subpopulation2_single_value'] = $profileData['subpopulation2']['single_value'];
                $subPopulation2SubQuery = "= :subpopulation2_single_value";
            } else {
                $parameters['subpopulation2_min_digits'] = $profileData['subpopulation2']['min_digits'];
                $parameters['subpopulation2_max_digits'] = $profileData['subpopulation2']['max_digits'];
                $subPopulation2SubQuery = "BETWEEN :subpopulation2_min_digits AND :subpopulation2_max_digits";
            }


            $caseQueryForFactorAndGpa['case_query'] = "CASE
                            WHEN $tableAlias.metadata_value $subPopulation1SubQuery THEN 1
                            WHEN $tableAlias.metadata_value $subPopulation2SubQuery THEN 2
                            END";
            $caseQueryForFactorAndGpa['parameters'] = $parameters;


        } elseif ($profileData['item_data_type'] == 'D') {
            $metaValueDateFormat = SynapseConstant::METADATA_TYPE_DATE_FORMAT;
            $metaValueDefaultDateFormat = SynapseConstant::METADATA_TYPE_DEFAULT_DATE_FORMAT;

            $parameters['subpopulation1_start_date'] = $profileData['subpopulation1']['start_date'];
            $parameters['subpopulation1_end_date'] = $profileData['subpopulation1']['end_date'];

            $parameters['subpopulation2_start_date'] = $profileData['subpopulation2']['start_date'];
            $parameters['subpopulation2_end_date'] = $profileData['subpopulation2']['end_date'];

            $caseQueryForFactorAndGpa['case_query'] = "CASE
                            WHEN STR_TO_DATE($tableAlias.metadata_value , '$metaValueDateFormat' ) 
                            BETWEEN STR_TO_DATE(:subpopulation1_start_date , '$metaValueDateFormat' ) AND STR_TO_DATE(:subpopulation1_end_date , '$metaValueDateFormat' ) THEN 1
                            WHEN STR_TO_DATE($tableAlias.metadata_value , '$metaValueDefaultDateFormat' )
                            BETWEEN STR_TO_DATE(:subpopulation1_start_date , '$metaValueDateFormat') AND STR_TO_DATE(:subpopulation1_end_date , '$metaValueDateFormat' ) THEN 1
                            WHEN STR_TO_DATE($tableAlias.metadata_value , '$metaValueDateFormat' )
                            BETWEEN STR_TO_DATE(:subpopulation2_start_date , '$metaValueDateFormat' ) AND STR_TO_DATE(:subpopulation2_end_date , '$metaValueDateFormat' ) THEN 2
                            WHEN STR_TO_DATE($tableAlias.metadata_value , '$metaValueDefaultDateFormat' )
                            BETWEEN STR_TO_DATE(:subpopulation2_start_date , '$metaValueDateFormat' ) AND STR_TO_DATE(:subpopulation2_end_date , '$metaValueDateFormat' ) THEN 2
                            END";
            $caseQueryForFactorAndGpa['parameters'] = $parameters;
        }
        return $caseQueryForFactorAndGpa;
    }

}
