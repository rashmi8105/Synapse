<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\Query\Expr\Expr;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class DatablockMasterLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:DatablockMasterLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATA_BLOCK_MASTERLANG = 'SynapseCoreBundle:DatablockMasterLang';

    const PROFILE = 'profile';

    const SURVEY = "survey";

    const S_DATABLOCK_M_ID = 's.datablock = m.id';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const DATA_BLOCK_MASTER = 'SynapseCoreBundle:DatablockMaster';

    const M_BLOCKTYPE_KEY = 'm.blockType = :key';

    /**
     * get datablocks for profile or survey
     *
     * @param string $type - profile|survey
     * @param int $languageId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getDatablocks($type, $languageId = 1)
    {
        $parameters = [
            'blockType' => $type,
            'languageId' => $languageId
        ];

        $sql = "SELECT
                    dm.id AS datablock_id, dml.datablock_desc AS datablock_name, count(dmd.id) AS profile_item_count
                FROM
                    datablock_master dm
                        INNER JOIN
                    datablock_master_lang dml ON dml.datablock_id = dm.id
                        LEFT JOIN
                    datablock_metadata dmd ON dmd.datablock_id = dm.id
                        AND dmd.deleted_at IS NULL
                WHERE
                    dm.block_type = :blockType
                        AND dml.lang_id = :languageId
                        AND dm.deleted_at IS NULL
                        AND dml.deleted_at IS NULL
                GROUP BY dm.id
                ";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters);
            $resultSet = $stmt->fetchAll();
            return $resultSet;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    public function removeDatablockLang($datablockLang)
    {
        $em = $this->getEntityManager();
        $em->remove($datablockLang);
    }

    public function getDataBlockById($blockId, $langId)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('om.datablockDesc,dm.id as dmid')
            ->from(self::DATA_BLOCK_MASTERLANG, 'om')
            ->join('om.datablock', 'dm')
            ->where('om.datablock = :databloack AND om.lang = :lang')
            ->setParameters(array(
            'databloack' => $blockId,
            'lang' => $langId
        ))
            ->getQuery();
        return $qb->getArrayResult();
    }

    public function getSurveyBlocks()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('m.id', 's.datablockDesc as survey_block_name')
            ->from(self::DATA_BLOCK_MASTERLANG, 's')
            ->LEFTJoin(self::DATA_BLOCK_MASTER, 'm', \Doctrine\ORM\Query\Expr\Join::WITH, self::S_DATABLOCK_M_ID)
            ->where(self::M_BLOCKTYPE_KEY)
            ->setParameters(array(
            'key' => self::SURVEY
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function checkSurveyBlock($blockName, $id = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('m.id', 's.datablockDesc as survey_name');
        $qb->from(self::DATA_BLOCK_MASTERLANG, 's');
        $qb->LEFTJoin(self::DATA_BLOCK_MASTER, 'm', \Doctrine\ORM\Query\Expr\Join::WITH, self::S_DATABLOCK_M_ID);
        $qb->where(self::M_BLOCKTYPE_KEY);
        $qb->andWhere('s.datablockDesc = :blockName');
        
        if (isset($id)) {
            $qb->andWhere('m.id != :id');
            $paramArr = array(
                'id' => $id,
                'key' => self::SURVEY,
                'blockName' => $blockName
            );
        } else {
            $paramArr = array(
                'key' => self::SURVEY,
                'blockName' => $blockName
            );
        }
        $qb->setParameters($paramArr);
        
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }

    public function getProfileDataBlocks($lang, $blockId, $type = false)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('m.id as profile_block_id', 's.datablockDesc as profile_block_name', 's.modifiedAt as modified_at');
        $qb->from(self::DATA_BLOCK_MASTERLANG, 's');
        $qb->LEFTJoin(self::DATA_BLOCK_MASTER, 'm', \Doctrine\ORM\Query\Expr\Join::WITH, self::S_DATABLOCK_M_ID);
        $qb->where('m.blockType = :key AND s.lang =:lang');
        if (isset($type) && $type == 'search') {
            $qb->andWhere('s.id IN (:id)');
            $qb->setParameters(array(
                'key' => self::PROFILE,
                'lang' => $lang,
                'id' => $blockId
            ));
        } else {
            $qb->setParameters(array(
                'key' => self::PROFILE,
                'lang' => $lang
            ));
        }
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }

    public function getSurveyBlockDetails($id = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('m.id', 'q.type', 'IDENTITY(q.survey) as survey_id ', 'IDENTITY(q.surveyQuestions) as survey_questions_id ', 'ebl.questionRpt as ebi_questionText', 'ebil.questionRpt as ebi_questionTexts', 'indl.questionRpt as ind_questionText', 'fal.name as factor_questionText', 's.datablockDesc as survey_name', 'IDENTITY(q.ebiQuestion) as ebi_question_id', 'q.id as qid', 'IDENTITY(q.factor) as factor_id', 'IDENTITY(sq.ebiQuestion) as ebi_question_ids', 'IDENTITY(sq.indQuestion) as ind_question_id');
        $qb->from(self::DATA_BLOCK_MASTERLANG, 's');
        $qb->LEFTJoin(self::DATA_BLOCK_MASTER, 'm', \Doctrine\ORM\Query\Expr\Join::WITH, self::S_DATABLOCK_M_ID);
        $qb->LEFTJoin('SynapseCoreBundle:DatablockQuestions', 'q', \Doctrine\ORM\Query\Expr\Join::WITH, 's.datablock = q.datablock');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestion', 'eb', \Doctrine\ORM\Query\Expr\Join::WITH, 'q.ebiQuestion = eb.id');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'ebl', \Doctrine\ORM\Query\Expr\Join::WITH, 'ebl.ebiQuestion = eb.id');
        $qb->LEFTJoin('SynapseSurveyBundle:Factor', 'fa', \Doctrine\ORM\Query\Expr\Join::WITH, 'q.factor = fa.id');
        $qb->LEFTJoin('SynapseSurveyBundle:FactorLang', 'fal', \Doctrine\ORM\Query\Expr\Join::WITH, 'fal.factor = fa.id');
        $qb->LEFTJoin('SynapseSurveyBundle:SurveyQuestions', 'sq', \Doctrine\ORM\Query\Expr\Join::WITH, 'q.surveyQuestions = sq.id');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestion', 'ebi', \Doctrine\ORM\Query\Expr\Join::WITH, 'sq.ebiQuestion = ebi.id');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'ebil', \Doctrine\ORM\Query\Expr\Join::WITH, 'ebil.ebiQuestion = ebi.id');
        $qb->LEFTJoin('SynapseSurveyBundle:IndQuestion', 'ind', \Doctrine\ORM\Query\Expr\Join::WITH, 'sq.indQuestion = ind.id');
        $qb->LEFTJoin('SynapseSurveyBundle:IndQuestionsLang', 'indl', \Doctrine\ORM\Query\Expr\Join::WITH, 'indl.indQuestion = ind.id');
        $qb->where(self::M_BLOCKTYPE_KEY);
        if ($id != '') {
            $qb->andwhere('s.datablock = :id');
            $qb->setParameters(array(
                'key' => self::SURVEY,
                'id' => $id
            ));
        } else {
            $qb->setParameters(array(
                'key' => self::SURVEY
            ));
        }
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }

    public function deleteSurveyBlock($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update(self::DATA_BLOCK_MASTERLANG, 'm');
        $qb->set('m.deletedAt', 'CURRENT_TIMESTAMP()');
        $qb->where($qb->expr()
            ->eq('m.datablock', ':id'));
        $qb->setParameters(array(
            'id' => $id
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
}