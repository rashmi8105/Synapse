<?php
namespace Synapse\CoreBundle\Util\Constants;

class SurveyBlockConstant
{

    const SURVEY_BLOCK_NAME_VALIDATION = "Survey Block name should not be greater than 50";

    const SURVEY_BLOCK_NAME_VALIDATION_KEY = "Survey_Block_name_should_not_be_greater_than_50";

    const SURVEY_BLOCK_NAME_EXISTS = "Survey Block already exists";

    const SURVEY_BLOCK_NAME_EXISTS_KEY = "Survey_Block_already_exists";

    const ERROR_INVALID_DATABLOCK_ID = "Invalid data block id";

    const ERROR_INVALID_DATABLOCK_ID_KEY = "Invalid_data_block_id";

    const EBI_QN = "ebi_question_id";

    const EBI_QNS = "ebi_question_ids";

    const FACTOR_ID = "factor_id";

    const IND_QN = "ind_question_id";

    const SURVEY_NAME = "survey_name";

    const ID = "id";

    const TYPE = "type";

    const QN_COUNT = "question_cnt";

    const FACTOR_COUNT = "factor_cnt";

    const ERROR_INVALID_DATABLOCK_OR_QID = "Invalid data block id or question id";

    const ERROR_INVALID_DATABLOCK_OR_QID_KEY = "Invalid_data_block_id_or_question_id";

    const ERROR_INVALID_DATABLOCK_OR_FID = "Invalid data block id or factor id";

    const ERROR_INVALID_DATABLOCK_OR_FID_KEY = "Invalid_data_block_id_or_factor_id";

    const ERROR_INVALID_LANG_ID = "Language not found";

    const ERROR_INVALID_LANG_ID_KEY = "Language_not_found";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATA_BLOCK_MASTER_LANG_REPO = 'SynapseCoreBundle:DatablockMasterLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATA_BLOCK_MASTER_REPO = 'SynapseCoreBundle:DatablockMaster';

    const SURVEY_ID = 'survey_id';

    const ERROR_INVALID_SURVEY_BLOCK = "Survey block not found";

    const ERROR_INVALID_SURVEY_BLOCK_KEY = "Survey_block_not_found";

    const QID = 'qid';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATA_BLOCK_QUESTIONS_REPO = 'SynapseCoreBundle:DatablockQuestions';
}