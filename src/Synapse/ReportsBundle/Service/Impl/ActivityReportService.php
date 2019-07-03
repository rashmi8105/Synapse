<?php
namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\RestBundle\Exception\ValidationException;

use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * @DI\Service("activity_report_service")
 */
class ActivityReportService extends AbstractService
{

    const SERVICE_KEY = 'activity_report_service';

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            "cache" = @DI\Inject("synapse_redis_cache"),
     *            "resque" = @DI\Inject("bcc_resque.resque")
     *            })
     */
    public function __construct($repositoryResolver, $logger , $container, $cache , $resque)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->cache = $cache;
        $this->resque = $resque;
    
    }
    
    public function initiateReportJob($reportRunningDto,$loggedUserId,$orgId,$reportInstanceId,$rawData){

        $getreportSections = $reportRunningDto->getReportSections();
        $dateRange = $reportRunningDto->getActivityReport();
        
        $startDate = $dateRange['start_date'];
        $endDate = $dateRange['end_date'];
        
        $jobObj = 'Synapse\ReportsBundle\Job\ActivityReportJob';
        $jobNumber = uniqid();
        $job = new $jobObj();

        $perObj = $this->container->get('person_service')->find($loggedUserId);
        $firstname =  $perObj->getFirstname();
        $lastname =  $perObj->getLastname();
 
        $job->args = array(
            
            'start_date' => $startDate,
            'end_date' => $endDate,
            'userId' => $loggedUserId,
            'orgId' => $orgId,
            'reportId' => $reportRunningDto->getReportId(),
            'reportRunByLastName' => $lastname,
            'reportRunByFirstName' => $firstname,
            'reportInstanceId' => $reportInstanceId,
            'reportSections' => $getreportSections,
            'requestJson' => $rawData
        );
        $this->jobs[$jobNumber] = $this->resque->enqueue($job, true);
    }
    
    public function objToArr($template){
        $encoders = array(
            new JsonEncoder()
        );
        $normalizers = array(
            new GetSetMethodNormalizer()
        );
        $serializer = new Serializer($normalizers, $encoders);
        $template = $serializer->serialize($template, 'json');
        $template = json_decode($template, true);
        
        return $template;
    }

    /**
     * Gets the mapworks activity report section query based on the passed in key.
     * TODO:: Convert these into repository functions.
     *
     * @param $key
     * @deprecated
     * @return string
     */
    public function getActivityReportSectionQueries($key)
    {

        $queryArray = array(

            "TopActivity" => "

select 'referral' as activity_type, count(id) total_count
from referrals where deleted_at is null and organization_id = {org_id}

and date(referrals.created_at) between {reporting_start_date} and {reporting_end_date}
##and referrals.person_id_student in ({reporting_on_student_ids})
##and referrals.person_id_faculty in ({reporting_on_faculty_ids})
##

UNION ALL

SELECT 'appointment' as activity_type, count(distinct(a.id)) total_appointments
FROM Appointments a
left join appointment_recepient_and_status as ars 
	on ars.appointments_id = a.id and (ars.person_id_student = a.person_id or ars.person_id_faculty = a.person_id)
where a.deleted_at is null and a.organization_id = {org_id}

and date(a.created_at) between {reporting_start_date} and {reporting_end_date}
##and (ars.person_id_student is not null and ars.person_id_student in ({reporting_on_student_ids}))
##and (ars.person_id_faculty is not null and ars.person_id_faculty in ({reporting_on_faculty_ids}))
##

UNION ALL

SELECT 'contact' as activity_type, count(id) total_contacts
FROM contacts c where deleted_at is null and organization_id = {org_id}

and date(c.created_at) between {reporting_start_date} and {reporting_end_date}
##and c.person_id_student in ({reporting_on_student_ids})
##and c.person_id_faculty in ({reporting_on_faculty_ids})
##

UNION ALL

SELECT 'academic_update' as activity_type, count(id) total_au
FROM academic_update au
where deleted_at is null and org_id = {org_id}

and date(au.update_date) between {reporting_start_date} and {reporting_end_date}
##and au.person_id_student in ({reporting_on_student_ids})
##and au.person_id_faculty_responded in ({reporting_on_faculty_ids})
" . ReportsConstants::MAX_SCALE_POSTFIX,

            // ---------------------------------------------------------
            "faculty" => "SELECT 
    'campus_connection' AS element_type,
    COUNT(DISTINCT (person_id)) AS total_staff
FROM
    (SELECT DISTINCT
        ogf.person_id
    FROM
        org_group_students ogs
    INNER JOIN org_group_tree ogt ON ogs.org_group_id = ogt.descendant_group_id
        AND ogt.deleted_at IS NULL
    INNER JOIN org_group_faculty ogf ON ogf.org_group_id = ogt.ancestor_group_id
        AND ogt.deleted_at IS NULL
    INNER JOIN org_permissionset op ON op.organization_id = ogs.organization_id
        AND ogf.org_permissionset_id = op.id
    WHERE
        (accesslevel_ind_agg = 1
            OR accesslevel_agg = 1)
            AND op.organization_id = {org_id}
            AND ogs.deleted_at IS NULL
            AND ogf.deleted_at IS NULL
            AND op.deleted_at IS NULL 
			##AND ogs.person_id IN ({reporting_on_student_ids}) 
	##
	UNION ALL SELECT DISTINCT
        ocf.person_id
    FROM
        org_course_student ocs
    INNER JOIN org_course_faculty ocf ON ocf.organization_id = ocs.organization_id
        AND ocf.org_courses_id = ocs.org_courses_id
    INNER JOIN org_permissionset op ON op.organization_id = ocs.organization_id
        AND ocf.org_permissionset_id = op.id
    INNER JOIN org_courses oc ON ocf.org_courses_id = oc.id
    INNER JOIN org_academic_terms oat ON oc.org_academic_terms_id = oat.id
    WHERE
        (accesslevel_ind_agg = 1
            OR accesslevel_agg = 1)
            AND date(now()) BETWEEN oat.start_date AND oat.end_date
            AND op.organization_id = {org_id}
            AND ocs.deleted_at IS NULL
            AND ocf.deleted_at IS NULL
            AND op.deleted_at IS NULL
            AND oc.deleted_at IS NULL
            AND oat.deleted_at IS NULL
			##AND ocs.person_id IN ({reporting_on_student_ids})
			##) AS merged
			##WHERE merged.person_id IN ({reporting_on_faculty_ids})
##
UNION ALL SELECT 
    'total' AS element_type, COUNT(opf.person_id) AS total_staff
FROM
    org_person_faculty AS opf
WHERE
    opf.organization_id = {org_id}
        AND opf.deleted_at IS NULL
        AND (opf.status = 1 OR opf.status IS NULL)
##
UNION ALL SELECT 
    'who_accessed_mapworks' AS element_type,
    COUNT(Lc.person_id) AS total_staff
FROM
    org_person_faculty AS opf
        INNER JOIN
    Logins_count AS Lc ON opf.person_id = Lc.person_id
WHERE
    opf.organization_id = {org_id}
        AND opf.deleted_at IS NULL
        AND (opf.status = 1 OR opf.status IS NULL) ",

            "faculty_part" => "

union all

SELECT 'who_accessed_mapworks' as element_type, count(opf.person_id) as total_staff, count(Lc.person_id) as accessed_staff
FROM org_person_faculty as opf
left join Logins_count as Lc on opf.person_id = Lc.person_id
where opf.organization_id = {org_id} AND opf.status = 1

",

            // ---------------------------------------------------------
            "student" => "

select 
    'student_viewed' as element_type,
    count(distinct (ops.person_id)) as total_students
from
    org_person_student ops
        inner join
    student_db_view_log sdvl ON sdvl.person_id_student = ops.person_id
		inner join
	org_person_faculty opf on opf.person_id = sdvl.person_id_faculty
where
	ops.organization_id = {org_id}
	and opf.organization_id = {org_id}
	and ops.deleted_at is null
	and opf.deleted_at is null
	and opf.status = 1
	and sdvl.deleted_at is null
	##and ops.person_id in ({reporting_on_student_ids})
            
##
union all

select
	'student_with_activity' as element_type,
	count(distinct(al.person_id_student)) as total_students
from
	activity_log al
    left join Appointments a 
        on a.id = al.appointments_id
	left join contacts c
        on c.id = al.contacts_id
where 
    activity_type in ('C' , 'N', 'A', 'R' ,'E')
    and al.deleted_at is null
	and ( 
        (al.activity_type = 'C' and date(c.created_at) between {reporting_start_date} and {reporting_end_date})
        or   
        (al.activity_type = 'A' and date(a.created_at) between {reporting_start_date} and {reporting_end_date})
        or
       (al.activity_type not in ('C','A') and date(al.created_at) between {reporting_start_date} and {reporting_end_date})
    )
	##and al.person_id_faculty in ({reporting_on_faculty_ids})
	##and al.person_id_student in ({reporting_on_student_ids})
            
##
union all

select
	'total' as element_type,
	count(distinct(person_id)) as total_students
from
	org_person_student as ops
where 
	ops.deleted_at IS NULL and ops.organization_id = {org_id}
	##and ops.person_id in ({reporting_on_student_ids})
" ,

            // ---------------------------------------------------------
            "ActivityOverview" => "

select
	case when actlog.activity_type = 'C'
		then 
			case when ct.parent_contact_types_id = 1
				then 'IC'
				else 'NIC'
			end
		else actlog.activity_type
		end as element_type,
	count( distinct(
        case when actlog.activity_type = 'A'
			then
				actlog.appointments_id
			else 
				actlog.id
		end)) as activity_count,
	count(distinct(actlog.person_id_student)) as student_count,
	0 as student_percentage,
	count(distinct(actlog.person_id_faculty)) as faculty_count,
	count(distinct(r.person_id_assigned_to)) as received_referrals
from
	activity_log as actlog
	left join referrals r on r.id = actlog.referrals_id
    left join Appointments a on a.id = actlog.appointments_id
	left join contacts c
		on c.id = actlog.contacts_id
	left join contact_types ct
		on ct.id = c.contact_types_id
		and ct.parent_contact_types_id in (1, 2)
where 
	actlog.organization_id = {org_id}
	and actlog.deleted_at is null
	and actlog.activity_type in ('N','C','A','R','E')
	and (
       (actlog.activity_type = 'C' and date(c.created_at) between {reporting_start_date} and {reporting_end_date})
        or   
        (actlog.activity_type = 'A' and date(a.created_at) between {reporting_start_date} and {reporting_end_date})
        or
       (actlog.activity_type not in ('C','A') and date(actlog.created_at) between {reporting_start_date} and {reporting_end_date})
    )
	##and (actlog.person_id_faculty in ({reporting_on_faculty_ids})
			or r.person_id_assigned_to in ({reporting_on_faculty_ids}))
	##and actlog.person_id_student in ({reporting_on_student_ids})
##
group by
	element_type

union all
            
SELECT 
            'C' as activity_type, 
            count(id) total_contacts,
            count(distinct(c.person_id_student)) as student_count,
            0 as student_percentage,
            count(distinct(c.person_id_faculty)) as faculty_count,
	        0 as received_referrals
            
FROM contacts c where deleted_at is null and organization_id = {org_id}

and date(c.created_at) between {reporting_start_date} and {reporting_end_date}
##and c.person_id_student in ({reporting_on_student_ids})
##and c.person_id_faculty in ({reporting_on_faculty_ids})
##
                        
union all

SELECT 
	'AU' as element_type,
	count(id) activity_count,
	count(distinct(person_id_student)) student_count,
	0 as student_percentage,
	count(distinct(person_id_faculty_responded)) faculty_count,
	0 as received_referrals
FROM academic_update au
where au.deleted_at is null and au.org_id = {org_id}

and date(au.update_date) between {reporting_start_date} and {reporting_end_date}
##and au.person_id_student in ({reporting_on_student_ids})
##and au.person_id_faculty_responded in ({reporting_on_faculty_ids})
" ,

            // ---------------------------------------------------------
            "ActivityByCategory" => "
            
select 
	'N' as activity_type,
	ac.short_name as element_type,
	count(n.id) activity_count
from 
	activity_category ac
	left join activity_category acd on acd.parent_activity_category_id = ac.id
	left join note n 
		on n.activity_category_id = acd.id
			and n.organization_id = {org_id}
			and n.deleted_at is null
where 
	ac.parent_activity_category_id is null 

and date(n.created_at) between {reporting_start_date} and {reporting_end_date}
##and n.person_id_student in ({reporting_on_student_ids})
##and n.person_id_faculty in ({reporting_on_faculty_ids})
##
group by ac.id, ac.short_name

union all

select 
	'C' as activity_type,
	ac.short_name as element_type,
	count(c.id) activity_count
from 
	activity_category ac
	left join activity_category acd on acd.parent_activity_category_id = ac.id
	left join contacts c 
		on c.activity_category_id = acd.id
			and c.organization_id = {org_id}
			and c.deleted_at is null
where 
	ac.parent_activity_category_id is null 

and date(c.created_at) between {reporting_start_date} and {reporting_end_date}
##and c.person_id_student in ({reporting_on_student_ids})
##and c.person_id_faculty in ({reporting_on_faculty_ids})
##
group by ac.id, ac.short_name

union all

select 
	'IC' as activity_type,
	ac.short_name as element_type,
	count(c.id) activity_count
from 
	activity_category ac
	left join activity_category acd on acd.parent_activity_category_id = ac.id
	left join contacts c 
		on c.activity_category_id = acd.id
			and c.organization_id = {org_id}
			and c.deleted_at is null
where 
	ac.parent_activity_category_id is null and c.contact_types_id in (select id from contact_types where parent_contact_types_id = 1)

and date(c.created_at) between {reporting_start_date} and {reporting_end_date}
##and c.person_id_student in ({reporting_on_student_ids})
##and c.person_id_faculty in ({reporting_on_faculty_ids})
##
group by ac.id, ac.short_name

union all

select 
	'R' as activity_type,
	ac.short_name as element_type,
	count(r.id) activity_count
from 
	activity_category ac
	left join activity_category acd on acd.parent_activity_category_id = ac.id
	left join referrals r 
		on r.activity_category_id = acd.id
			and r.organization_id = {org_id}
			and r.deleted_at is null
where 
	ac.parent_activity_category_id is null 

and date(r.created_at) between {reporting_start_date} and {reporting_end_date}
##and r.person_id_student in ({reporting_on_student_ids})
##and r.person_id_faculty in ({reporting_on_faculty_ids})
##
group by ac.id, ac.short_name

union all

select
	'A' as activity_type,
	ac.short_name as element_type,
	count(distinct(a.id)) activity_count
from activity_category ac
left join activity_category acd on acd.parent_activity_category_id = ac.id
left join Appointments a 
	on a.activity_category_id = acd.id
		and a.organization_id = {org_id}
		and a.deleted_at is null 
left join activity_log as ars 
	on ars.appointments_id = a.id and (ars.person_id_student = a.person_id or ars.person_id_faculty = a.person_id)
where 
	ac.parent_activity_category_id is null 

and date(a.created_at) between {reporting_start_date} and {reporting_end_date}
##and (ars.person_id_student is not null and ars.person_id_student in ({reporting_on_student_ids}))
##and (ars.person_id_faculty is not null and ars.person_id_faculty in ({reporting_on_faculty_ids}))
##
group by ac.id, ac.short_name            
" ,

            // ---------------------------------------------------------
            "referral" => "
select 
	count(id) total_referrals,
    sum(is_discussed) discussed_count,
	sum(is_leaving) intent_to_leave_count,
    sum(is_high_priority) high_priority_concern_count ,
	SUM(CASE 
		WHEN status = 'o'
			THEN 1
				 ELSE 0
			   END)  number_open_count 
from 
	referrals 
where 
	deleted_at is null and organization_id = {org_id}

and date(referrals.created_at) between {reporting_start_date} and {reporting_end_date}
##and referrals.person_id_student in ({reporting_on_student_ids})
##and referrals.person_id_faculty in ({reporting_on_faculty_ids})
" ,

            // ---------------------------------------------------------
            "appointments" => "
           
SELECT 
	(SELECT  count(distinct(a.id)) total_appointments
FROM Appointments a
left join appointment_recepient_and_status as ars 
	on ars.appointments_id = a.id and (ars.person_id_student = a.person_id or ars.person_id_faculty = a.person_id)
where a.deleted_at is null and a.organization_id = {org_id}

and date(a.created_at) between {reporting_start_date} and {reporting_end_date}
##and (ars.person_id_student is not null and ars.person_id_student in ({reporting_on_student_ids}))
##and (ars.person_id_faculty is not null and ars.person_id_faculty in ({reporting_on_faculty_ids}))
) as total_appointments,
            
(select count(id)  from (
SELECT 
	a.id ,ars.person_id_student,ars.has_attended
	
FROM 
	Appointments a
inner join appointment_recepient_and_status as ars on a.id = ars.appointments_id 

where 
	a.deleted_at is null 
	and a.organization_id = {org_id}
and has_attended = 1
and date(a.created_at) between {reporting_start_date} and {reporting_end_date}
##and ars.person_id_student in ({reporting_on_student_ids})
##
group by  a.id , ars.has_attended
) as dv1 ) AS completed_count,
SUM(CASE 
		WHEN (a.person_id = opf.person_id) THEN 1
		ELSE 0
	END) AS staff_initiated_count,
	SUM(CASE 
		WHEN (a.person_id = ops.person_id) THEN 1
		ELSE 0
	END) AS student_initiated_count
FROM 
	Appointments a
	left join org_person_faculty opf on opf.person_id = a.person_id
    left join org_person_student ops on ops.person_id = a.person_id
    
where 
	a.deleted_at is null 
	and a.organization_id = {org_id}
    and a.id IN (
        SELECT  distinct(a.id) total_appointments
        FROM Appointments a
        left join appointment_recepient_and_status as ars 
        	on ars.appointments_id = a.id and (ars.person_id_student = a.person_id or ars.person_id_faculty = a.person_id)
        where a.deleted_at is null and a.organization_id = {org_id}
        
        and date(a.created_at) between {reporting_start_date} and {reporting_end_date}
        ##and (ars.person_id_student is not null and ars.person_id_student in ({reporting_on_student_ids}))
        ##and (ars.person_id_faculty is not null and ars.person_id_faculty in ({reporting_on_faculty_ids}))
    )
    AND (opf.status = 1 OR opf.status IS NULL)

",

            // ---------------------------------------------------------
            "contacts" => "
SELECT 
	count(id) total_contacts,
	SUM(CASE 
		WHEN contact_types_id in (select id from contact_types where parent_contact_types_id = 1) THEN 1
		ELSE 0
	END) AS interaction_contacts_count,
	SUM(CASE 
		WHEN contact_types_id in (select id from contact_types where parent_contact_types_id = 2) THEN 1
		ELSE 0
	END) AS non_interaction_contacts_count
FROM 
	contacts c 
WHERE 
	deleted_at is null and organization_id = {org_id}

and date(c.created_at) between {reporting_start_date} and {reporting_end_date}
##and c.person_id_student in ({reporting_on_student_ids})
##and c.person_id_faculty in ({reporting_on_faculty_ids})
",

            // ---------------------------------------------------------
            "Au" => "
SELECT 
    COUNT(id) AS total_au,
	SUM(CASE 
		WHEN failure_risk_level ='High' THEN 1
		ELSE 0
	END) AS failure_risk_level_count,
	SUM(CASE 
		WHEN grade in ('D' ,'D-','F', 'No Pass','F/No Pass') THEN 1
		ELSE 0
	END) AS grade_df_count,
	COUNT(DISTINCT(person_id_student)) AS student_involved,
	COUNT(DISTINCT(person_id_faculty_responded)) AS faculty_logged
FROM 
    academic_update
WHERE 
    deleted_at IS NULL 
    AND org_id = {org_id}
    AND DATE(update_date) BETWEEN {reporting_start_date} AND {reporting_end_date}
    ##and person_id_student in ({reporting_on_student_ids})
    ##and person_id_faculty_responded in ({reporting_on_faculty_ids})

" ,

            // ---------------------------------------------------------
            "NotesByCategory" => "
select 
	'N' as activity_type,
	ac.short_name as element_type,
	count(n.id) activity_count
from 
	activity_category ac
	left join activity_category acd on acd.parent_activity_category_id = ac.id
	left join note n 
		on n.activity_category_id = acd.id
			and n.organization_id = {org_id}
			and n.deleted_at is null
where 
	ac.parent_activity_category_id is null 

and date(n.created_at) between {reporting_start_date} and {reporting_end_date}
##and n.person_id_student in ({reporting_on_student_ids})
##and n.person_id_faculty in ({reporting_on_faculty_ids})
##
group by ac.id, ac.short_name
" ,

            // ---------------------------------------------------------
            "ContactsByCategory" => "
select 
	'C' as activity_type,
	ac.short_name as element_type,
	count(c.id) activity_count
from 
	activity_category ac
	left join activity_category acd on acd.parent_activity_category_id = ac.id
	left join contacts c 
		on c.activity_category_id = acd.id
			and c.organization_id = {org_id}
			and c.deleted_at is null
where 
	ac.parent_activity_category_id is null 

and date(c.created_at) between {reporting_start_date} and {reporting_end_date}
##and c.person_id_student in ({reporting_on_student_ids})
##and c.person_id_faculty in ({reporting_on_faculty_ids})
##
group by ac.id, ac.short_name
" ,

            // ---------------------------------------------------------
            "ReferralsByCategory" => "
select 
	'R' as activity_type,
	ac.short_name as element_type,
	count(r.id) activity_count
from 
	activity_category ac
	left join activity_category acd on acd.parent_activity_category_id = ac.id
	left join referrals r 
		on r.activity_category_id = acd.id
			and r.organization_id = {org_id}
			and r.deleted_at is null
where 
	ac.parent_activity_category_id is null 

and date(r.created_at) between {reporting_start_date} and {reporting_end_date}
##and r.person_id_student in ({reporting_on_student_ids})
##and r.person_id_faculty in ({reporting_on_faculty_ids})
##
group by ac.id, ac.short_name
" ,

            // ---------------------------------------------------------
            "AppointmentsByCategory" => "
select
	'A' as activity_type,
	ac.short_name as element_type,
	count(distinct(a.id)) activity_count
from activity_category ac
left join activity_category acd on acd.parent_activity_category_id = ac.id
left join Appointments a 
	on a.activity_category_id = acd.id
		and a.organization_id = {org_id}
		and a.deleted_at is null 
left join activity_log as ars 
	on ars.appointments_id = a.id and (ars.person_id_student = a.person_id or ars.person_id_faculty = a.person_id)
where 
	ac.parent_activity_category_id is null 

and date(a.created_at) between {reporting_start_date} and {reporting_end_date}
##and (ars.person_id_student is not null and ars.person_id_student in ({reporting_on_student_ids}))
##and (ars.person_id_faculty is not null and ars.person_id_faculty in ({reporting_on_faculty_ids}))
##
group by ac.id, ac.short_name
"
        );
        return $queryArray[$key];
    }


    /**
     * Replaces the string replaceable items in the query for the mapworks activity report.
     * TODO:: When getActivityReportSectionQueries is eliminated, eliminate this.
     *
     * @param string $query
     * @param array $args
     * @deprecated
     * @return string
     */
    public function replacePlaceHoldersInQuery($query, $args)
    {

        $finalArr = array();
        $startDate = $args['start_date'];
        $endDate = $args['end_date'];

        $sqlDateForm = 'str_to_date( "[DATE]", "%Y-%m-%d")';

        $replaceArr = array(
            'org_id' => $args['orgId'],
            'reporting_start_date' => str_replace( '[DATE]', $startDate, $sqlDateForm),
            'reporting_end_date' => str_replace( '[DATE]', $endDate, $sqlDateForm),
            'reporting_on_student_ids' => $args['reporting_on_student_ids'],
            'reporting_on_faculty_ids' => $args['reporting_on_faculty_ids']
        );

        $queryArr = explode("##", $query);

        foreach ($queryArr as $section) {

            if (strpos($section, '{')) {

                foreach ($replaceArr as $key => $val) {
                    if (isset($val) && trim($val) != "") {
                        $section = str_replace("{" . $key . "}", $val, $section);
                    }
                }

                if (strpos($section, '{')) {} else {
                    $finalArr[] = $section;
                }
            } else {
                $finalArr[] = $section;
            }
        }
        $query = implode($finalArr, " ");

        return $query;
    }
}