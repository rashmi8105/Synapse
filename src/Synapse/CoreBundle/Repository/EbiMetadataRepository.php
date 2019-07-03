<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\EbiMetadata;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use \Synapse\CoreBundle\Util\Constants\ProfileConstant;

class EbiMetadataRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:EbiMetadata';

    /**
     *
     * @param EbiMetadata $ebiMetadata            
     * @return EbiMetadata
     */
    public function create(EbiMetadata $ebiMetadata)
    {
        $em = $this->getEntityManager();
        $em->persist($ebiMetadata);
        return $ebiMetadata;
    }

    public function getEbiProfileCount()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('max(mdm.sequence) as max_sequence');
        $qb->from(ProfileConstant::EBI_METADATA_REPO, 'mdm');
        $qb->where('mdm.definitionType = :definitionType AND mdm.deletedBy IS NULL');
        $qb->setParameter("definitionType", "E");
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if ($result) {
            $result = $result[0]['max_sequence'];
        }
        return (int) $result;
    }

    public function getProfileWithProfileBlock($profileId)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('em.id as profile_id', 'eml.metaName as profile_name', 'dms.id as profile_block_id', 'dmsl.datablockDesc as profile_block_name')
            ->from(ProfileConstant::EBI_METADATA_REPO, 'em')
            ->join('SynapseCoreBundle:EbiMetadataLang', 'eml', \Doctrine\ORM\Query\Expr\Join::WITH, 'eml.ebiMetadata = em.id')
            ->join('SynapseCoreBundle:DatablockMetadata', 'dm', \Doctrine\ORM\Query\Expr\Join::WITH, 'dm.ebiMetadata = em.id')
            ->join('SynapseCoreBundle:DatablockMaster', 'dms', \Doctrine\ORM\Query\Expr\Join::WITH, 'dms.id = dm.datablock')
            ->join('SynapseCoreBundle:DatablockMasterLang', 'dmsl', \Doctrine\ORM\Query\Expr\Join::WITH, 'dmsl.datablock = dms.id')
            ->where('em.id = :profile_id')
            ->setParameters(array(
            'profile_id' => $profileId
        ))
            ->getQuery();
        return $qb->getArrayResult();
    }

    /**
     *
     * @param EbiMetadata $ebiMetadata            
     */
    public function remove(EbiMetadata $ebiMetadata)
    {
        $em = $this->getEntityManager();
        $em->remove($ebiMetadata);
    }

    public function merge(EbiMetadata $ebiMetadata)
    {
        $em = $this->getEntityManager();
        $em->merge($ebiMetadata);
    }

    public function getEbiMetadataReferance($id)
    {
        $em = $this->getEntityManager();
        return $em->getReference(ProfileConstant::EBI_METADATA_REPO, $id);
    }

    public function getAllProfileIds()
    {
        $em = $this->getEntityManager();
        $sql = "SELECT meta_key,id FROM ebi_metadata where deleted_at is NULL";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }
	
    public function IsEbiProfileExists($key)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('mdm.id');
        $qb->from(ProfileConstant::EBI_METADATA_REPO, 'mdm');
        $qb->where('mdm.key = :metaKey');
        $qb->setParameters(array(
             
            "metaKey" => $key
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if(count($result) > 0)
        {
            return false;
        }else{
            return true;
        }
    }


    /**
     * Returns data about all profile items (ebi metadata) in the given datablock.
     *
     * @param int $datablock
     * @return array
     */
    public function getEbiMetadataByDatablock($datablock)
    {
        $sql = "select dm.ebi_metadata_id, em.meta_key, eml.meta_description, em.scope, em.metadata_type, dml.datablock_desc
                from ebi_metadata em
                inner join ebi_metadata_lang eml on eml.ebi_metadata_id = em.id
                inner join datablock_metadata dm on dm.ebi_metadata_id = em.id
                inner join datablock_master_lang dml on dml.datablock_id = dm.datablock_id
                where em.deleted_at is null
                and eml.deleted_at is null
                and dm.deleted_at is null
                and dml.deleted_at is null
                and (em.status is null or em.status <> 'archived')
                and dm.datablock_id = :datablock;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute([':datablock' => $datablock]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $stmt->fetchAll();
    }
    
}