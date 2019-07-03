<?php
namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\CoreBundle\Util\Constants\StudentConstant;

/**
 * @DI\Service("reports_helper_service")
 */
class ReportsHelperService extends AbstractService
{

    const SERVICE_KEY = 'reports_helper_service';

    private $reportsRepo;

    /*
     * Replaces Risk Level with Risk ColorText Takes Array with Risk level and replaces it with color. 
     * DOES NOT change the key in the array UNIT TESTED.
     * @param array $riskSource (Array with Risk Level) 
     * @returns array (Array with Risk Color)
     */
    public function replaceRiskLevelWithColorText($riskSource, $riskLevelRepo)
    {
        $newRiskSource = array();

        foreach ($riskSource as $riskEntry) {
            $replaceLevelWithColor = $riskEntry;
            $riskLevel = $riskEntry['risk_level'];
            $riskColor = $riskLevelRepo->findOneBy([
                'id' => $riskLevel
            ]);
            $replaceLevelWithColor['risk_level'] = $riskColor->getRiskText();
            
            $newRiskSource[] = $replaceLevelWithColor;
        }
        
        return $newRiskSource;
    }

    /**
     * replaces OrgAcademicYearId with its Name
     *
     * @param array $yearId            
     * @return string
     */
    public function replaceYearIdWithName($yearId, $orgAcademicYearRepository)
    {
        $orgAcademicYear = $orgAcademicYearRepository->findOneBy([
            'id' => $yearId
        ]);
        $yearName = $orgAcademicYear = $orgAcademicYear->getName();
        return $yearName;
    }

    /**
     * replaces OrgAcademicTermId with its Name
     *
     * @param array $termId            
     * @return string
     */
    public function replaceTermIdWithName($termId, $orgAcademicTermRepository)
    {
        $orgAcademicTerm = $orgAcademicTermRepository->findOneBy([
            'id' => $termId
        ]);
        $termName = $orgAcademicTerm->getName();
        return $termName;
    }

    /**
     * Get activities as per the year start date, end date, participant students
     *
     * @param integer $orgId
     * @param string $yearStartDate
     * @param string $yearEndDate
     * @param string $type
     * @param array $participantStudents
     * @param null|integer $accessPersonId
     * @param null|integer $pageNumber
     * @param null|integer $recordsPerPage
     * @param integer $personId
     * @param integer $debug
     * @param string $sortBy
     * @return mixed
     */
    public function getActivities($orgId, $yearStartDate, $yearEndDate, $type, $participantStudents, $accessPersonId = NULL, $pageNumber = NULL, $recordsPerPage = NULL, $personId, $debug, $sortBy)
    {
        // This method gets called from reportService  and it extends ReportsHelperService which has $this->activityService initialized
        $sharingAccess = $this->activityService->getSharingAccess($personId);

        $permArr = array(
            StudentConstant::NOTE_TEAM_ACCESS => $sharingAccess[StudentConstant::NOTES][StudentConstant::TEAM_VIEW],
            'notePublicAccess' => $sharingAccess[StudentConstant::NOTES][StudentConstant::PUBLIC_VIEW],
            'contactTeamAccess' => $sharingAccess[StudentConstant::LOG_CONTACTS][StudentConstant::TEAM_VIEW],
            'contactPublicAccess' => $sharingAccess[StudentConstant::LOG_CONTACTS][StudentConstant::PUBLIC_VIEW],
            'referralTeamAccess' => $sharingAccess[StudentConstant::REFERRALS][StudentConstant::TEAM_VIEW],
            'referralPublicAccess' => $sharingAccess[StudentConstant::REFERRALS][StudentConstant::PUBLIC_VIEW],
            'referralPublicAccessReasonRouted' => $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED][StudentConstant::PUBLIC_VIEW],
            'referralTeamAccessReasonRouted' => $sharingAccess[StudentConstant::REFERRALS_REASON_ROUTED][StudentConstant::TEAM_VIEW],
            'appointmentTeamAccess' => $sharingAccess[StudentConstant::BOOKING][StudentConstant::TEAM_VIEW],
            'appointmentPublicAccess' => $sharingAccess[StudentConstant::BOOKING][StudentConstant::PUBLIC_VIEW],
            'emailTeamAccess' => $sharingAccess[StudentConstant::EMAIL][StudentConstant::TEAM_VIEW],
            'emailPublicAccess' => $sharingAccess[StudentConstant::EMAIL][StudentConstant::PUBLIC_VIEW]
        );

