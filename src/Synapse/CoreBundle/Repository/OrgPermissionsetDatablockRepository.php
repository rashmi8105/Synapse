<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\RestBundle\Entity\BlockDto;
use Synapse\CoreBundle\Util\Constants\OrgPermissionsetConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use JMS\DiExtraBundle\Annotation as DI;

class OrgPermissionsetDatablockRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPermissionsetDatablock';

    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    public function getOrgDatablockList($type, $orgpermissionsetid, $langid)
    {
        $em = $this->getEntityManager();
        
        if ($type == ('profile' || 'survey')) {
            $qb = $em->createQueryBuilder()
                ->select('m.id as datablock_id', 's.datablockDesc as datablock_name', 'opd.modifiedAt as modified_at')
                ->from('SynapseCoreBundle:DatablockMasterLang', 's')
                ->join('SynapseCoreBundle:DatablockMaster', 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 's.datablock = m.id')
                ->join(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO, 'opd', \Doctrine\ORM\Query\Expr\Join::WITH, 'opd.datablock = m.id')
                ->where('m.blockType = :key AND opd.orgPermissionset = :orgPermissionset AND s.lang =:lang')
                ->setParameters(array(
                'key' => $type,
                'orgPermissionset' => $orgpermissionsetid,
                'lang' => $langid
            ))
                ->getQuery();
            
            $resultSet = $qb->getResult();
            return $resultSet;
        }
    }

    public function getOrgDataBlocks($dataBlockType, $permissionsetId)
    {
        try {
            $sql = <<<SQL
SELECT db.id blockId, dbl.datablock_desc blockName, (opd.id IS NOT NULL) blockSelection, opd.modified_at lastUpdated
FROM datablock_master db
INNER JOIN datablock_master_lang dbl
    ON dbl.datablock_id=db.id
LEFT JOIN org_permissionset_datablock opd
    ON (opd.datablock_id=db.id AND opd.org_permissionset_id=:permissionsetId)
WHERE db.block_type=:dataBlockType
SQL;
            $stmt = $this->getEntityManager()
                ->getConnection()
                ->prepare($sql);
            $stmt->execute([
                'permissionsetId' => $permissionsetId,
                'dataBlockType' => $dataBlockType
            ]);
            
            $resultSet = array();
            $results = $stmt->fetchAll();
            foreach ($results as $row) {
                $blockDto = new BlockDto();
                $blockDto->fromArray($row);
                $resultSet[] = $blockDto;
            }
        } catch (\Exception $e) {         
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $resultSet;
    }

    public function getAllblockIdByPermissions($permissionId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('IDENTITY(opd.datablock) as block_id');
        $qb->from(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO, 'opd');
        $qb->LEFTjoin('SynapseCoreBundle:DatablockMaster', 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 'opd.datablock = m.id');
        $qb->where('opd.orgPermissionset IN (:orgpermission)');
        $qb->andWhere('m.blockType = :blockType');
        $qb->setParameters([
            'orgpermission' => $permissionId,
            'blockType' => "profile"
        ]);      
        $resultSet = $qb->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        return $resultSet;
    }

    public function getAllSurveyblockIdByPermissions($permissionId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('IDENTITY(opd.datablock) as block_id');
        $qb->from(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO, 'opd');
        $qb->LEFTjoin('SynapseCoreBundle:DatablockMaster', 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 'opd.datablock = m.id');
        $qb->where('opd.orgPermissionset IN (:orgpermission)');
        $qb->andWhere('m.blockType = :blockType');
        $qb->setParameters([
            'orgpermission' => $permissionId,
            'blockType' => "survey"
        ]);
		$qb->groupBy('opd.datablock');
        $resultSet = $qb->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        return $resultSet;
    }

    public function deleteSurveyBlock($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO, 'opd');
        $qb->set('opd.deletedAt', 'CURRENT_TIMESTAMP()');
        $qb->where($qb->expr()
            ->eq('opd.datablock', ':id'));
        $qb->setParameters(array(
            'id' => $id
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }


    /**
     * Returns a list of all datablock - group combinations (for the given type of datablock) for the given faculty member.
     * Note: This function intentionally does not take group hierarchy into account.
     *
     * @param int $personId
     * @param string $datablockType - 'profile' or 'survey'
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getDatablocksAndGroupsForPerson($personId, $datablockType)
    {
        $sql = 'select opd.datablock_id, ogf.org_group_id
                from org_permissionset_datablock opd
                inner join org_group_faculty ogf on ogf.org_permissionset_id = opd.org_permissionset_id
                where opd.deleted_at is null
                and ogf.deleted_at is null
                and opd.block_type = :datablockType
                and ogf.person_id = :personId
                order by opd.datablock_id, ogf.org_group_id;';

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute([':personId' => $personId, ':datablockType' => $datablockType]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $stmt->fetchAll();
    }


    /**
     * Returns a list of all datablock - course combinations (for the given type of datablock) for the given faculty member.
     *
     * @param int $personId
     * @param string $datablockType - 'profile' or 'survey'
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getDatablocksAndCoursesForPerson($personId, $datablockType)
    {
        $sql = 'select opd.datablock_id, ocf.org_courses_id
                from org_permissionset_datablock opd
                inner join org_course_faculty ocf on ocf.org_permissionset_id = opd.org_permissionset_id
                inner join org_courses oc on oc.id = ocf.org_courses_id
                inner join org_academic_terms oat on oat.id = oc.org_academic_terms_id and curdate() between oat.start_date and oat.end_date
                where opd.deleted_at is null
                and ocf.deleted_at is null
                and oc.deleted_at is null
                and oat.deleted_at is null
                and opd.block_type = :datablockType
                and ocf.person_id = :personId
                order by opd.datablock_id, ocf.org_courses_id;';

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute([':personId' => $personId, ':datablockType' => $datablockType]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $stmt->fetchAll();
    }


    /**
     * Returns an array of org_group_ids for all groups in which the given faculty member has a permission set
     * which includes the given datablock.
     * Note: This function intentionally does not take group hierarchy into account.
     *
     * @param int $personId
     * @param int $datablockId
     * @return array
     */
    public function getGroupsWithGivenDatablockForPerson($personId, $datablockId)
    {
        $sql = 'select ogf.org_group_id
                from org_permissionset_datablock opd
                inner join org_group_faculty ogf on ogf.org_permissionset_id = opd.org_permissionset_id
                where opd.deleted_at is null
                and ogf.deleted_at is null
                and opd.datablock_id = :datablockId
                and ogf.person_id = :personId;';

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute([':personId' => $personId, ':datablockId' => $datablockId]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        $results = array_map('current', $results);      // un-nest the array
        return $results;
    }


    /**
     * Returns an array of org_courses_ids for all current courses in which the given faculty member has a permission set
     * which includes the given datablock.
     *
     * @param int $personId
     * @param int $datablockId
     * @return array
     */
    public function getCoursesWithGivenDatablockForPerson($personId, $datablockId)
    {
        $sql = 'select ocf.org_courses_id
                from org_permissionset_datablock opd
                inner join org_course_faculty ocf on ocf.org_permissionset_id = opd.org_permissionset_id
                inner join org_courses oc on oc.id = ocf.org_courses_id
                inner join org_academic_terms oat on oat.id = oc.org_academic_terms_id and curdate() between oat.start_date and oat.end_date
                where opd.deleted_at is null
                and ocf.deleted_at is null
                and oc.deleted_at is null
                and oat.deleted_at is null
                and opd.datablock_id = :datablockId
                and ocf.person_id = :personId;';

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute([':personId' => $personId, ':datablockId' => $datablockId]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        $results = array_map('current', $results);      // un-nest the array
        return $results;
    }
}
