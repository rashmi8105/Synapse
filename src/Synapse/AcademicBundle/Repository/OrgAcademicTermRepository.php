<?php
namespace Synapse\AcademicBundle\Repository;

use Synapse\AcademicBundle\Entity\OrgAcademicTerms;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgAcademicTermRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicBundle:OrgAcademicTerms';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return OrgAcademicTerms|null
     * @throws \Exception
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param \Exception $exception
     * @return OrgAcademicTerms|null
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $object = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($object, $exception);
    }

    /**
     *  Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param \Exception $exception
     * @param array|null $orderBy
     * @return OrgAcademicTerms|BaseEntity
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null){
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }

    public function deleteTerm(OrgAcademicTerms $academicTerms)
    {
        $this->getEntityManager()->remove($academicTerms);
    }

    //TODO::Clarify the scenario here for two overlapping terms
    public function getAcademicTermDates($currentDate, $orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('oat.id,oat.endDate, oat.name');
        $qb->from('SynapseAcademicBundle:OrgAcademicTerms', 'oat');
        $qb->andWhere('oat.startDate <= :currDate');
        $qb->andWhere('oat.endDate >= :currDate');
        $qb->andWhere('oat.organization = :organization');
        $qb->setParameters(array(
            'currDate' => $currentDate,
            'organization' => $orgId
        ));
        $qb->addOrderBy('oat.endDate', 'desc');
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        return $result;
    }


    /**
     * Returns a list (including metadata) of the terms for the given $orgAcademicYearId.
     *
     * @param int $orgAcademicYearId
     * @param int $orgId
     * @return array
     */
    public function getAcademicTermsForYear($orgAcademicYearId, $orgId)
    {
        $parameters = [
            'orgAcademicYearId' => $orgAcademicYearId,
            'orgId' => $orgId
        ];

        $sql = "SELECT
    				oat.name,
					oat.id AS org_academic_term_id,
					oat.start_date,
					oat.end_date,
    				oat.term_code,
    				CASE
    					WHEN
    						(oat.start_date <= DATE(now())
            				AND oat.end_date >= DATE(now()))
    					THEN
                          1
    					ELSE
        					0
    				END AS is_current_academic_term
				FROM
    				org_academic_terms oat
                        INNER JOIN
    				org_academic_year oay
    					    ON oat.org_academic_year_id = oay.id
    					    AND oat.organization_id = oay.organization_id
				WHERE
                    oat.org_academic_year_id = :orgAcademicYearId
                    AND oat.organization_id = :orgId
                    AND oat.deleted_at IS NULL
                    AND oay.deleted_at IS NULL
                ORDER BY oat.start_date ASC, oat.end_date DESC";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        
        $result = $stmt->fetchAll();
        return $result;
    }
}