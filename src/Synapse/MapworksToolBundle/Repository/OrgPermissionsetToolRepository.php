<?php
namespace Synapse\MapworksToolBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgPermissionsetToolRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseMapworksToolBundle:OrgPermissionsetTool';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants or NULL if no specific lock mode should be used during the search.
     * @param int|null $lockVersion The lock version.
     * @return OrgPermissionsetTool|null
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
     * @return OrgPermissionsetTool[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return OrgPermissionsetTool|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * This will fetch the tools for corresponding PermissionId
     *
     * @param int $permissionsetId
     * @param int $organizationId
     * @return array $results
     */
    public function getToolsWithPermissionsetSelection($permissionsetId, $organizationId)
    {
        $parameters = array(
            'permissionsetId' => $permissionsetId,
            'organizationId' => $organizationId
        );
        $sql = "SELECT 
                    mt.id AS tool_id,
                    mt.tool_name,
                    mt.short_code,
                    (opt.id IS NOT NULL) AS selection,
                    mt.can_access_with_aggregate_only_permission
                FROM
                    mapworks_tool mt
                        LEFT JOIN
                    org_permissionset_tool opt 
                            ON opt.mapworks_tool_id = mt.id
                            AND opt.org_permissionset_id =:permissionsetId
                            AND opt.organization_id=:organizationId
                            AND opt.deleted_at IS NULL
                WHERE   
                    mt.deleted_at IS NULL 
                ORDER BY 
                    mt.tool_name ASC";
        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }
}
