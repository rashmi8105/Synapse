<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use JMS\Serializer\Tests\Fixtures\Publisher;

/**
 * SurveyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SurveyRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:Survey';

    public function getSurveyDetails($surveyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('IDENTITY(sl.survey) as survey_id', 'sl.name as survey_name')
            ->from('SynapseCoreBundle:Survey', 's')
            ->LEFTJoin('SynapseCoreBundle:SurveyLang', 'sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.survey = s.id')
            ->where('sl.survey = :surveyId')
            ->setParameters(array(
            'surveyId' => $surveyId
        ))
            ->getQuery();
        
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * Written flat queries because group concat was not supporting in doctorine
     * 
     * @return multitype:
     */
    public function getSurveyQuestion()
    {
        $em = $this->getEntityManager();
        $sql = 'SELECT s.id as survey_id, group_concat(sq.id) as questions FROM synapse.survey as s
                Left join synapse.survey_questions as sq on (sq.survey_id = s.id) where sq.deleted_at is NULL group by s.id';
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }

    public function getSurveyFactor()
    {
        $em = $this->getEntityManager();
        $sql = 'SELECT s.id as survey_id,group_concat(f.id) as factor FROM survey as s
                Left join factor as f on (f.survey_id = s.id) where f.deleted_at is NULL group by s.id';
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }

    public function getOrganizationSurveys($orgId, $langId, $currentDate)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s.id as survey_id', 'sl.name as survey_name', 'wl.openDate as start_date', 'wl.closeDate as end_date');
        $qb->from('SynapseCoreBundle:Survey', 's');
        $qb->LEFTJoin('SynapseCoreBundle:SurveyLang', 'sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.survey = s.id');
        $qb->LEFTJoin('SynapseSurveyBundle:WessLink', 'wl', \Doctrine\ORM\Query\Expr\Join::WITH, 'wl.survey = s.id');
        $qb->LEFTJoin('SynapseAcademicBundle:OrgAcademicYear', 'oay', \Doctrine\ORM\Query\Expr\Join::WITH, 'oay.organization = wl.organization and oay.yearId = wl.year');
        $qb->where('wl.organization = :orgId');
        $qb->andWhere('oay.startDate <= :currDate');
        $qb->andWhere('oay.endDate >= :currDate');
        $qb->andWhere('sl.languageMaster = :langId');
        $qb->andWhere("wl.status IN ('launched', 'closed')");
        $qb->setParameters(array(
            'orgId' => $orgId,
            'currDate' => $currentDate,
            'langId' => $langId
        ));
        $qb->groupBy('wl.survey');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
}
