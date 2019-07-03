<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\RestBundle\Entity\BlockDto;
use Synapse\CoreBundle\Util\Constants\OrgPermissionsetConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use JMS\DiExtraBundle\Annotation as DI;

class OrgReportPermissionsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgReportPermissions';

    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    public function getReportsPermissionSet($permissionsetId, $organizationId)
    {
        try {
            $em = $this->getEntityManager();
            $sql = <<<SQL
            SELECT 
    rep.id,
    rep.name,
    rep.short_code shortCode,    
    (orp.id IS NOT NULL) selection,
    is_coordinator_report
FROM
    reports rep
        LEFT JOIN
    org_report_permissions orp ON orp.report_id = rep.id
        AND orp.org_permissionset_id =:permissionsetId AND orp.organization_id=:organizationId AND orp.deleted_at IS NULL
WHERE   
	rep.is_active=:isActive AND rep.deleted_at IS NULL order by rep.name ASC
SQL;
            $stmt = $this->getEntityManager()
                ->getConnection()
                ->prepare($sql);
            $stmt->execute([
                'permissionsetId' => $permissionsetId,
                'organizationId' => $organizationId,
                'isActive' => 'y'
            ]);
            
            $data = $stmt->fetchAll();
        } catch (\Exception $e) {         
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $data;
    }

   

   

   
}
