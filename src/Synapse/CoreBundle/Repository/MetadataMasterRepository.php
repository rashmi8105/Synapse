<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\MetadataMaster;

class MetadataMasterRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:MetadataMaster';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $metadataMasterEntity = 'SynapseCoreBundle:MetadataMaster';
    
    /**
     *
     * @param MetadataMaster $metadataMaster
     * @return MetadataMaster
     */
    public function create(MetadataMaster $metadataMaster)
    {
        $em = $this->getEntityManager();
        $em->persist($metadataMaster);
        return $metadataMaster;
    }


    public function getEbiProfileCount()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('max(mdm.sequence) as max_sequence');
        $qb->from($this->metadataMasterEntity, 'mdm');
        $qb->where('mdm.definitionType = :definitionType AND mdm.deletedBy IS NULL');
        $qb->setParameter("definitionType", "E");
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if ($result) {
            $result = $result[0]['max_sequence'];
        }
        return (int) $result;
    }

    public function getOrgProfileCount($organization)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('max(mdm.sequence) as max_sequence');
        $qb->from($this->metadataMasterEntity, 'mdm');
        $qb->where('mdm.definitionType = :definitionType AND mdm.organization =:organization AND mdm.deletedBy IS NULL');
        $qb->setParameters(array(
                "definitionType" => "O",
                "organization" => $organization
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        if ($result) {
            $result = $result[0]['max_sequence'];
        }
        return (int) $result;
    }

    public function getMetadataByKey($key, $organization)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('mdm')
           ->from($this->metadataMasterEntity, 'mdm')
           ->where('mdm.key = :key AND mdm.definitionType = :type')
           ->orWhere('mdm.key = :key AND mdm.organization = :organization')
           ->setParameters([
                'key' => $key,
                'organization' => $organization,
                'type' => 'E'
           ]);
        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     *
     * @param MetadataMaster $metadataMaster
     */
    public function remove(MetadataMaster $metadataMaster)
    {
        $em = $this->getEntityManager();
        $em->remove($metadataMaster);

    }
    public function merge(MetadataMaster $metadataMaster)
    {
        $em = $this->getEntityManager();
        $em->merge($metadataMaster);

    }

}