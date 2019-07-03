<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\OrgMetadataListValues;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgMetadataListValuesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgMetadataListValues';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return OrgMetadataListValues|null
     * @throws \Exception
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    public function remove($orgMetadataList)
    {
        $em = $this->getEntityManager();
        $em->remove($orgMetadataList);
    }

    public function getListValues($metadataid, $listvalue)
    {
        $entity_manager = $this->getEntityManager();
        
        $query_bulder = $entity_manager->createQueryBuilder();
        $query_bulder->select('metavals.listName as listName')
            ->from('SynapseCoreBundle:OrgMetadataListValues', 'metavals')
            ->
        where('metavals.orgMetadata= :key AND metavals.listValue = :listValue')
            ->setParameters(array(
            'key' => $metadataid,
            'listValue' => $listvalue
        ));
        $query = $query_bulder->getQuery();
        $result = $query->getArrayResult();
        
        
        return $result;
    }


    /**
     * Returns a lookup table of options for the given (categorical) ISP (org metadata),
     * where the key is the numeric list_value and the value is the corresponding list_name.
     *
     * @param int $orgMetadataId
     * @return array
     */
    public function getListValuesAndNamesForOrgMetadata($orgMetadataId)
    {
        $sql = 'select list_name, list_value
                from org_metadata_list_values
                where org_metadata_id = :orgMetadataId
                and deleted_at is null
                order by list_value + 0;';      // order them as numbers, not as strings

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute([':orgMetadataId' => $orgMetadataId]);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        $lookupTable = [];
        foreach ($records as $record) {
            $lookupTable[$record['list_value']] = $record['list_name'];
        }

        return $lookupTable;
    }
}