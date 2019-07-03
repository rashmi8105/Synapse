<?php
namespace Synapse\CoreBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Entity\OrgGroupTree;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgGroupListener
{
    /**
     * Method used for populating the org_group_tree table after a record is persisted in Org_groups table
     *
     * @param OrgGroup $orgGroup
     * @param LifecycleEventArgs $args
     */
    public function postPersist(OrgGroup $orgGroup, LifecycleEventArgs $args)
    {

        $em = $args->getEntityManager();
        $parentGroupId = $orgGroup->getParentGroup();
        if (empty($parentGroupId)) {

            $orgGroupTree = new OrgGroupTree;
            $orgGroupTree->setAncestorGroupId($orgGroup);
            $orgGroupTree->setDescendantGroupId($orgGroup);
            $orgGroupTree->setPathLength(0);
            $em->persist($orgGroupTree);
            $em->flush();
        } else {

            $groupId = $orgGroup->getId();
            $parentGroupId = $orgGroup->getParentGroup()->getId();
            $sql = "INSERT INTO org_group_tree(ancestor_group_id, descendant_group_id, path_length, created_at , modified_at)
                    SELECT ogt.ancestor_group_id, :groupId, path_length + 1 ,  NOW() , NOW()
                    FROM org_group_tree AS ogt
                    WHERE ogt.descendant_group_id = :parentgroupId AND ogt.deleted_at IS NULL
                    UNION ALL
                    SELECT :groupId ,:groupId, 0 ,NOW(), NOW();";

            try {
                $args->getEntityManager()
                    ->getConnection()
                    ->executeQuery($sql, array(
                        'groupId' => $groupId,
                        'parentgroupId' => $parentGroupId
                    ));
            } catch (\Exception $e) {
                throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
            }
        }
    }

    /**
     *
     * This would remove the references of deleted group from the org_group_tree table  before it deleted from the org_group Table
     * @param OrgGroup $orgGroup
     * @param LifecycleEventArgs $args
     */
    public function preRemove(OrgGroup $orgGroup, LifecycleEventArgs $args)
    {

        $groupId = $orgGroup->getId();
        $sql = "UPDATE org_group_tree SET deleted_at = NOW() WHERE ancestor_group_id = :groupId OR descendant_group_id = :groupId";
        try {
            $args->getEntityManager()
                ->getConnection()
                ->executeQuery($sql, array(
                    'groupId' => $groupId
                ));
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }
}