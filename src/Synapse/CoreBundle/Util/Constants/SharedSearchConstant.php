<?php
namespace Synapse\CoreBundle\Util\Constants;

class SharedSearchConstant
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_SEARCH_REPO = "SynapseSearchBundle:OrgSearch";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_SEARCH_SHARED_REPO = "SynapseSearchBundle:OrgSearchShared";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const PERSON_REPO = "SynapseCoreBundle:Person";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATA_LIST_REPO = 'SynapseCoreBundle:MetadataListValues';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const PERSON_SERVICE = 'person_service';

    const ORG_NOT_FOUND = "Organization Not Found.";

    const ORG_NOT_FOUND_KEY = "organization_not_found.";

    const ORG_SEARCH_NOT_FOUND = "OrgSearch Not Found.";

    const ORG_SEARCH_NOT_FOUND_KEY = "orgSearch_not_found.";

    const SEARCH_NOT_FOUND = "Search Not Found.";

    const SEARCH_NOT_FOUND_KEY = "search_not_found.";

    const PERSON_NOT_FOUND = "Person Not Found.";

    const PERSON_NOT_FOUND_KEY = "person_not_found.";

    const FIELD_SOURCEID = 'sourceId';

    const FIELD_SRCSHARED_SEARCHID = 'src_shared_search_id';

    const FIELD_DESTID = 'destId';

    const FIELD_PERSON_SHAREDBY = 'personIdSharedby';

    const FIELD_PERSON_SHAREDWITH = 'personIdSharedwith';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_SEARCH_SHARED_WITH_REPO = "SynapseSearchBundle:OrgSearchSharedWith";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_SEARCH_SHARED_BY_REPO = "SynapseSearchBundle:OrgSearchSharedBy";

    const ORG_SEARCH = 'orgSearch';

    /**
     * @deprecated - Use Service::SERVICE_KEY in the future.
     */
    const SAVED_SEARCH_SERV = 'savedsearch_service';

    const DUP_SEARCH_NAME_ERROR = 'Search Name Already Exists.';

    const DUP_SEARCH_NAME_ERROR_KEY = 'search_name_already_exists';
    
    const SEARCH_ALREADY_SHARED = 'Search Already Shared With Person';
}