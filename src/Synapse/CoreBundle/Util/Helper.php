<?php
namespace Synapse\CoreBundle\Util;

use JMS\Serializer\Serializer;

class Helper
{

    const BASEQUERY_SELECT = "
                SELECT
                    SQL_CALC_FOUND_ROWS
                    p.id,
                    p.firstname,
                    p.lastname,
                    p.risk_level,
                    rml.risk_model_id,
                    ops.status AS student_status,
                    il.id AS intent_id,
                    il.image_name AS intent_imagename,
                    il.text AS intent_text,
                    rl.image_name AS risk_imagename,
                    rl.risk_text AS risk_text,
                    (COUNT(DISTINCT (lc.id))) AS login_cnt,p.external_id,
                    (
                        SELECT
                        (
                            CASE WHEN (activity_type='N')
                                THEN CONCAT(activity_date, ' - ','Note')
                            WHEN (activity_type='A')
                                THEN CONCAT(activity_date , ' - ','Appointment')
                            WHEN (activity_type='C')
                                THEN CONCAT(activity_date, ' - ','Contact')
                            WHEN (activity_type='E')
                                THEN CONCAT(activity_date, ' - ','Email')
                            WHEN (activity_type='R')
                                THEN CONCAT(activity_date, ' - ','Referral')
                            ELSE
                                CONCAT(activity_date, ' - ','Login')
                            END
                        ) AS new
                        FROM
                            activity_log
                        WHERE
                            id =  MAX(lc.id)
                    ) AS last_activity,
                    r.id AS rid,
                    p.userName AS primary_email,
                    (
                        CASE WHEN (p.risk_level IS NULL OR p.risk_level=0)
                            THEN (SELECT id FROM risk_level WHERE deleted_at IS NULL AND risk_text='gray')
                        ELSE p.risk_level
                        END
                    ) AS risk_level_order,
                    (
                        CASE WHEN (p.intent_to_leave IS NULL OR p.intent_to_leave=0)
                            THEN (SELECT id FROM intent_to_leave WHERE deleted_at IS NULL AND `text`='gray')
                        ELSE p.intent_to_leave
                        END
                    ) AS intent_to_leave_order,
                    (
                        SELECT activity_date FROM activity_log WHERE id = MAX(lc.id)
                    ) as last_activity_date
";


    const BASEQUERY_CONST1 = "
          left join person_ebi_metadata pem on 
                (pem.person_id = p.id and pem.ebi_metadata_id= [EBI_METADATA_CLASSLEVEL_ID] 
                    /* and pem.org_academic_year_id = [CURRENT_ACADEMIC_YEAR] */)
                left join ebi_metadata_list_values emlv on (pem.metadata_value = emlv.list_value
            		and emlv.ebi_metadata_id = [EBI_METADATA_CLASSLEVEL_ID])
          left join intent_to_leave il on (p.intent_to_leave = il.id)
          left join referrals r on (p.id = r.person_id_student and  r.deleted_at is null)
          left join org_person_student ops on (p.id = ops.person_id)
          left outer join activity_log lc on (lc.person_id_student = p.id and lc.deleted_at is null)
          left join risk_level rl on (p.risk_level = rl.id)
          left join risk_model_levels rml on (rml.risk_level = rl.id)
          
          
          
         ";


