<?php
namespace Synapse\CoreBundle\Util\Constants;

class GroupUploadConstants
{

    const GROUP_AMAZON_URL = 'https://ebi-synapse-bucket.s3.amazonaws.com/group-uploads/';

    const AMAZONSECRET = 'amazon_s3.secret';

    const GROUP_DIR = 'group_uploads/';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const MODULE_NAME = "UploadBundle:SubgroupUpload:";

    /**
     * Sub Group Upload
     */
    const PARENT_GROUP_ID = "ParentGroupId";

    const GROUP_ID = "GroupId";

    const GROUP_NAME = "GroupName";


    /**
     * Sub Group Upload
     */
    const PARENT_GROUP_ID_LOWER = "parentgroupid";

    const GROUP_ID_LOWER = "groupid";

    const GROUP_NAME_LOWER = "groupname";

    /**
     * Faculty Upload
     */
    const EXTERNAL_ID = "ExternalId";
    
    const FIRSTNAME = "FirstName";

    const LASTNAME = "LastName";

    const FULL_PATH_NAMES  = "FullPathNames";

    const FULL_PATH_GROUP_IDS = "FullPathGroupIDs";

    const REMOVE = "Remove";

    const CLEAR = "#clear";

    const PERMISSION_SET = "PermissionSet";

    const INVISIBLE = "Invisible";



    const EXTERNAL_ID_LOWER = "externalid";

    const REMOVE_LOWER = "remove";

    const PERMISSION_SET_LOWER = "permissionset";

    const INVISIBLE_LOWER = "invisible";

    /**
     * Manage Group Student Upload
     * (Also uses External_ID from Faculty Upload)
     */

     const GROUP_EXTERNAL = 'GroupID';

     const PRIMARY_EMAIL = "PrimaryEmail";

}