<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use JMS\DiExtraBundle\Annotation as DI;

class OrgGroupTreeRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgGroupTree';

    /**
     * Returns all children from a given parent group id
     *
     * @param array $parentGroupId
     * @param int $orgId
     * @return array
     */
    public function getEachGeneration($parentGroupId, $orgId){

        if(!is_array($parentGroupId)){
            $parentGroupId = explode(',', $parentGroupId);
        }

        $SQL = "
        SELECT
            og.id
        FROM
           org_group og
         INNER JOIN
           org_group_tree ogt ON og.id = ogt.descendant_group_id
        WHERE
            og.organization_id = :orgId
                AND ogt.ancestor_group_id IN (:parentGroupId)
                AND ogt.deleted_at IS NULL
                AND og.deleted_at  IS NULL;
        ";
        try{
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($SQL, ['parentGroupId'=>$parentGroupId, 'orgId'=>$orgId],['parentGroupId'=>Connection::PARAM_INT_ARRAY]);
        }catch(\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        $arrayOfIds = array();
        foreach($records as $record){
            $arrayOfIds[] = $record['id'];
        }
        return $records;
    }


    /**
     * returns true if the id is a descendant
     * of the ancestor id, else false
     *
     * @param int $descendantId
     * @param int $ancestorId
     * @return bool
     */
    public function isAncestor($descendantId, $ancestorId){
        $SQL = "
        SELECT
            1 as is_ancestor
        FROM
            org_group og
              INNER JOIN
            org_group_tree ogt ON og.id = ogt.descendant_group_id
        WHERE
            og.id = :descendantId
                AND ogt.ancestor_group_id = :ancestorId
                AND ogt.deleted_at IS NULL
                AND og.deleted_at  IS NULL;
        ";
        try{
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($SQL, ['descendantId'=>$descendantId, 'ancestorId'=>$ancestorId],[]);
        }catch(\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $records = $stmt->fetchAll();
        if(!empty($records[0])){
            return true;
        } else {
            return false;
        }
    }


    /**
     * This function will return the basis for the
     * Existing File Download. It will get all
     * student group combinations along with the
     * top level ancestor associated with student
     * group combination. Top Level includes immediate
     * subgroups of AllStudents.
     *
     * @param int $organizationId
     * @return Array
     */
    public function getAllStudentGroupCombinationsWithAncestorForAnOrganization($organizationId){
        $SQL = "
                SELECT
                    person.external_id,
                    person.firstname,
                    person.lastname,
                    person.username,
                    IF(og2.external_id IS NULL OR og2.external_id = '',
                        og2.group_name,
                        og2.external_id) AS header_name,
                    IF(og1.external_id IS NULL OR og1.external_id = '',
                        og1.group_name,
                        og1.external_id) AS person_in
                FROM
                    (SELECT
                               ancestor_group_id, descendant_group_id
                           FROM
                               org_group_tree
                           INNER JOIN org_group ON org_group.id = org_group_tree.ancestor_group_id
                           WHERE
                               org_group.organization_id = :organizationId
                                   AND org_group.deleted_at IS NULL
                                   AND ((org_group.parent_group_id IS NULL
                                   AND (org_group.external_id != 'ALLSTUDENTS'
                                   OR external_id IS NULL))
                                   OR org_group.parent_group_id = (SELECT
                                       id
                                   FROM
                                       org_group
                                   WHERE
                                       organization_id = :organizationId
                                           AND external_id = 'ALLSTUDENTS'
                                           AND deleted_at IS NULL))
                           GROUP BY ancestor_group_id , descendant_group_id) AS all_groups
                        INNER JOIN
                    org_group_students ON org_group_students.org_group_id = all_groups.descendant_group_id
                        INNER JOIN
                    person ON person.id = org_group_students.person_id
                        INNER JOIN
                    org_group og1 ON og1.id = descendant_group_id
                        INNER JOIN
                    org_group og2 ON og2.id = ancestor_group_id
                WHERE
                    org_group_students.organization_id = :organizationId
                        AND og1.deleted_at IS NULL
                        AND og2.deleted_at IS NULL
                        AND person.deleted_at IS NULL
                        AND org_group_students.deleted_at IS NULL
                ORDER BY external_id, og1.id, og2.id
        ";
        try{
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($SQL, ['organizationId'=>$organizationId],[]);
        }catch(\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $records = $stmt->fetchAll();
        return $records;
    }

    /**
     *  Returns all the descendant group ids with externalIds for the given group ID.
     * @param $groupId
     * @return array
     */
    public function findAllDescendantGroups($groupId)
    {

        $sql = "SELECT ogt.descendant_group_id as group_id, og.external_id
                    FROM org_group_tree AS ogt 
                    INNER JOIN org_group AS og ON ogt.descendant_group_id = og.id
                    WHERE ogt.ancestor_group_id = :groupId  AND ogt.deleted_at IS NULL AND og.deleted_at IS NULL";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, [
                'groupId' => $groupId
            ]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

}