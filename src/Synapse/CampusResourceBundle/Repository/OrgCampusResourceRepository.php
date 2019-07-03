<?php
namespace Synapse\CampusResourceBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CampusResourceBundle\Entity\OrgCampusResource;
use Synapse\CampusResourceBundle\Util\Constants\CampusResourceConstants;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

/**
 * Class OrgCampusResourceRepository
 * @package Synapse\CampusResourceBundle\Repository
 */
class OrgCampusResourceRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCampusResourceBundle:OrgCampusResource';

    /**
     *
     * @param OrgCampusResource $orgCampusResource            
     * @return OrgCampusResource
     */
    public function create(OrgCampusResource $orgCampusResource)
    {
        $em = $this->getEntityManager();
        $em->persist($orgCampusResource);
        return $orgCampusResource;
    }

    /**
     * @param int $id => Either the orgID or campusResourceID, see $singleDetail parameter documentation.
     * @param int $singleDetail => 1 to fetch a single campus resource by a campus resource ID,
     *                             Anything else to fetch all campus resources by organization ID
     * @return array
     */
    public function getCampusResources($id, $singleDetail)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('ocr.id as id', 'o.id as organization_id', 'ocr.name as resource_name', 'p.firstname as firstname', 'p.lastname as lastname', 'p.id as staff_id', 'ocr.phone as resource_phone_number', 'ocr.email as resource_email', 'ocr.location as resource_location', 'ocr.url as resource_url', 'ocr.description as resource_description', 'ocr.receiveReferals as receive_referals', 'ocr.visibleToStudent as visible_to_students');
        $qb->from(CampusResourceConstants::ORG_CAMPUS_RESOURCE_ENT, 'ocr');
        $qb->join(CampusResourceConstants::PERSON_REPO, 'p', \Doctrine\ORM\Query\Expr\Join::WITH, CampusResourceConstants::OCRPERSON_ID);
        $qb->join(CampusResourceConstants::ORGANIZATION_ENT, 'o', \Doctrine\ORM\Query\Expr\Join::WITH, CampusResourceConstants::OCRORG_ID);
        if ($singleDetail == 1) {
            $qb->where('ocr.id = :id AND ocr.deletedBy IS NULL');
            $qb->setParameters(array(
                'id' => $id
            ));
        } else {
            $qb->where('ocr.orgId = :orgId AND ocr.deletedBy IS NULL');
            $qb->setParameters(array(
                CampusResourceConstants::ORG_ID => $id
            ));
        }
        $qb->orderBy('ocr.name', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    /**
     * @param int $orgId
     * @return array
     */
    public function getSingleCampusVisibleResource($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('ocr.id as id', 'o.id as organization_id', 'ocr.name as resource_name', 'p.firstname as firstname', 'p.lastname as lastname', 'p.id as staff_id', 'ocr.phone as resource_phone_number', 'ocr.email as resource_email', 'ocr.location as resource_location', 'ocr.url as resource_url', 'ocr.description as resource_description', 'ocr.receiveReferals as receive_referals', 'ocr.visibleToStudent as visible_to_students');
        $qb->from(CampusResourceConstants::ORG_CAMPUS_RESOURCE_ENT, 'ocr');
        $qb->join(CampusResourceConstants::PERSON_REPO, 'p', \Doctrine\ORM\Query\Expr\Join::WITH, CampusResourceConstants::OCRPERSON_ID);
        $qb->join(CampusResourceConstants::ORGANIZATION_ENT, 'o', \Doctrine\ORM\Query\Expr\Join::WITH, CampusResourceConstants::OCRORG_ID);
        $qb->where(CampusResourceConstants::OCR_ORG_ID . ' AND ocr.visibleToStudent = 1 and ocr.deletedBy IS NULL');
        $qb->setParameters(array(
            CampusResourceConstants::ORG_ID => $orgId
        ));
        $qb->orderBy('ocr.name', 'ASC');
        $query = $qb->getQuery();
        $result = $query->getResult();
        
        return $result;
    }

    /**
     * Method used to fetch campus resources for referral creation by permission
     *
     * @param int $organizationId
     * @param array $studentIds
     * @return array
     */
    public function getCampusResourcesForReferralCreation($organizationId, $studentIds) {

        $parameters = [
            'studentIds' => $studentIds,
            'organizationId' => $organizationId,
            'studentCount' => count($studentIds)
        ];
        $parameterTypes = ['studentIds' => Connection::PARAM_INT_ARRAY];

            $sql = "SELECT
                        resource_name,
                        faculty_id,
                        firstname,
                        lastname,
                        COUNT(student_id) AS student_count
                    FROM
                        (SELECT DISTINCT
                            ocr.id AS resource_id,
                            ocr.name AS resource_name,
                            p.id AS faculty_id,
                            p.firstname,
                            p.lastname,
                            ofspm.student_id
                        FROM
                            org_campus_resource ocr
                                INNER JOIN
                            org_faculty_student_permission_map ofspm
                                    ON ofspm.faculty_id = ocr.person_id
                                    AND ofspm.org_id = ocr.org_id
                                INNER JOIN
                            org_permissionset_features opf
                                    ON opf.org_permissionset_id = ofspm.permissionset_id
                                    AND opf.organization_id = ofspm.org_id
                                INNER JOIN
                            feature_master_lang fml
                                    ON fml.feature_master_id = opf.feature_id
                                INNER JOIN
                            person p
                                    ON p.id = ofspm.faculty_id
                                    AND p.organization_id = ofspm.org_id
                        WHERE
                            ocr.receive_referals = 1
                            AND ocr.org_id = :organizationId
                            AND ofspm.student_id IN (:studentIds)
                            AND opf.receive_referral = 1
                            AND fml.feature_name = 'Referrals'
                            AND ocr.deleted_at IS NULL
                            AND opf.deleted_at IS NULL
                            AND fml.deleted_at IS NULL
                            AND p.deleted_at IS NULL) AS resources
                    GROUP BY resource_id
                    HAVING student_count = :studentCount";
            try {
                $em = $this->getEntityManager();
                $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

            } catch (\Exception $e) {
                throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
            }

            $resultSet = $stmt->fetchAll();
            return $resultSet;

    }
}