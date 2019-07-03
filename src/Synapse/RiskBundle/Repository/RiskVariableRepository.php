<?php
namespace Synapse\RiskBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class RiskVariableRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:RiskVariable';

    public function getAllRiskVariables($status)
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder();
        $qb->select('rv.id as rvid', 'rv.riskBVariable as risk_b_variable', 'rv.source', 'rv.isArchived as is_archived', 'IDENTITY(rv.ebiMetadata) as ebi_metadata_id', 'IDENTITY(rv.surveyQuestions) as survey_questions_id', 'IDENTITY(rv.orgMetadata) as org_metadata_id', 'IDENTITY(rv.orgQuestion) as org_question_id', 'IDENTITY(rv.factor) as factor_id', 'IDENTITY(rv.ebiQuestion) as ebi_question_id', 'IDENTITY(rv.survey) as survey_id', 'org.campusId as campus_id', 'IDENTITY(rmw.riskModel) as risk_model_id')
            ->from('SynapseRiskBundle:RiskVariable', 'rv')
            ->LEFTJoin('SynapseCoreBundle:Organization', 'org', \Doctrine\ORM\Query\Expr\Join::WITH, 'rv.org = org.id')
            ->LEFTJoin('SynapseRiskBundle:RiskModelWeights', 'rmw', \Doctrine\ORM\Query\Expr\Join::WITH, 'rmw.riskVariable = rv.id')
            ->where('rv.isArchived= :val')
            ->groupBy('rvid')
            ->setParameters(array(
            'val' => $status
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        
        return $resultSet;
    }

    public function getRiskCatValues($rid)
    {
        $em = $this->getEntityManager();
        $sql = "SELECT bucket_value,group_concat(option_value SEPARATOR ';' ) as option_value_str FROM risk_variable_category 
            where risk_variable_id=" . $rid . " group by bucket_value;";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }

    public function getStatusCount($val = 0)
    {
        $searchResult = array();
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('count(rv.id) as cnt')
            ->from('SynapseRiskBundle:RiskVariable', 'rv')
            ->where('rv.isArchived = :status')
            ->setParameters(array(
            'status' => $val
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function getRiskVarialbeList()
    {
        $em = $this->getEntityManager();
        $sql = "(SELECT om.meta_name,em.meta_key,o.campus_id,rv.*,rvc.bucket_value,group_concat(rvc.option_value SEPARATOR ';') as value1,'' as value2
                FROM risk_variable_category as rvc right join risk_variable rv  on rvc.risk_variable_id=rv.id left join organization o ON rv.org_id= o.id
                left join org_metadata om ON om.id=rv.org_metadata_id left join ebi_metadata em ON em.id=rv.ebi_metadata_id
                WHERE rvc.deleted_at IS NULL and rv.deleted_at IS NULL group by rvc.bucket_value,rv.id order by rv.id,rvc.bucket_value)
                UNION
                (SELECT om.meta_name,em.meta_key,o.campus_id,rv.*,rvr.bucket_value,rvr.min as value1,rvr.max as value2
                FROM risk_variable_range as rvr right join risk_variable rv  on rvr.risk_variable_id=rv.id left join organization o ON rv.org_id= o.id
                left join org_metadata om ON om.id=rv.org_metadata_id left join ebi_metadata em ON em.id=rv.ebi_metadata_id
                WHERE rv.deleted_at IS NULL order by rv.id,rvr.bucket_value) order by id,bucket_value";
        $resultSet = $em->getConnection()->fetchAll($sql);
        
        return $resultSet;
    }
}