        $referralTeamAccess = $sharingAccess['Referrals']['team_view'];
        $referralPublicAccess = $sharingAccess['Referrals']['public_view'];
        $referralPublicAccessReasonRouted = $permArr['referralPublicAccessReasonRouted'];
        $referralTeamAccessReasonRouted = $permArr['referralTeamAccessReasonRouted'];

        $noteTeamAccess = (int)$permArr[StudentConstant::NOTE_TEAM_ACCESS];
        $notePublicAccess = (int)$permArr['notePublicAccess'];

        $contactTeamAccess = (int)$permArr['contactTeamAccess'];
        $contactPublicAccess = (int)$permArr['contactPublicAccess'];

        $appointmentTeamAccess = (int)$permArr['appointmentTeamAccess'];
        $appointmentPublicAccess = $permArr['appointmentPublicAccess'];

        $emailTeamAccess = (int)$permArr['emailTeamAccess'];
        $emailPublicAccess = (int)$permArr['emailPublicAccess'];

        $this->reportsRepo = $this->repositoryResolver->getRepository(ReportsConstants::REPORTS_REPO);

        $rfacultyAccess = '';
        $cfacultyAccess = '';
        $nfacultyAccess = '';
        $afacultyAccess = '';
        $efacultyAccess = '';

        $rAddYear = '';
        $cAddYear = '';
        $nAddYear = '';
        $aAddYear = '';
        $eAddYear = '';

        if ($yearStartDate != NULL || $yearEndDate != NULL) {
            $rAddYear = " and (R.created_at between '" . $yearStartDate . "' and '" . $yearEndDate . "')";
            $cAddYear = " and (C.contact_date between '" . $yearStartDate . "' and '" . $yearEndDate . "')";
            $nAddYear = " and (N.created_at between '" . $yearStartDate . "' and '" . $yearEndDate . "')";
            $aAddYear = " and (A.start_date_time between '" . $yearStartDate . "' and '" . $yearEndDate . "')";
            $eAddYear = " and (E.created_at between '" . $yearStartDate . "' and '" . $yearEndDate . "')";
        }

        if ($accessPersonId != '') {
            $rfacultyAccess = ' and R.person_id_faculty =' . $accessPersonId;
            $cfacultyAccess = ' and C.person_id_faculty =' . $accessPersonId;
            $nfacultyAccess = ' and N.person_id_faculty =' . $accessPersonId;
            $afacultyAccess = ' and A.person_id =' . $accessPersonId;
            $efacultyAccess = ' and E.person_id_faculty=' . $accessPersonId;
        }
        $tempsql = array();

