<?php
namespace Synapse\CoreBundle\Util\Constants;

class TeamsConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const TEAM_MEMBER_REPO = 'SynapseCoreBundle:TeamMembers';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const TEAM_REPO = 'SynapseCoreBundle:Teams';

    const FIELD_PERSONID = "person_id";

    const FIELD_ISLEADER = "is_leader";

    const FIELD_STAFF = "staff";

    const ERROR_TEAM_NOT_FOUND = "Team Not Found.";

    const ERROR_TEAM_NOT_FOUND_KEY = "team_not_found";

    const DATE_FORMAT = "Y-m-d";

    const FIELD_LOGINS = "logins";

    const FIELD_TEAM_LASTNAME = "team_member_lastname";

    const FIELD_STUDENT_LASTNAME = "student_lastname";

    const FIELD_ACTIVITY = "activity";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_PERSON_STUDENT_REPO = 'SynapseCoreBundle:OrgPersonStudent';
    
    const CUSTOM = "custom";
    
    const FIELD_SELECT_COLS = 't.id as team_id,t.teamName as team_name';
    
    const TEAM_ID_COM = 't.id = tm.teamId';
    
    const TEAM_PERSON_ID_COM = 'tm.person = :pid';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const REFERRAL_REPO = "SynapseCoreBundle:Referrals";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const CONTACT_REPO = "SynapseCoreBundle:Contacts";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const NOTE_REPO = "SynapseCoreBundle:Note";

    const TEAM_SHARING_ERROR_MESSAGE = "Team Sharing Option cannot be selected without also choosing a team.";
}