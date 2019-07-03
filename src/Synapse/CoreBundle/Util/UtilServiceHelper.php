<?php
namespace Synapse\CoreBundle\Util;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use SplFileObject;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @DI\Service("util_service")
 */
class UtilServiceHelper extends AbstractService
{

    const SERVICE_KEY = 'util_service';

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger")
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
    }

    /**
     * DEPRECATED: Use DateUtilityService methods instead, depending on the needed functionality.
     *
     * DO NOT USE THIS FUNCTION IN NEW CODE!!!
     *
     * TODO: Remove this function and its uses in favor of methods in DateUtilityService
     *
     * It will return current date and time based on organization time zone
     * @param string $orgTimezone
     * @param string $dateFormat
     * @param boolean $sendTimezone
     * @param string $dateTime
     * @deprecated Please refer to methods in DateUtilityService to replace usage of this method
     * @return mixed
     */
    public function getDateByTimezone($orgTimezone, $dateFormat = '', $sendTimezone = false, $dateTime = null)
    {
        try {
            //Get the current date time. Attempt to retrieve the timezone object for that organization based on the passed in value.
            $currentNow = new \DateTime('now');
            $timezone = $this->repositoryResolver->getRepository('SynapseCoreBundle:MetadataListValues')->findByListName($orgTimezone);

            //If there is a timezone object
            if ($timezone) {
                $timeZone = $timezone[0]->getListValue();

                //If there is a datetime object passed in, use the passed in datetime object and set it to the timezone. Otherwise, set the current time datetime object to the timezone
                if (isset($dateTime)) {
                    $dateTime = new \DateTime($dateTime);
                    $dateTime->setTimezone(new \DateTimeZone('UTC'));
                    $currentNow = $dateTime->setTimezone(new \DateTimeZone($timeZone));
                } else {
                    $currentNow = new \DateTime('now', new \DateTimeZone($timeZone));
                }
            }

        } catch (\Exception $e) {
            $currentNow = new \DateTime('now');
        }

        //If the sendTimezone flag is true, return the timezone object.
        if ($sendTimezone) {
            return $timeZone;
        }

        //If there is a passed in format for the datetime, use it. Otherwise, use the default datetime format.
        $format = ($dateFormat == '') ? 'Ymd_HisT' : $dateFormat;

        //Format the datetime object based on the above format string.
        $currentDate = $currentNow->format($format);
        
        return $currentDate;
    }


    /**
     * It will take $records,$fileName as mandatory params and based on that will generate records in CSV format
     *
     * This function has been deprecated. Please use: CSVUtilityService::generateCSV()
     *
     * @param array $records
     * @param string $fileName
     * @param string $options
     * @return multitype:unknown
     * @deprecated
     */
    public function generateCSV ($records, $fileName, $options = null, $customSearchDto=null, $orgTimeZone = null, $path = ReportsConstants::EXPORT_CSV) {

        $file = new SplFileObject("data://".$path."/$fileName", 'w');

        if(is_null($options)){
            $options =  array();
        }

        /**
         * In case of custom search, selected attributes should be displayed in CSV download as first row
         */
        $searchAttr = [];
        if(!empty($customSearchDto) && !empty($customSearchDto->getSelectedAttributesCsv())){
            $searchAttr[] = "Search Attributes->";
            $searchAttr[] = $customSearchDto->getSelectedAttributesCsv();
            $file->fputcsv($searchAttr);
        }

        $columns = array();
        foreach( $records as $record) {
            foreach( $record as $column => $value) {
                if(!in_array($column,$options['ignored'])){
                    if(array_key_exists($column,$options['columnNamesMap'])){
                        $column =  $options['columnNamesMap'][$column]['display_name'];
                    }
                    /**
                     *  Added regular Expression to change the "_" or camel case
                     *   with Proper ColumnName with whitespace between words
                     */
                    $columns[] = ucfirst(preg_replace('/([a-z])([A-Z])/', '$1 $2', lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', ucfirst($column)))))));

                }
            }
            break;
        }

        $file->fputcsv($columns);

        foreach( $records  as $record) {
            $newArr =  array();
            foreach($record as $key => $value)
                if(!in_array($key,$options['ignored'])){

                if(array_key_exists($key,$options['columnNamesMap'])){
                    /**
                     *  Date format if available in the options array
                     */
                    $format = (isset($options['columnNamesMap'][$key]['format'])) ? $options['columnNamesMap'][$key]['format'] : '';

                    if(isset($options['columnNamesMap'][$key]['type'])){
                        $value =  $this->$options['columnNamesMap'][$key]['type']($value, $format, $orgTimeZone);
                    }
                }
                $newArr[] = $value;
            }
            $file->fputcsv($newArr);
        }

        return array('file_name' => $fileName);
    }


    /**
     * It will convert the given value in Y-m-d format
     *
     * This method is used above in generateCSV(), but not referenced properly. Please instead use an appropriate method from DateUtilityService
     *
     * @param $value
     * @param $format
     * @param $orgTimezone
     * @return string | null
     *
     * @deprecated
     */
    private function convertDate($value, $format = '', $orgTimezone = '')
    {
        if(empty($value)){
            return $value;
        }
         /*
         * if $format available then return date and time based on organization timezone
         */
        if (isset($format)) {
            return $this->getDateByTimezone($orgTimezone, $format, '', $value);
        }
        $value = new \DateTime($value);
        return $value->format('Y-m-d');
    }

   /**
    * TODO: Remove this function when the /surveys/deprecated API is removed.
    *
    * Method would return the cohort codes for a student
    *
    * @param  $studentId
    * @return string
    * @deprecated
    */
    public function getCohotCodesForStudent($studentId){

        $orgPersonStudentSurveyLinkRepo = $this->repositoryResolver->getRepository('SynapseSurveyBundle:OrgPersonStudentSurveyLink');
        $cohortCode = $orgPersonStudentSurveyLinkRepo->getStudentCohort($studentId);
        if(is_array($cohortCode) & count($cohortCode) > 0){
            $cohortCode =  implode(",",$cohortCode); // imploding the array of cohort code to make it a "," separated string
        }else{
            $cohortCode =  -1; //  this is to avoid breaking of query and would return no results if no cohortcode found
        }

        return $cohortCode;

    }

    /**
     *  Returns the string being appended to the Query to order the query result with the sortBy field.
     *
     * @param $sortBy
     * @param $sortableFields
     * @param string $defaultSortFields
     * @param bool $riskLevelGrayFlag
     * @param string $tableAlias
     * @return string
     * @deprecated
     */
    public function getSortByField($sortBy, $sortableFields, $defaultSortFields = '', $riskLevelGrayFlag = false, $tableAlias = 'RL')
    {
        $sortOrder = '';
        $riskFieldArray = ['student_risk_status', 'risk_text'];

        if (empty($sortBy)) {

            return $defaultSortFields;
        }

        if (($sortBy[0] == '+') || ($sortBy[0] == '-')) {

            if ($sortBy[0] == '-') {
                $sortOrder = ' DESC';
            }

            $sortBy = substr($sortBy, 1, strlen($sortBy));
        }

        // Manipulate the order for student risk to maintain risk sorting standards
        if (in_array($sortBy, $riskFieldArray)) {
            if (trim($sortOrder) == 'DESC') {
                $sortOrder = ' ASC ';
            } else {
                $sortOrder = ' DESC ';
            }
        }

        if (isset($sortableFields[$sortBy])) {

            // Added $riskLevelGrayFlag to identify the sorting field is risk level and add below query snippet to sort the resultset by risk as per the sorting standards.
            if ($riskLevelGrayFlag && in_array($sortBy, $riskFieldArray)) {
                $riskLevelGraySortOrder = " $tableAlias.id=(SELECT id FROM risk_level WHERE risk_text='gray' AND deleted_at IS NULL) ASC, ";
            } else {
                $riskLevelGraySortOrder = " ";
            }

            return ' ORDER BY ' . $riskLevelGraySortOrder . ' ' . str_replace('[SORT_ORDER]', $sortOrder, $sortableFields[$sortBy]);
        } else {
            return '';
        }
    }
    
}