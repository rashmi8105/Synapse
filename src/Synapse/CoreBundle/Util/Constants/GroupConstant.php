<?php
namespace Synapse\CoreBundle\Util\Constants;

class GroupConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_GROUP_REPO = "SynapseCoreBundle:OrgGroup";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_GROUP_FACULTY_REPO = "SynapseCoreBundle:OrgGroupFaculty";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_STUDENT_REPO = "SynapseCoreBundle:OrgGroupStudents";

    const ERROR_GROUP_NOT_FOUND = "Group Not Found.";

    const ERROR_GROUP_NOT_FOUND_KEY = "group_not_found";

    const ERROR_PERSON_NOT_FOUND = "Person Not Found.";

    const STAFF_ID = 'staff_id';

    const STAFF_PERMISSIONSET_ID = 'staff_permissionset_id';

    const STAFF_INVISIBLE = 'staff_is_invisible';

    const GROUP_STAFFID = 'group_staff_id';

    const ORGANIZATION = 'organization';

    const GROUP_ID = 'group_id';

    const PARENT_ID = 'parent_id';

    const GROUP_NAME = 'group_name';

    const ORGANIZATION_ID = 'organization_id';

    const SUBGROUPS = 'subgroups';

    const CREATEDAT = 'created_at';

    const MODIFIEDAT = 'modified_at';

    const SUBGROUP_STAFF_COUNT = 'subgroups_staff_count';

    const SUBGROUP_STUDENT_COUNT = 'subgroups_student_count';

    const PERSON_COUNT = 'personcount';

    const BREADCRUMP = 'bread_crump';

    const PARENT_GROUP_NAME = 'parent_group_name';

    const ORGGROUP = 'orgGroup';

    const ERROR_PERSON_NOT_FOUND_KEY = 'Person_not_found';

    const STAFF = 'staff';

    const STUDENT = 'student';

    /**
     * System Group
     */
    const SYS_GROUP_NAME = "All Students";

    const SYS_GROUP_EXTERNAL_ID = "ALLSTUDENTS";

    const EXTERNAL_ID = 'external_id';

    const PARENT_GROUP_ID = 'parent_group_id';

    const DATETIME = 'Y-m-d H:i:s';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_STUDENTREPO = "SynapseCoreBundle:OrgPersonStudent";
}