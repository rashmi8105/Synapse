<?php
namespace Synapse\CoreBundle\Util\Constants;

class ProxyConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = 'SynapseCoreBundle:Person';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_REPO = 'SynapseCoreBundle:Organization';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_USER_REPO = 'SynapseCoreBundle:EbiUsers';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PROXY_LOG_REPO = 'SynapseCoreBundle:ProxyLog';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    const PROXY_DB_EXCEPTION = "Database Error";

    const PROXY_DB_EXCEPTION_CODE = "db_error";

    const ORGANIZATION_NOT_FOUND_CODE = 'ESPRJ-1817 organization_not_found';

    const ORGANIZATION_NOT_FOUND = 'Organization ID Not Found';

    const USER_ACCESS_DENIED = "You do not have EBI Admin/Coordinator Access";

    const USER_ACCESS_DENIED_KEY = 'ESPRJ-1816 Logged User not have Admin/Coordinator Access';

    const EBI_USER_NOT_FOUND = 'EBI User Not found';

    const EBI_USER_NOT_FOUND_KEY = 'ESPRJ-1816 EBI_User_Not_found';

    const ID = 'id';

    const ERROR_PERSON_ALREADY_PROXIED = 'Nested Proxy is not Allowed';

    const ERROR_PERSON_ALREADY_PROXIED_KEY = 'ESPRJ-1817 Logged in Person Already in Proxy User.';

    const PROXY_ESPRJ_1817 = 'Proxy User -  Create Proxy View - CAMPUS_RES_ESPRJ_1817';

    const FIELD_PERSON_ID = 'personId';

    const FIELD_PERSON_ID_PROXIED_FOR = 'personIdProxiedFor';

    const PROXY_RECORD_NOT_FOUND = 'proxy user record not found';

    const PROXY_RECORD_NOT_FOUND_CODE = 'ESPRJ-1817 Delete Proxy User - Proxy Record not found';

    const USER_ALREADY_PROXIED = 'User is already in proxy, Nested Proxy is not Allowed';

    const USER_ALREADY_PROXIED_KEY = 'ESPRJ-1816 Nested proxy not allowed';

    const ERROR_PERSON_NOT_FOUND = 'Person not found';

    const ERROR_PERSON_NOT_FOUND_KEY = 'ESPRJ-1816 person_not_found';
}