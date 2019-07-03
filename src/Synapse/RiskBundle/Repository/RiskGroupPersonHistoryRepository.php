<?php
namespace Synapse\RiskBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\RiskBundle\Entity\RiskGroupPersonHistory;

class RiskGroupPersonHistoryRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseRiskBundle:RiskGroupPersonHistory';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return RiskGroupPersonHistory |null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return RiskGroupPersonHistory[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return RiskGroupPersonHistory|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


    public function getRiskGroupByOrg($orgId, $groupid)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('IDENTITY(rgph.riskGroup) as riskGroupId, p.externalId,p.id');
        $qb->from('SynapseRiskBundle:RiskGroupPersonHistory', 'rgph');
        $qb->LEFTJoin('SynapseCoreBundle:Person', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'rgph.person = p.id');
        $qb->where('p.organization = :orgId AND rgph.riskGroup= :group');
        $qb->setParameters([
            'orgId' => $orgId,
            'group' => $groupid
        ]);
        $qb->addOrderBy('rgph.riskGroup', 'desc');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
    
    public function getRiskGroupsByOrg($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
    
        $qb->select('IDENTITY(rgph.riskGroup) as riskGroupId');
        $qb->from('SynapseRiskBundle:RiskGroupPersonHistory', 'rgph');
        $qb->LEFTJoin('SynapseCoreBundle:Person', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'rgph.person = p.id');
        $qb->where('p.organization = :orgId');
        $qb->setParameters([
            'orgId' => $orgId,
          
            ]);
        $qb->distinct();
        $qb->addOrderBy('rgph.riskGroup', 'desc');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
    
    /**
     * check student already maaped with risk group
     * https://jira-mnv.atlassian.net/browse/ESPRJ-4752
     * 
     * The expected behavior is to see an error message if coordinator tries to associate a single student with multiple groups
     * 
     * @param Person|int $person
     * @return array
     */
    public function getStudentRiskGroup($person)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('IDENTITY(rgph.riskGroup) as riskGroupId, IDENTITY(rgph.person) as personId');
        $qb->from('SynapseRiskBundle:RiskGroupPersonHistory', 'rgph');
       
        $qb->where('rgph.person = :person');
        $qb->setParameters([
            'person' => $person
           
            ]);
        
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }


    /**
     * check student already mapped with risk group
     *
     * @param int $personId
     * @param string $currentDateTime
     * @return bool
     */
    public function isStudentInValidRiskGroup($personId, $currentDateTime)
    {
        $parameters = [
            'personId' => $personId,
            'currentDateTime' => $currentDateTime
        ];
        


        $sql = "SELECT 
                    1
                FROM
                    risk_group_person_history rgph
                    INNER JOIN org_person_student ops 
                        ON ops.person_id = rgph.person_id
                    INNER JOIN org_risk_group_model orgm 
                        ON orgm.risk_group_id = rgph.risk_group_id
                        AND orgm.org_id = ops.organization_id
                    INNER JOIN risk_model_master rmm
                        ON rmm.id = orgm.risk_model_id
                WHERE
                    rgph.person_id = :personId
                    AND ops.deleted_at IS NULL
                    AND orgm.deleted_at IS NULL
                    AND rmm.deleted_at IS NULL
                    AND rmm.calculation_end_date > :currentDateTime
                LIMIT 1";


        $result = $this->executeQueryFetchAll($sql, $parameters);
        if ($result) {
            return true;
        } else {
            return false;
        }

    }
}

