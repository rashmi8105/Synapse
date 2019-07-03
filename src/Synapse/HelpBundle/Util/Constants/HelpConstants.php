<?php
namespace Synapse\HelpBundle\Util\Constants;

class HelpConstants
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGDOCUMENT_ENT = 'SynapseHelpBundle:OrgDocuments';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGANIZATION_ENT = 'SynapseCoreBundle:Organization';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const IDENTITYVALUES_ENT = 'SynapseCoreBundle:IdentityValues';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_LANG_REPO = 'SynapseCoreBundle:OrganizationLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBICONFIG_REPO = 'SynapseCoreBundle:EbiConfig';

    const ORGANIZATION_NOT_FOUND_CODE = 'organization_not_found';

    const ORGANIZATION_NOT_FOUND = 'Organization ID Not Found';

    const HELP_TYPE_LINK = 'link';

    const HELP_TYPE_FILE = 'file';

    const HELP_NOT_FOUND = 'Help Not Found';

    const HELP_NOT_FOUND_CODE = "help_not_found";

    const EBI_CONGIF_EMAIL_KEY = 'Coordinator_Support_Helpdesk_Email_Address';

    const EBI_CONGIF_PHONE_KEY = 'Coordinator_Support_Helpdesk_Phone_Number';

    const EBI_CONGIF_SANDBOX_SITE_KEY = 'Sandbox_Site_URL';

    const EBI_CONGIF_TRAINING_SITE_KEY = 'Training_Site_URL';

    const HELP_DB_EXCEPTION = "Database Error";

    const HELP_DB_EXCEPTION_CODE = "db_error";

    const HELP_ORGID = "orgId";

    const HELP_ID = "id";

    const HELP_TITLE = "title";

    const HELP_DESCRIPTION = "description";

    const HELP_TYPE = "type";

    const HELP_LINK = "link";

    const HELP_FILEPATH = "file_path";

    const HELP_DISPLAY_NAME = "display_filename";

    const COORDINATOR_ACCESS_DENIED = "You do not have coordinator access";

    const SUB_DOMAIN_KEY = "Sub_Domain";

    const ZEN_DESK_ERROR = "Ticket Not Create";

    const ZEN_DESK_ERROR_CODE = "Ticket_Not_Create";

    const ZEN_DESK_LIC_ERROR = "Ticket Not Create - ZenDesk Not Responding";

    const ZEN_DESK_LIC_ERROR_CODE = "Ticket_Not_Create_In_Zendesk";

    const ZEN_DESK_FILE_NOT_UPLOADED = "File Not Uploaded";
    
    const ZEN_DESK_CAT_ERROR = "Zendesk not configured properly. Please contact Mapworks Administrator.";
    const ZEN_DESK_CAT_ERROR_CODE = "zendesk_config.";
}
