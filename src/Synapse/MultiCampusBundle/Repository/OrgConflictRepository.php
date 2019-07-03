<?php
namespace Synapse\MultiCampusBundle\Repository;

use Synapse\MultiCampusBundle\Entity\OrgConflict;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Util\Constants\TierConstant;

class OrgConflictRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseMultiCampusBundle:OrgConflict';

    public function remove(OrgConflict $orgConflict)
    {
        $em = $this->getEntityManager();
        $em->remove($orgConflict);
    }

    public function create(OrgConflict $orgConflict)
    {
        $em = $this->getEntityManager();
        $em->persist($orgConflict);
        return $orgConflict;
    }

    public function getConflictsUserAccount()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $queryBuilder = $qb->select('count(conflicts.id) conflictCount, conflicts.createdAt , IDENTITY(conflicts.srcOrgId) as sourceOrganization, IDENTITY(conflicts.dstOrgId) as destinationOrganization')
            ->from(TierConstant::ORG_CONFLICT_ENTITY, TierConstant::CONFLICTS)
            ->where('conflicts.status != :status OR conflicts.status IS NULL')
            ->groupBy('conflicts.srcOrgId, conflicts.dstOrgId')
            ->setParameters(array(
            'status' => 'merged'
        ))
            ->getQuery();
        $resultSet = $queryBuilder->getResult();
        return $resultSet;
    }

    public function listStudentsConflicts($sourceId, $destinationId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select(TierConstant::CONFLICTS_P_C);
        $qb->from(TierConstant::ORG_CONFLICT_ENTITY, TierConstant::CONFLICTS);
        $qb->leftJoin('conflicts.studentId', 'p');
        $qb->leftJoin(TierConstant::CONTACTS, 'c');
        $qb->where('conflicts.studentId = p.id', TierConstant::CONFLICT_DEST_ORG_ID, TierConstant::CONFLICT_SOURCE_ORG_ID, 'conflicts.facultyId IS NULL');
        $qb->setParameters(array(
            TierConstant::SOURCE_ID => $sourceId,
            TierConstant::DESTINATION_ID => $destinationId
        ));
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

    public function listFacultyConflicts($sourceId, $destinationId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select(TierConstant::CONFLICTS_P_C);
        $qb->from(TierConstant::ORG_CONFLICT_ENTITY, TierConstant::CONFLICTS);
        $qb->leftJoin('conflicts.facultyId', 'p');
        $qb->leftJoin(TierConstant::CONTACTS, 'c');
        $qb->where('conflicts.facultyId = p.id', TierConstant::CONFLICT_DEST_ORG_ID, TierConstant::CONFLICT_SOURCE_ORG_ID, 'conflicts.studentId IS NULL');
        $qb->setParameters(array(
            TierConstant::SOURCE_ID => $sourceId,
            TierConstant::DESTINATION_ID => $destinationId
        ));
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

    public function listHybridConflicts($sourceId, $destinationId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select(TierConstant::CONFLICTS_P_C);
        $qb->from(TierConstant::ORG_CONFLICT_ENTITY, TierConstant::CONFLICTS);
        $qb->leftJoin('conflicts.facultyId', 'p');
        $qb->leftJoin(TierConstant::CONTACTS, 'c');
        $qb->where('conflicts.facultyId = p.id', 'conflicts.studentId = p.id', TierConstant::CONFLICT_SOURCE_ORG_ID, 'conflicts.srcOrgId = :sourceId');
        $qb->setParameters(array(
            TierConstant::SOURCE_ID => $sourceId,
            TierConstant::DESTINATION_ID => $destinationId
        ));
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

    public function isConflictExist($source, $facultyId, $studentId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(conflicts.id)');
        $qb->from(TierConstant::ORG_CONFLICT_ENTITY, TierConstant::CONFLICTS);
        $qb->where('conflicts.srcOrgId = :source');
        $parameter[] = array(
            'source' => $source
        );
        if (! empty($facultyId)) {
            $qb->andWhere('conflicts.facultyId = :facultyId');
            $parameter[] = array(
                'facultyId' => $facultyId
            );
        }
        if (! empty(($studentId))) {
            $qb->andWhere('conflicts.studentId = :studentId');
            $parameter[] = array(
                'studentId' => $studentId
            );
        }
        $parameterArray = call_user_func_array('array_merge', $parameter);
        $results = $qb->setParameters($parameterArray)
            ->getQuery()
            ->getSingleScalarResult();
        return $results;
    }

    public function isDualConflicts($person)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(conflicts.id)');
        $qb->from(TierConstant::ORG_CONFLICT_ENTITY, TierConstant::CONFLICTS);
        $qb->where('conflicts.studentId = :person');
        $qb->orWhere('conflicts.facultyId = :person');
        $qb->setParameters(array(
            // 'orgId' => $orgId,
            'person' => $person
        ));
        $results = $qb->getQuery()->getSingleScalarResult();
        return $results;
    }

    public function findAutoResolveConflicts($person)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('conflicts.id');
        $qb->from(TierConstant::ORG_CONFLICT_ENTITY, TierConstant::CONFLICTS);
        $qb->where('conflicts.studentId = :person');
        $qb->orWhere('conflicts.facultyId = :person');
        $qb->setParameters(array(
            'person' => $person
        ));
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }

    public function findAutoResolveConflictsByPerson($person, $sourceOrg, $destOrg)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('conflicts.id');
        $qb->from(TierConstant::ORG_CONFLICT_ENTITY, TierConstant::CONFLICTS);
        $qb->where('conflicts.studentId = :person');
        $qb->orWhere('conflicts.facultyId = :person');
        $qb->andWhere('conflicts.srcOrgId = :sourceOrg');
        $qb->andWhere('conflicts.dstOrgId = :destOrg');
        $qb->setParameters(array(
            'person' => $person,
            'sourceOrg' => $sourceOrg,
            'destOrg' => $destOrg
        ));
        $results = $qb->getQuery()->getArrayResult();
        return $results;
    }
}