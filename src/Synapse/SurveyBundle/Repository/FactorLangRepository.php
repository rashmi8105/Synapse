<?php
namespace Synapse\SurveyBundle\Repository;

use Synapse\SurveyBundle\Entity\Factor;
use Synapse\SurveyBundle\Entity\FactorLang;
use Synapse\CoreBundle\Repository\SynapseRepository;

/**
 * FactorLangRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FactorLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:FactorLang';

    public function checkFactorName($factorName, $id = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('factor.id', 'lang.name as factorName');
        $qb->from('SynapseSurveyBundle:FactorLang', 'lang');
        $qb->LEFTJoin('SynapseSurveyBundle:Factor', 'factor', \Doctrine\ORM\Query\Expr\Join::WITH, 'lang.factor = factor.id');
        $qb->where('lang.name = :factorName');
        if (isset($id)) {
            $qb->andWhere('factor.id ! =:id');
            $qb->setParameters(array(
                'id' => $id,
                'factorName' => $factorName
            ));
        } else {
            $qb->setParameters(array(
                'factorName' => $factorName
            ));
        }
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    

    /*
     *  Function to return list of factor id  for a given array of Survey Blocks
     *  
     *   @param array $surveyBlockArr
     *   @param array $surveyId
     *   @return array  
     */
    
    
    public function getFactorsBasedOnSurveyBlocks($surveyBlockArr,$surveyId){
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select(' DISTINCT IDENTITY(DQ.factor) as factor_id','lang.name as factorName','factor.sequence');
        $qb->from('SynapseCoreBundle:DatablockQuestions', 'DQ');
        $qb->INNERJoin('SynapseSurveyBundle:Factor', 'factor', \Doctrine\ORM\Query\Expr\Join::WITH, 'factor.id = DQ.factor');
        $qb->INNERJoin('SynapseSurveyBundle:FactorLang', 'lang', \Doctrine\ORM\Query\Expr\Join::WITH, 'lang.factor = DQ.factor');
        $qb->where('DQ.type = :type');
        $qb->andWhere('DQ.datablock IN ( :datablockArr)');
        $qb->andWhere('DQ.survey = :surveyId');
        $qb->setParameters(array(
            'type' => 'factor',
            'datablockArr' => $surveyBlockArr,
            'surveyId' => $surveyId
        ));
        
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
}