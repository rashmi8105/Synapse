<?php
namespace Synapse\CoreBundle\Util\Constants;

class PdfDetailsConstant
{

    const DOCTRINE = 'doctrine';

    const EXTERNALID = 'externalId';

    const COLUMN_NAME = 'column_name';

    const FIELD_NAME = 'fieldName';

    const DATA_TYPE = 'data_type';

    const DECIMAL_POINTS = 'decimal_points';

    const DESCRIPTION = 'description';

    const STRING = 'string';

    const ITEM_DATA_TYPE = 'item_data_type';

    const MIN_RANGE = 'min_range';

    const MAX_RANGE = 'max_range';
    
    const MIN_VALUE = 'min_value';
    
    const MAX_VALUE = 'max_value';

    const NUMBER_TYPE = 'number_type';

    const LENGTH = 'length';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_CONFIG_REPO = 'SynapseCoreBundle:EbiConfig';
    
    const REQUIRED = 'required';

    const EXT_ID_MAX_LENGTH = 45;

    const GROUP_NAME = 'groupName';

    const GROUP_DESCRIPTION = "Semicolon-delimited list of group ID's.  Valid ID's are this top-level group or any of its subgroups.  Can also contain #clear.  See explanation at bottom of this file.";

    const COLUMNNAME = "columnName";

    const GROUP_NAME_MAX_LENGTH = 100;
}