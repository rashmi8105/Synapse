<?php
namespace Synapse\CoreBundle\Util\Constants;

class StudentConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = 'SynapseCoreBundle:Person';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITY_LOG_SERVICE = "SynapseCoreBundle:ActivityLog";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_LIST_REPO = 'SynapseCoreBundle:MetadataListValues';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_SEARCH_REPO = 'SynapseCoreBundle:EbiSearch';

    const INVALID_STUDENT = 'Not a valid Student Id.';

    const INVALID_STUDENT_KEY = 'Not a valid Student Id';

    const RELATED_ACTIVITY = 'related_activities';

    const RELATED_ACTIVITY_ID = 'related_activity_id';

    const ACTIVITY_DATE = 'activity_date';

    const ACTIVITY_ID = 'activity_id';

    const ACTIVITY_LOG_ID = 'activity_log_id';

    const ACTIVITY_TYPE = 'activity_type';

    const ACTIVITY_CONTACT_TYPE_TEXT = 'activity_contact_type_text';

    const ACTIVITY_CONTACT_TYPE_ID = 'activity_contact_type_id';

    const ACTIVITY_REFERRAL_STATUS = 'activity_referral_status';

    const ACTIVITY_DESCRIPTION = 'activity_description';

    const ACTIVITY_REASON_TEXT = 'activity_reason_text';

    const ACTIVITY_CREATED_LAST_NAME = 'activity_created_by_last_name';

    const ACTIVITY_CREATED_FIRST_NAME = 'activity_created_by_first_name';

    const ACTIVITY_CREATED_BY_ID = 'activity_created_by_id';

    const ACTIVITY_REASON_ID = 'activity_reason_id';

    const ORGANIZATION = 'organization';

    const PERSON_STUDENT_ID = 'personIdStudent';

    const PERSON_STUDENT = 'personStudent';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    const START_DATE = 'startDate';

    const MY_ANS = 'myanswer';

    const BLOCK_DESC = 'blockdesc';

    const CONTACT = 'Contact';

    const STUD_ID = 'studentId';

    const ACTIVITY_ARR = 'acivityArr';

    const FACULTY = 'faculty';

    const ORGID = 'orgId';

    const PERMISSION_ACCESS = 'No Permission Access';

    const TEAM_VIEW = 'team_view';

    const PUBLIC_VIEW = 'public_view';

    const LOG_CONTACTS = 'Log Contacts';

    const TEAM_ACCESS = 'teamAccess';

    const PUBLIC_ACCESS = 'publicAccess';

    const NOTE_TEAM_ACCESS = 'noteTeamAccess';

    const REFERRALS = 'Referrals';
    
    const REFERRALS_REASON_ROUTED = 'Referrals Reason Routed';
    
    const PUBLIC_ACCESS_REASON_ROUTED = 'publicAccessReasonRouted';
    
    const TEAM_ACCESS_REASON_ROUTED = 'teamAccessReasonRouted';

    const NOTES = 'Notes';
    
    const BOOKING = 'Booking';
    
    const EMAIL = 'Email';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const ACTIVITY_SERVICE = 'activity_service';

    const RISK_LEVEL = 'risk_level';

    const RISK_IMAGENAME = 'risk_imagename';

    const INTENT_IMAGENAME = 'intent_imagename';
	
	const QUERY_ERROR = 'System encountered an unexpected error. Please contact Mapworks support team.';
}