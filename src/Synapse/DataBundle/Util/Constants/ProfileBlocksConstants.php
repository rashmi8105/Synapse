<?php
namespace Synapse\DataBundle\Util\Constants;

class ProfileBlocksConstants
{

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATABLOCKMASTER_ENT = 'SynapseCoreBundle:DatablockMaster';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATABLOCKMASTER_LANG_ENT = 'SynapseCoreBundle:DatablockMasterLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_METADATA_ENT = 'SynapseCoreBundle:EbiMetadata';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATABLOCK_METADATA_ENT = 'SynapseCoreBundle:DatablockMetadata';

    const EBI_LANG_ID = 1;

    const DATABLOCK_MASTER_NOT_FOUND = "Datablock Master Not Found";

    const DATABLOCK_MASTER_NOT_FOUND_CODE = "datablock_master_not_found";

    const DATABLOCK_DB_EXCEPTION = "Database Error";

    const DATABLOCK_DB_EXCEPTION_CODE = "db_error";

    const DATABLOCK = 'datablock';

    const ITEM_DATA_TYPE = 'item_data_type';

    const SEQUENCE_NO = 'sequence_no';

    const NUMBER_TYPE = 'number_type';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_METADATA_LIST_VALUES = 'SynapseCoreBundle:EbiMetadataListValues';

    const METALISTVAL_DUP_ERR = 'metalistvalue_duplicate_Error';

    const DATA_ATT_TO_PROFILE = 'Data attached to this profile.';
}
