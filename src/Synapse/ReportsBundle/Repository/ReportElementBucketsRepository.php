<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\ReportsBundle\Entity\ReportElementBuckets;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ReportElementBucketsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:ReportElementBuckets';

    public function remove(ReportElementBuckets $reportElementBuckets)
    {
        $em = $this->getEntityManager();
        $em->remove($reportElementBuckets);
    }
    
    public function deleteSectionElementsBucket($elemetIds)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('SynapseReportsBundle:ReportElementBuckets', 'bucket');
        $qb->set('bucket.deletedAt', 'CURRENT_TIMESTAMP()');
        $qb->where($qb->expr()
            ->in('bucket.elementId', ':elemetIds'));
        $qb->setParameters(array(
            'elemetIds' => $elemetIds
        ));
        
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
	
	public function createBucket(ReportElementBuckets $bucket)
    {
        $em = $this->getEntityManager();
        $em->persist($bucket);	
		$em->flush();		
        return $bucket;
    }
}