    const BASEQUERY_CONST2 = " and deleted_at is null and org_permissionset_id in
                               (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null))
                                and ogs.deleted_at is null ";

    const BASEQUERY_CONST3 = " and p.deleted_at is null ";

    const BASEQUERY_CONST4 = "AND p.id in(select distinct person_id from org_course_student where deleted_at is null and org_courses_id in
                            (select distinct id from org_courses oc where deleted_at is null and id in
                            (select distinct org_courses_id from org_course_faculty where deleted_at is null and org_permissionset_id in
                            (select id from org_permissionset where deleted_at is null and view_courses=1)) ";

    const BASEQUERY_CONST6 = " p.id in(select distinct person_id from person_ebi_metadata ";

    const BASEQUERY_CONST7 = " p.id in(select distinct person_id from person_org_metadata ";

    const CONTACT_TYPES = "contacttypes";

    const REFL_STATUS = "referralstatus";

    const SQL_MY_GROUP_STUDENTS = '
        /* Filter for selecting groups */
        
            SELECT DISTINCT ogs.person_id
            FROM
                org_group_students ogs
                    INNER JOIN
                org_group_tree ogt
                        ON ogt.descendant_group_id = ogs.org_group_id
                    INNER JOIN
                org_group_faculty ogf
                        ON ogf.org_group_id = ogt.ancestor_group_id
                        AND ogf.organization_id = ogs.organization_id
                    INNER JOIN
                org_group_tree ogt2
                        ON ogt2.descendant_group_id = ogs.org_group_id
            WHERE
                ogs.deleted_at IS NULL
                AND ogt.deleted_at IS NULL
                AND ogf.deleted_at IS NULL
	            AND ogt2.deleted_at IS NULL
                AND ogs.organization_id = [ORG_ID]
                AND ogf.person_id = [FACULTY_ID]
                AND ogt2.ancestor_group_id IN ([GROUP_IDS])
        ';

    public static function generateToken($salt = null)
    {
        $token = "";
        if (is_null($salt)) {
            $token = md5(time());
        } else {
            $token = md5($salt . time() . $salt);
        }
        return $token;
    }

    public static function generateEmailMessage($message, $tokenValues)
    {
        preg_match_all('/\\$\$(.*?)\$\$/', $message, $tokenArrays);
        $tokenArray = $tokenArrays[0];
        $tokenKeys = $tokenArrays[1];
        for ($tokenCount = 0; $tokenCount < count($tokenArray); $tokenCount ++) {
            if (isset($tokenValues[$tokenKeys[$tokenCount]])) {
                $message = str_replace($tokenArray[$tokenCount], $tokenValues[$tokenKeys[$tokenCount]], $message);
            }
        }
        return $message;
    }

    /**
     * Return date in UTC formate
     *
     * @param string $date
     * @param string $orgTimezone
     * @return date
     */
    public static function getUtcDate($date = null, $orgTimezone = null)
    {
        if ($date) {
            $dateTime = clone $date;
        } else {
            $dateTime = new \DateTime('now');
            $date = $dateTime;
        }
        if (! is_null($orgTimezone)) {
            try {
                $orgTimezone = new \DateTimeZone($orgTimezone);
                $dateTime = new \DateTime($date->format('Y-m-d H:i:s'), $orgTimezone);
            } catch (\Exception $e) {}
        }
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        return $dateTime;
    }

    public static function getStartDateWeek(\DateTime $fuDate, $weekno)
    {
        $date = clone $fuDate;
        if ($weekno == 5) {
            // last week
            $totalDays = $date->format('t');
            $date->setDate($date->format('Y'), $date->format('m'), $totalDays);
            $weekstart = (int) $date->format('W');
            $date->setISODate($date->format('Y'), $weekstart);
        } else {

            $weekstart = (int) $date->format('W');
            if ($weekno != 1) {

                $date = $date->add(\DateInterval::createFromDateString('first sunday of this month'));
                if ($date->format('j') == 1) {
                    // This is first week
                    $noofdays = ($weekno - 1) * 7;
                } else {
                    // this is not first week
                    $noofdays = ($weekno - 2) * 7;
                }
                $date->modify("+$noofdays day");
            } else {
                $date->setDate($date->format('Y'), $date->format('n'), 1);
            }
        }
        return clone $date;
    }


    public static function getOrganizationDate(\DateTime $date, $timezone = 'UTC')
    {
        try {
            $date->setTimezone(new \DateTimeZone($timezone));
            return $date;
        } catch (\Exception $e) {}
    }

    public static function setOrganizationDate(\DateTime $date, $timezone = 'UTC')
    {
        try {
            $date->setTimezone(new \DateTimeZone($timezone));
        } catch (\Exception $e) {
            $date->setTimezone(new \DateTimeZone('UTC'));
        }
    }

    public static function generateQuery($query, $tokenValues)
    {
        return self::generateEmailMessage($query, $tokenValues);
    }

    public static function queryAppend($value, $append)
    {
        $valCount = substr_count($value, ',');
        if ($value == null || strlen($value) < 1) {
            $sqlApp = "";
        } elseif ($valCount == 0 && strlen($value) > 0) {
            $sqlApp = $append . " = '" . $value . "'";
        } elseif ($valCount > 0) {
            $valueArr = explode(',', $value);
            foreach ($valueArr as $arr) {
                if (trim($arr) != "") {
                    $ansArr[] = $arr;
                }
                $value = "'" . implode("','", $ansArr) . "'";
            }
            $sqlApp = $append . " in ($value)";
        } else {
            $sqlApp = "";
        }
        return $sqlApp;
    }

    public static function filterMap($key, $type)
    {
        $resp = "";
        if ($key == "interaction" && $type == self::CONTACT_TYPES) {
            $resp = 1;
        } elseif ($key == "non-interaction" && $type == self::CONTACT_TYPES) {
            $resp = 2;
        } elseif ($key == "all" && $type == self::CONTACT_TYPES) {
            $resp = '1,2';
        } elseif ($key == "open" && $type == self::REFL_STATUS) {
            $resp = "O";
        } elseif ($key == "closed" && $type == self::REFL_STATUS) {
            $resp = "C";
        } elseif ($key == "all" && $type == self::REFL_STATUS) {
            $resp = 'O,C';
        } else {
            $resp = "";
        }
        return $resp;
    }


    public static function encrypt($textToEncrypt, $encryptionMethod = "AES-256-CBC", $secretHash = "25c6c7ff35b9979b151f2136cd13b0ff")
    {
        $encryptedMessage = @openssl_encrypt($textToEncrypt, $encryptionMethod, $secretHash);
        return $encryptedMessage;
    }

    public static function decrypt($textToEncrypt, $encryptionMethod = "AES-256-CBC", $secretHash = "25c6c7ff35b9979b151f2136cd13b0ff")
    {
        $decryptedMessage = @openssl_decrypt($textToEncrypt, $encryptionMethod, $secretHash);
        return $decryptedMessage;
    }
    
    public static function getStartPointRecord($pageNo,$limit){
    
        $startPoint = ($pageNo * $limit) - $limit;
        return $startPoint;
    }

    
    public static function getSortByField($sortBy, $sortableFields, $defaultsortFields='')
    {    
        $sortOrder = '';
        if(empty($sortBy)){
            
            return $defaultsortFields;
        }
         
        if ( ($sortBy[0] == '+') || ($sortBy[0] == '-') ) {
             
            if ($sortBy[0] == '-') {
                $sortOrder = ' desc';
            }
             
            $sortBy = substr( $sortBy, 1, strlen($sortBy));
        }
        if(isset($sortableFields[$sortBy])) {
            return ' order by ' . str_replace('[SORT_ORDER]', $sortOrder, $sortableFields[$sortBy]);
        }else{
            return  '';
        }
    }


    public static function getLimit($pageNo, $offset)
    {
        $startPoint = ($pageNo * $offset) - $offset;
        return " LIMIT $startPoint , $offset ";
    }

}