        $tempsql['referrals'] = "
              [SELECT] 
              activity_id, 
              student_id,
              student_firstname, 
              student_lastname,  
              faculty_firstname,     
              faculty_lastname,
              details, 
              activity_type,
              '' as activity_status, 
              activity_date,
              created_by
              from (
             ( SELECT  
                    R.id AS activity_id, 
                    PS.id as student_id,
                    PS.firstname as student_firstname, 
                    PS.lastname as student_lastname,  
                    P.firstname AS faculty_firstname,     
                    P.lastname AS faculty_lastname,
                    R.note AS details, 
                    'Referral' as activity_type,
                    '' as activity_status, 
                    R.created_at AS activity_date,
                    CONCAT(P.firstname , ' ' ,P.lastname) as created_by
                    FROM activity_log AS AL 
                    LEFT JOIN referrals AS R ON AL.referrals_id = R.id 
                    LEFT JOIN person AS P ON R.person_id_faculty = P.id 
                    LEFT JOIN person AS PS on PS.id = R.person_id_student 
                    LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id 
                    LEFT JOIN related_activities as RA ON R.id = RA.referral_id
                    LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id 
                    LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id
                    WHERE R.organization_id = $orgId AND R.status in ('O','C') AND R.person_id_student in (
                    SELECT DISTINCT merged.student_id
            FROM
                (
                    SELECT student_id,permissionset_id 
                    FROM  org_faculty_student_permission_map 
                    WHERE faculty_id = $personId AND org_id = $orgId
                ) AS merged
            INNER JOIN org_person_student AS P ON P.person_id=merged.student_id AND P.deleted_at IS NULL AND P.organization_id = $orgId
            INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id AND OPS.accesslevel_ind_agg = 1) 
                AND R.deleted_at IS NULL AND (
                CASE WHEN AL1.activity_type IS NOT NULL AND ( AL1.activity_type = 'R' AND R1.access_private = 1) THEN R.person_id_faculty = $personId ELSE
                
                CASE WHEN R.access_team = 1 THEN
                
                (
                RT.teams_id IN
                (SELECT teams_id FROM team_members WHERE person_id = $personId AND teams_id IN (SELECT teams_id from referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL))
                
                AND
                
                (([referralTeamAccess] = 1
                and R.is_reason_routed = 0)
                OR ([referralTeamAccessReasonRouted] = 1
                and R.is_reason_routed = 1))
                
                )
                
                ELSE
                CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $personId
                ELSE
                (
                R.access_public = 1
                 AND
                 ((([referralPublicAccess] = 1
                                and R.is_reason_routed = 0)
                                OR ([referralPublicAccessReasonRouted] = 1
                                and R.is_reason_routed = 1))
                                OR R.person_id_faculty = $personId)
                )
                
                
                END END END ) $rAddYear $rfacultyAccess
                )
                  union 
                ( 
                SELECT 
                    R.id AS activity_id, 
                    PS.id as student_id,
                    PS.firstname as student_firstname, 
                    PS.lastname as student_lastname,  
                    P.firstname AS faculty_firstname,     
                    P.lastname AS faculty_lastname,
                    R.note AS details, 
                    'Referral' as activity_type,
                    '' as activity_status, 
                    R.created_at AS activity_date,
                    CONCAT(P.firstname , ' ' ,P.lastname) as created_by
                FROM 
                
                referrals_interested_parties AS RIP 
                LEFT JOIN referrals AS R ON RIP.referrals_id = R.id 
                LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id 
                LEFT JOIN person AS P ON R.person_id_faculty = P.id 
                LEFT JOIN person AS PS on PS.id = R.person_id_student
                WHERE  RIP.person_id = $personId AND R.deleted_at IS NULL 
                AND P.deleted_at IS NULL 
                AND PS.deleted_at IS NULL 
                AND RIP.deleted_at IS NULL
                
                AND 
                CASE WHEN R.access_team = 1 THEN 
                
                (
                RT.teams_id IN 
                (SELECT teams_id FROM team_members WHERE person_id = $personId AND teams_id IN (SELECT teams_id from referrals_teams WHERE referrals_id = R.id AND deleted_at IS NULL)) 
                
                AND 
                
                (([referralTeamAccess] = 1
                and R.is_reason_routed = 0)
                OR ([referralTeamAccessReasonRouted] = 1
                and R.is_reason_routed = 1))
                
                )
                
                ELSE 
                CASE WHEN R.access_private = 1 THEN R.person_id_faculty = $personId 
                ELSE 
                ((
                R.access_public = 1 
                 AND 
                 (([referralPublicAccess] = 1
                                and R.is_reason_routed = 0)
                                OR ([referralPublicAccessReasonRouted] = 1
                                and R.is_reason_routed = 1))
                )
                OR R.person_id_faculty = $personId)
                
                
                END END 
                
                
                
                $rAddYear  $rfacultyAccess ) ) as derived Group by activity_id ";

