<?php
namespace Synapse\CoreBundle\Util\Constants;

class ActivityLogConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITYLOG_ENTITY = "SynapseCoreBundle:ActivityLog";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = "SynapseCoreBundle:Person";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITYCATEGORY_REPO = "SynapseCoreBundle:ActivityCategory";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const CONTACTTYPE_REPO = "SynapseCoreBundle:ContactTypes";

    const STUDENT_ID = "studentId";

    const ORG_ID = "orgId";

    const ACTIVITY_TYPE = "activityType";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const CONTACT_ENTITY = "SynapseCoreBundle:Contacts";

    const PERSONID_STUDID_COMPARE = 'AL.personIdStudent = :studentId';

    const AL_ORG_COMPARE = 'AL.organization = :orgId';

    const AL_ACTIVITY_TYPE = 'AL.activityType = :activityType';

    const AL_ID = 'AL.id';

    const AL_CREATED_AT = 'AL.createdAt';

    const AL_CONTACTS = 'AL.contacts = C.id';

    const C_CONTACT_TYPES_ID = 'C.contactTypesId = CT.id';
}