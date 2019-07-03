<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Synapse\CoreBundle\Entity\EbiQuestion;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class EbiQuestionRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:EbiQuestion';

    /**
     *
     * @param EbiQuestion $ebiQuestion            
     * @return EbiQuestion
     */
    public function create(EbiQuestion $ebiQuestion)
    {
        $em = $this->getEntityManager();
        $em->persist($ebiQuestion);
        return $ebiQuestion;
    }

    /**
     *
     * @param EbiQuestion $ebiQuestion            
     */
    public function remove(EbiQuestion $ebiQuestion)
    {
        $em = $this->getEntityManager();
        $em->remove($ebiQuestion);
    }

    /**
     *
     * @param EbiQuestion $ebiQuestion            
     */
    public function merge(EbiQuestion $ebiQuestion)
    {
        $em = $this->getEntityManager();
        $em->merge($ebiQuestion);
    }

    public function getEbiQuestions()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('EQ.id as id', 'EQL.questionText as text')
            ->from('SynapseCoreBundle:EbiQuestion', 'EQ')
            ->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'EQL', \Doctrine\ORM\Query\Expr\Join::WITH, 'EQ.id = EQL.ebiQuestion')
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }
    
    public function getAllEbiQuestions(){
        $em = $this->getEntityManager();
        
        $sql = "SELECT group_concat(id) as ebi_ids FROM ebi_question where deleted_at is NULL";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }


    /**
     * Returns a combined list of factor_ids and ebi_question_ids in the system,
     * to be used in a template for uploading survey blocks.
     *
     * @return array
     */
    public function getEbiQuestionsAndFactors()
    {
        $sql = "SELECT
                    '' AS LongitudinalId,
                    survey_id as SurvID,
                    id as FactorId
                FROM factor
                WHERE deleted_at IS NULL
            UNION
                SELECT
                    id as LongitudinalId ,
                    '' as SurvID,
                    '' as FactorId
                FROM ebi_question
                WHERE deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();

        return $result;
    }
}