                        $tempsql['contact'] = " [SELECT] 
                    C.id AS activity_id, 
                    PS.id as student_id,
                    PS.firstname as student_firstname, 
                    PS.lastname as student_lastname, 
                    P.firstname AS faculty_firstname,     
                    P.lastname AS faculty_lastname, 
                    C.note AS details,
                    'Contact' as activity_type,
                    '' as activity_status,
                    C.contact_date AS activity_date,
                    CONCAT(P.firstname , ' ' ,P.lastname) as created_by
                FROM activity_log AS AL 
                LEFT JOIN contacts AS C ON AL.contacts_id = C.id 
                LEFT JOIN person AS P ON C.person_id_faculty = P.id 
                LEFT JOIN person AS PS on PS.id = C.person_id_student
                LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id 
                LEFT JOIN related_activities as RA ON C.id = RA.contacts_id 
                LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id 
                LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id 
                WHERE C.person_id_student in (SELECT DISTINCT merged.student_id
                        FROM
                            (SELECT student_id,permissionset_id 
                                FROM  org_faculty_student_permission_map 
                                WHERE faculty_id = $personId AND org_id = $orgId
                             ) AS merged
                        INNER JOIN org_person_student AS P ON P.person_id=merged.student_id AND P.deleted_at IS NULL AND P.organization_id = $orgId
                        INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id AND OPS.accesslevel_ind_agg = 1)
                        AND AL.deleted_at IS NULL AND C.deleted_at IS NULL AND C.organization_id = $orgId AND ( 
            CASE WHEN AL1.activity_type IS NOT NULL AND  (AL1.activity_type = 'C' AND C1.access_private = 1)  THEN C.person_id_faculty = $personId ELSE 
            CASE WHEN C.access_team = 1 THEN CT.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $personId AND teams_id IN (SELECT teams_id from contacts_teams WHERE contacts_id = C.id AND deleted_at IS NULL)) AND  $contactTeamAccess = 1 ELSE 
            CASE WHEN C.access_private = 1 THEN C.person_id_faculty = $personId ELSE ((C.access_public = 1 AND $contactPublicAccess  = 1) OR C.person_id_faculty = $personId) END END END ) $cAddYear $cfacultyAccess Group by C.id
            ";

        $tempsql['note'] = " [SELECT]      
                        	N.id AS activity_id,
                        	PS.id as student_id,  
                           	PS.firstname as student_firstname, 
                        	PS.lastname as student_lastname,  
                        	P.firstname AS faculty_firstname,     
                        	P.lastname AS faculty_lastname, 
                        	N.note AS details, 
                            'Note' as activity_type,
                        	'' as activity_status,    
                            N.note_date AS activity_date,
                            CONCAT(P.firstname , ' ' ,P.lastname) as created_by
                            FROM     activity_log AS AL  
                            LEFT JOIN     note AS N ON AL.note_id = N.id  
                            LEFT JOIN     person AS P ON N.person_id_faculty = P.id 
                            LEFT JOIN     person AS PS on PS.id = N.person_id_student 
                            LEFT JOIN     note_teams AS NT ON N.id = NT.note_id  
                            LEFT JOIN     related_activities AS RA ON N.id = RA.note_id  
                            LEFT JOIN     activity_log AL1 ON RA.activity_log_id = AL1.id  
                            LEFT JOIN     note AS N1 ON AL1.note_id = N1.id  
                            WHERE     AL.person_id_student in (SELECT DISTINCT merged.student_id
                            FROM
                                (
                                    SELECT student_id,permissionset_id 
                                    FROM  org_faculty_student_permission_map 
                                    WHERE faculty_id = $personId AND org_id = $orgId
                                 ) AS merged
                            INNER JOIN org_person_student AS P ON P.person_id=merged.student_id AND P.deleted_at IS NULL AND P.organization_id = $orgId
                            INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id AND OPS.accesslevel_ind_agg = 1) 
                AND AL.deleted_at IS NULL  AND N.deleted_at IS NULL AND N.organization_id = $orgId AND (
                CASE  WHEN      AL1.activity_type IS NOT NULL   AND ( AL1.activity_type = 'N'   AND N.access_private = 1)  THEN      N.person_id_faculty = $personId  ELSE CASE      WHEN   N.access_team = 1      THEN   NT.teams_id IN (SELECT     teams_id       FROM    team_members       WHERE    person_id = $personId AND teams_id IN (SELECT      teams_id FROM     note_teams WHERE     note_id = N.id AND deleted_at IS NULL)) AND $noteTeamAccess  = 1 ELSE CASE   WHEN N.access_private = 1 THEN N.person_id_faculty = $personId   ELSE ((N.access_public = 1 AND $notePublicAccess = 1) OR N.person_id_faculty = $personId) END  END     END  ) $nAddYear $nfacultyAccess GROUP BY N.id ";

        $tempsql['appointment'] = " [SELECT]
                                        A.id AS activity_id,
                                        PS.id as student_id,
                                        PS.firstname as student_firstname,
                                        PS.lastname as student_lastname,
                                        P.firstname AS faculty_firstname,
                                        P.lastname AS faculty_lastname,
                                        A.description AS details,
                                        'Appointment' as activity_type,
                                        '' as activity_status,
                                        A.start_date_time AS activity_date,
                                        CONCAT(P1.firstname , ' ' ,P1.lastname) as created_by
                                        FROM     activity_log AL
                                        LEFT JOIN    appointment_recepient_and_status ARS ON (AL.appointments_id = ARS.appointments_id)        AND (ARS.deleted_at IS NULL)
                                        LEFT JOIN    Appointments A ON (ARS.appointments_id = A.id)        AND (A.deleted_at IS NULL)
                                        LEFT JOIN     appointments_teams as APT ON A.id = APT.appointments_id
                                        LEFT JOIN     person AS P ON ARS.person_id_faculty = P.id
                                        LEFT JOIN     person AS P1 ON A.person_id = P1.id
                                        LEFT JOIN     person AS PS on PS.id = ARS.person_id_student
                                        LEFT JOIN     related_activities AS RA ON A.id = RA.appointment_id
                                        LEFT JOIN     activity_log AL1 ON RA.activity_log_id = AL1.id
                                        LEFT JOIN     Appointments AS A1 ON AL1.note_id = A1.id
                                        WHERE
                                        (AL.person_id_student in (SELECT DISTINCT merged.student_id
                                        FROM
                                        ( 
                                            SELECT student_id,permissionset_id 
                                            FROM  org_faculty_student_permission_map 
                                            WHERE faculty_id = $personId AND org_id = $orgId
                                        ) AS merged
                                        INNER JOIN org_person_student AS P ON P.person_id=merged.student_id AND P.deleted_at IS NULL AND P.organization_id = $orgId
                                        INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id AND OPS.accesslevel_ind_agg = 1)
                                        AND AL.organization_id = $orgId
                                        AND AL.activity_type = 'A')
                                        AND (AL.deleted_at IS NULL)
                                        AND (CASE
                                        WHEN
                                        A.access_team = 1
                                        THEN
                                        APT.teams_id IN (SELECT
                                        teams_id
                                        FROM
                                        team_members
                                        WHERE
                                        person_id = $personId) and $appointmentTeamAccess = 1
                                        ELSE CASE
                                        WHEN A.access_private = 1 THEN ARS.person_id_faculty = $personId
                                        ELSE ((A.access_public = 1  and $appointmentPublicAccess = 1)
                                              OR ARS.person_id_faculty = $personId)
                                        END
                                        END
                                        )
                                        $aAddYear $afacultyAccess
                                        GROUP BY AL.appointments_id ";

                                        $tempsql['email'] = " [SELECT] 
                                    E.id AS activity_id, 
                                    PS.id as student_id,
                                    PS.firstname as student_firstname, 
                                    PS.lastname as student_lastname,  
                                    P.firstname AS faculty_firstname,     
                                    P.lastname AS faculty_lastname,
                                    E.email_subject AS details, 
                                    'Email' as activity_type,
                                    '' as activity_status, 
                                    E.created_at AS activity_date,
                                    CONCAT(P.firstname , ' ' ,P.lastname) as created_by
                                FROM activity_log AS AL 
                                LEFT JOIN email AS E ON AL.email_id = E.id 
                                LEFT JOIN person AS P ON E.person_id_faculty = P.id 
                                LEFT JOIN person AS PS on PS.id = E.person_id_student 
                                LEFT JOIN email_teams AS ET ON E.id = ET.email_id 
                                LEFT JOIN related_activities as RA ON E.id = RA.email_id 
                                LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id 
                                LEFT JOIN email AS E1 ON AL1.appointments_id = E1.id 
                                WHERE E.organization_id = $orgId AND E.person_id_student in (SELECT DISTINCT merged.student_id
                                            FROM
                                                (  
                                                    SELECT student_id,permissionset_id 
                                                    FROM  org_faculty_student_permission_map 
                                                    WHERE faculty_id = $personId AND org_id = $orgId
                                                 ) AS merged
                                            INNER JOIN org_person_student AS P ON P.person_id=merged.student_id AND P.deleted_at IS NULL AND P.organization_id = $orgId
                                            INNER JOIN org_permissionset OPS ON merged.permissionset_id = OPS.id AND OPS.accesslevel_ind_agg = 1) 
                                AND E.deleted_at IS NULL AND (
                                CASE WHEN AL1.activity_type IS NOT NULL AND ( AL1.activity_type = 'E' AND E1.access_private = 1) THEN E.person_id_faculty = $personId ELSE 
                                CASE WHEN E.access_team = 1 THEN ET.teams_id IN (SELECT teams_id FROM team_members WHERE person_id = $personId AND teams_id IN (SELECT teams_id from email_teams WHERE email_id = E.id AND deleted_at IS NULL)) AND $emailTeamAccess = 1 ELSE 
                                CASE WHEN E.access_private = 1 THEN E.person_id_faculty = $personId ELSE ((E.access_public = 1 AND $emailPublicAccess = 1) OR E.person_id_faculty = $personId) END END END ) $eAddYear $efacultyAccess GROUP BY E.id ";

        if ($type != NULL) {
            $sql = $tempsql[$type];

            if (empty($sortBy)) {
                $sql .= " order by activity_date DESC";
            } else {
                $sql .= " order by " . $this->getSortByField($sortBy);
            }

            if (strtoupper($type[0]) == "R") {

                $id = "activity_id";
            } else {
                $id = strtoupper($type[0]) . ".id";
            }
            $sql = str_replace('[SELECT]', "select  SQL_CALC_FOUND_ROWS  $id as rowcount , ", $sql);
        } else {
            $sql = implode('UNION ALL', $tempsql);

            $preSql = "SELECT  SQL_CALC_FOUND_ROWS activity_id as rowcount ,
                    activity_id,student_id,student_firstname,student_lastname,faculty_firstname,
                    faculty_lastname,details,activity_type,activity_status,activity_date,created_by
             FROM ( ";

            $participantStudentIds = implode(",", $participantStudents);


            if (empty($sortBy)) {
                $postSql = " )  as dv  WHERE student_id IN ( $participantStudentIds ) order by activity_date DESC ";
            } else {
                $postSql = " ) as dv WHERE student_id IN ( $participantStudentIds ) order by " . $this->getSortByField($sortBy);
            }

            $sql = $preSql . $sql . $postSql;

            $sql = str_replace('[SELECT]', "SELECT", $sql);
        }

        if ($pageNumber != NULL || $recordsPerPage != NULL) {
            $limit = $this->getLimit($pageNumber, $recordsPerPage);
            $sql = $sql . $limit;
        }

        $this->logger->debug("ReportsHelperService->getCampusActivity - sql:\r\n $sql");

        // reason routed permission fix

        $sql = str_replace("[referralTeamAccess]", $referralTeamAccess, $sql);
        $sql = str_replace("[referralPublicAccess]", $referralPublicAccess, $sql);
        $sql = str_replace("[referralPublicAccessReasonRouted]", $referralPublicAccessReasonRouted, $sql);
        $sql = str_replace("[referralTeamAccessReasonRouted]", $referralTeamAccessReasonRouted, $sql);

        // reason routed permission fix
        $records = $this->reportsRepo->executeQueryFetchAll($sql);

        $countQuery = "SELECT FOUND_ROWS() cnt";
        $cntQuery = $this->reportsRepo->executeQueryFetch($countQuery);
        $totalActCount = $cntQuery['cnt'];

        $finalArr['records'] = $records;
        $finalArr['total_count'] = $totalActCount;
        return $finalArr;
    }

    /**
     * Get sort by field for the activity download
     *
     * @param string $sortBy
     * @return string
     */
    private function getSortByField($sortBy)
    {
        $sortableFields = array(
            'activity_type' => ' activity_type [SORT_ORDER]',
            'activity_created_by' => ' faculty_firstname [SORT_ORDER], faculty_lastname [SORT_ORDER]',
            'activity_created_on' => ' activity_date [SORT_ORDER]',
            'student_name' => ' student_lastname [SORT_ORDER], student_firstname [SORT_ORDER]'
        );
        
        $sortOrder = '';
        if (($sortBy[0] == '+') || ($sortBy[0] == '-')) {
            
            if ($sortBy[0] == '-') {
                $sortOrder = ' desc';
            }
            
            $sortBy = substr($sortBy, 1, strlen($sortBy));
        }
        
        return str_replace('[SORT_ORDER]', $sortOrder, $sortableFields[$sortBy]);
    }


    private function getLimit($pageNo, $offset)
    {
        $startPoint = ($pageNo * $offset) - $offset;
        return " LIMIT $startPoint , $offset ";
    }


    /**
     * Download the key for ISQ type questions.
     *
     * @param int $surveyId
     * @param filePointer $csvFilePath  
     * @param int $orgId
     * write the data in CSV.
     */     
    protected function downloadOrgQuestionKey($surveyId, $csvFilePath, $orgId, $index, $cohortId)
    {
        $surveyOrgQuestions = $this->surveyQuestionsRepo->getOrgQuestionsForSurvey($surveyId, $orgId, $cohortId);
        $questionTypes = [
            ReportsConstants::KEY_CATEGORY => ReportsConstants::CATEGORY,
            ReportsConstants::KEY_SCALED => ReportsConstants::SCALED,
            ReportsConstants::KEY_MR => ReportsConstants::MULTIRESPONSE,
            ReportsConstants::KEY_LA => ReportsConstants::LONGANSWER,
            ReportsConstants::KEY_SA => ReportsConstants::SHORTANSWER,
            ReportsConstants::KEY_NUMERIC => ReportsConstants::NUMERIC
        ];
        $noOptions = [
            ReportsConstants::KEY_LA,
            ReportsConstants::KEY_SA,
            ReportsConstants::KEY_NUMERIC
        ];
        if (! empty($surveyOrgQuestions)) {
            foreach ($surveyOrgQuestions as $orgQuestion) {
                $type = $orgQuestion[ReportsConstants::ORG_QUESTION_TYPE];
                $orgQuestionId = $orgQuestion[ReportsConstants::ORG_QUESTION_ID];
                $question_type = $questionTypes[$type];
                $question = $orgQuestion[ReportsConstants::ORG_QUESTION_TEXT];
                $orgQuestionOptions = $this->surveyQuestionsRepo->getOrgQuestionOptions($surveyId, $orgQuestionId);
                $org_question_id = $index.'-'.'ISQ '.$orgQuestionId;
                if (! empty($orgQuestionOptions)) {
                    if (in_array($type, $noOptions)) {
                        $orgQuestions = [
                            $org_question_id,
                            $question,
                            '',
                            '',
                            $question_type,
                            '',
                            ''
                        ];
                        @fputcsv($csvFilePath, $orgQuestions);
                    } else {
                        foreach ($orgQuestionOptions as $orgQuestionOption) {
                            $option_value = ($type == ReportsConstants::KEY_MR) ? $orgQuestionOption[ReportsConstants::SEQUENCE] : $orgQuestionOption[ReportsConstants::ORG_OPTION_VALUE];                           
                            $orgQuestions = [
                                $org_question_id,
                                $question,
                                $option_value,
                                $orgQuestionOption[ReportsConstants::ORG_OPTION_TEXT],
                                $question_type,
                                '',
                                ''
                            ];
                            @fputcsv($csvFilePath, $orgQuestions);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Download the key for Factor questions.
     *
     * @param int $surveyId
     * @param filePointer $csvFilePath     
     * write the data into CSV.
     */
    public function downloadFactorKey($surveyId, $csvFilePath, $index)
    {
        $this->dataBlockQuestionRepo = $this->repositoryResolver->getRepository(ReportsConstants::DATABLOCK_QUESTION_REPO);
        $surveyFactors = $this->dataBlockQuestionRepo->getFactorForSurvey($surveyId);
        if (! empty($surveyFactors)) {
            foreach ($surveyFactors as $surveyFactor) {
                /**
                 * Key download file will have 7 columns which is for ebi, isq and factor
                 * First five columns are for ebi and isq.
                 * While listing factor, those five columns will be empty,
                 * and factor details (id, name) will be shown in sixth and seventh columns
                 */
                $factorId = $index.'-Factor '.$surveyFactor[ReportsConstants::FACTOR_ID];
                $survey_factors = [
                    '',
                    '',
                    '',
                    '',
                    '',
                    $factorId,
                    $surveyFactor[ReportsConstants::FACTOR_NAME]
                ];
                fputcsv($csvFilePath, $survey_factors);
            }
        }
    }
    
    /**
     * Download the key Survey Questions.
     *
     * @param int $survey_id
     * @param filePointer $csvFilePath     
     * write survey questions into CSV.
     */
    
    public function downloadEbiQuestionKey($survey_id, $csvFilePath)
    {        
        $this->surveyQuestionsRepo = $this->repositoryResolver->getRepository(ReportsConstants::SURVEY_QUESTIONS_REPOR);
        $questionTypes = [
                ReportsConstants::KEY_CATEGORY => ReportsConstants::CATEGORY,
                ReportsConstants::KEY_SCALED => ReportsConstants::SCALED,
                ReportsConstants::KEY_MR => ReportsConstants::MULTIRESPONSE,
                ReportsConstants::KEY_LA => ReportsConstants::LONGANSWER,
                ReportsConstants::KEY_SA => ReportsConstants::SHORTANSWER,
                ReportsConstants::KEY_NUMERIC => ReportsConstants::NUMERIC
            ];            
        $noOptions = [
                ReportsConstants::KEY_LA,
                ReportsConstants::KEY_SA,
                ReportsConstants::KEY_NUMERIC
            ];
        $surveysQues = $this->surveyQuestionsRepo->getUniqueSurveyQuestionsForCohort($survey_id); 
        foreach($surveysQues as $surveyQuestion) {              
            $surveyId = $surveyQuestion['survey_id'];                        
            $type = $surveyQuestion['question_type'];
            $questionType = $questionTypes[$type];
            if (in_array($type, $noOptions)) {                    
                $ques = [
                            $surveyQuestion['qnbr'],
                            $surveyQuestion['ebi_ques_text'],
                            '',
                            '',
                            $questionType,
                            '',
                            ''
                        ];
                fputcsv($csvFilePath, $ques);
            } else {
                $questionOptions = $this->surveyQuestionsRepo->getOptionsForSurveyQuestions($surveyId, $surveyQuestion['ebi_question_id']);
                if(!empty($questionOptions))
                {
                    foreach($questionOptions as $options) {
                        $ques = [
                                    $surveyQuestion['qnbr'],
                                    $surveyQuestion['ebi_ques_text'],
                                    $options['ebi_option_value'],
                                    $options['ebi_option_text'],
                                    $questionType,
                                    '',
                                    ''
                                ];
                        fputcsv($csvFilePath, $ques);
                    }
                }
            }
        }
    }
}