<?php
namespace Synapse\SurveyBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;

class FactorQuestionsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:FactorQuestions';

    public function getFactorQuestions($factorId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(fq.ebiQuestion) as factor_ebi_que','IDENTITY(fq.surveyQuestions) as survey_que','IDENTITY(sq.ebiQuestion) as survey_ebi_que','IDENTITY(sq.indQuestion) as ind_que');
        $qb->from('SynapseSurveyBundle:FactorQuestions', 'fq');
        $qb->INNERJoin('SynapseSurveyBundle:SurveyQuestions', 'sq', \Doctrine\ORM\Query\Expr\Join::WITH, 'fq.surveyQuestions = sq.id');
        $qb->where('fq.factor = :id');
        $qb->setParameters(array(
            "id" => $factorId
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return  $resultSet;
        
    }
    
    public function getFactorQuestionsForDownload(){
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('IDENTITY(fq.ebiQuestion) as ebi_ques_id','IDENTITY(fq.factor) as factor_id');
        $qb->from('SynapseSurveyBundle:FactorQuestions', 'fq');
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return  $resultSet;
    }
    
    public function getAllFactorQuestions($factorId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('feql.questionRpt as fact_ebi_ques,            
            IDENTITY(fq.ebiQuestion) as factor_ebi_id');
        $qb->from('SynapseSurveyBundle:Factor', 'smq');
        $qb->LEFTJoin('SynapseSurveyBundle:FactorQuestions', 'fq', \Doctrine\ORM\Query\Expr\Join::WITH, 'smq.id = fq.factor');
        $qb->LEFTJoin('SynapseSurveyBundle:FactorLang', 'f', \Doctrine\ORM\Query\Expr\Join::WITH, 'smq.id = f.factor');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'eql', \Doctrine\ORM\Query\Expr\Join::WITH, 'fq.ebiQuestion = eql.ebiQuestion');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'feql', \Doctrine\ORM\Query\Expr\Join::WITH, 'fq.ebiQuestion = feql.ebiQuestion');
        $qb->LEFTJoin('SynapseSurveyBundle:SurveyQuestions', 'sq', \Doctrine\ORM\Query\Expr\Join::WITH, 'fq.surveyQuestions = sq.id');
        $qb->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'eql1', \Doctrine\ORM\Query\Expr\Join::WITH, 'sq.ebiQuestion = eql1.ebiQuestion');
        $qb->LEFTJoin('SynapseSurveyBundle:IndQuestionsLang', 'iql', \Doctrine\ORM\Query\Expr\Join::WITH, 'sq.indQuestion = iql.indQuestion');
        $qb->where('smq.id = :factorId');
        $qb->andWhere('fq.ebiQuestion IS NOT NULL');
        $qb->setParameters(array(
            "factorId" => $factorId
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    public function deleteFactorQuestions($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('SynapseSurveyBundle:FactorQuestions', 'fq');
        $qb->set('fq.deletedAt', 'CURRENT_TIMESTAMP()');
        $qb->where($qb->expr()
            ->eq('fq.factor', ':id'));
        $qb->setParameters(array(
            'id' => $id
        ));
        
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
	
	public function getDataBlockQuestionsBasedPermission($orgId, $personId, $isAggregate = false)
    {
        if($isAggregate){
            
            $aggregateCond = ' and ( ops.accesslevel_ind_agg = 1 or ops.accesslevel_agg = 1 ) ';
        }else {
            
            $aggregateCond = ' and ops.accesslevel_ind_agg = 1 ';
        }
        try {
            $em = $this->getEntityManager();
            $sql = "select distinct(dbq.ebi_question_id)
		      from 
			datablock_questions dbq
			inner join org_permissionset_datablock opd
				on (opd.datablock_id = dbq.datablock_id
					and opd.organization_id = $orgId
					and opd.block_type = 'survey')
			inner join (
					select
						ogf.org_permissionset_id as permissionset_id
					from
						org_group_faculty ogf 
					where ogf.person_id = $personId
						and ogf.organization_id = $orgId
						and ogf.deleted_at is null
				UNION ALL
					select
						ocf.org_permissionset_id as permissionset_id
					from org_course_faculty ocf
							inner join
						org_courses oc on oc.id = ocf.org_courses_id
							inner join
						org_academic_terms AS oat ON (oat.id = oc.org_academic_terms_id
							AND DATE(now()) between oat.start_date and oat.end_date)
					where ocf.organization_id = $orgId
						and oat.organization_id = $orgId
						and ocf.person_id = $personId
						and ocf.deleted_at is null
						and oat.deleted_at is null
					
				) as permissionset on ( permissionset.permissionset_id = opd.org_permissionset_id )
			inner join
			     org_permissionset ops on ( ops.id = permissionset.permissionset_id )
		where 			
			ops.organization_id = $orgId
			$aggregateCond
			and dbq.deleted_at is null
			and dbq.ebi_question_id is not null
            and opd.deleted_at is null";
        
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {
        throw new ValidationException([
            SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
            return $stmt->fetchAll();
    }
	
	public function getFactors($ebiQuestionId)
    {		
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('f.sequence as sequence,fl.name as factorName, f.id as factorId');		
        $qb->from('SynapseSurveyBundle:FactorQuestions', 'fq');        
		$qb->LEFTJoin('SynapseSurveyBundle:Factor', 'f', \Doctrine\ORM\Query\Expr\Join::WITH, 'f.id = fq.factor');
		$qb->LEFTJoin('SynapseSurveyBundle:FactorLang', 'fl', \Doctrine\ORM\Query\Expr\Join::WITH, 'fl.factor = f.id');
        $qb->where('fq.ebiQuestion IN (:id)');
        $qb->setParameters(array(
            "id" => $ebiQuestionId
        ));
		$qb->groupBy('fq.factor');
        $query = $qb->getQuery();		
        $resultSet = $query->getResult();			
        return  $resultSet;        
    }
	
	public function getFactorQuestionList($factor)
    {		
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(fq.ebiQuestion) as ebi_question_id');		
        $qb->from('SynapseSurveyBundle:FactorQuestions', 'fq');        		
        $qb->where('fq.factor IN (:factor)');
		$qb->andWhere('fq.ebiQuestion IS NOT NULL');
        $qb->setParameters(array(
            "factor" => $factor
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getResult();			
        return  $resultSet;        
    }
    
} 