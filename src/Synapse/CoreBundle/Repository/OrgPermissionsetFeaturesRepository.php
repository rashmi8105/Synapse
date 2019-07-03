<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgPermissionsetFeaturesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPermissionsetFeatures';

    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
        return;
    }

    public function listOrPermissionsetFeaturesAll($orgpermission, $langid)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('m.id as feature_id', 's.featureName as feature_name', 'opf')
            ->from('SynapseCoreBundle:FeatureMasterLang', 's')
            ->join('SynapseCoreBundle:FeatureMaster', 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 's.featureMaster = m.id')
            ->where('s.lang = :langid')
            ->join('SynapseCoreBundle:OrgPermissionsetFeatures', 'opf', \Doctrine\ORM\Query\Expr\Join::WITH, 'opf.feature = m.id')
            ->where('s.lang = :langid AND opf.orgPermissionset = :orgpermission ')
            ->setParameters(array(
            'langid' => $langid,
            'orgpermission' => $orgpermission
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        
        return $resultSet;
    }

    /**
     *
     * @param
     *            $permissionsetId
     * @param int $langId            
     * @param int[] $featureIds            
     * @return array
     */
    public function getPermissionSetsByFeatures($permissionsetId, $langId = null, array $featureIds = null)
    {
        $em = $this->getEntityManager();
        
        $langWhere = '';
        if (is_int($langId)) {
            $langWhere = 'AND s.lang = ' . $langId;
        }
        
        $featuresWhere = '';
        if ($featureIds) {
            $featuresWhere = 'AND opf.feature IN(:featureIds)';
        }
        
        $dql = <<<DQL
SELECT opf, partial f.{id}, s.featureName
FROM SynapseCoreBundle:OrgPermissionsetFeatures opf
INNER JOIN opf.feature f WITH opf.feature=f
INNER JOIN SynapseCoreBundle:FeatureMasterLang s WITH s.featureMaster=f
WHERE opf.orgPermissionset = :permissionsetId
$langWhere $featuresWhere
AND opf.deletedAt IS NULL
AND f.deletedAt Is NULL
DQL;
        $query = $em->createQuery($dql)->setParameter('permissionsetId', $permissionsetId);
        
        if ($featureIds) {
            $query->setParameter('featureIds', $featureIds);
        }
        
        $data = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        
        $resultSet = [];
        foreach ($data as $idx => $row) {
            $resultSet[$idx] = $row[0];
            $resultSet[$idx]['feature']['name'] = $data[$idx]['featureName'];
        }
        
        return $resultSet;
    }

    /**
     *
     * @param
     *            $permissionsetId
     * @param int $langId            
     * @param int[] $featureIds            
     * @return array
     */
    public function getFeaturesByPermissionSet($permissionsetId, $langId = null, array $featureIds = null)
    {
        $em = $this->getEntityManager();
        
        $langWhere = '';
        if (is_numeric($langId)) {
            $langWhere = 'AND s.lang = ' . $langId;
        }
        
        $featuresWhere = '';
        if ($featureIds) {
            $featuresWhere = 'AND opf.feature IN(:featureIds)';
        }
        
        $dql = <<<DQL
SELECT opf, partial f.{id}, s.featureName
FROM SynapseCoreBundle:OrgPermissionsetFeatures opf
INNER JOIN opf.feature f WITH opf.feature=f
INNER JOIN SynapseCoreBundle:FeatureMasterLang s WITH s.featureMaster=f
INNER JOIN SynapseCoreBundle:OrgPermissionset op WITH op=opf.orgPermissionset
WHERE op = :permissionsetId
$langWhere $featuresWhere
AND op.deletedAt IS NULL
AND opf.deletedAt IS NULL
AND f.deletedAt Is NULL
DQL;
        $query = $em->createQuery($dql)->setParameter('permissionsetId', $permissionsetId);
        
        if ($featureIds) {
            $query->setParameter('featureIds', $featureIds);
        }
        
        $data = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        
        $resultSet = [];
        foreach ($data as $idx => $row) {
            $resultSet[$idx] = $row[0];
            $resultSet[$idx]['feature']['name'] = $data[$idx]['featureName'];
        }
        
        return $resultSet;
    }


    /**
     * This query will grab the Union of the features for
     * an array of permissionsets in a given organization
     *
     * @param array $permissionSetArray
     * @param int $organizationId
     * @param int $specificFeatureId = should be an integer between the numbers
     *                                    1 and 7, the number represents the feature below:
     *                                  1 => Referrals
     *                                  2 => Notes
     *                                  3 => Log Contacts
     *                                  4 => Booking
     *                                  5 => Student Referrals
     *                                  6 => Reason Routing
     *                                  7 => Email
     * example: array(1, 2, 3); array(7)
     * @return array
     */
    public function getFeaturePermissions($permissionSetArray, $organizationId, $specificFeatureId)
    {
        $specificFeatureSQL = "";

        $parameters = [
            'organizationId' => $organizationId,
            'permissionSetArray' => $permissionSetArray
        ];

        $parameterTypes = ['permissionSetArray' => Connection::PARAM_INT_ARRAY];

        if (!empty($specificFeatureId)) {
            $specificFeatureSQL = "AND feature_id = :specificFeatureId";
            $parameters['specificFeatureId'] = $specificFeatureId;
        }

        $sql = "
                SELECT
                    feature_id,
                    MAX(private_create) AS private_create,
                    MAX(team_create) AS teams_create,
                    MAX(public_create) AS public_create,
                    MAX(public_view) AS public_view,
                    MAX(team_view) AS teams_view,
                    MAX(reason_referral_private_create) AS reason_referrals_private_create,
                    MAX(reason_referral_team_create) AS reason_referrals_teams_create,
                    MAX(reason_referral_public_create) AS reason_referrals_public_create,
                    MAX(reason_referral_team_view) AS reason_referrals_teams_view,
                    MAX(reason_referral_public_view) AS reason_referrals_public_view
                FROM
                    org_permissionset_features
                WHERE
                    org_permissionset_id IN (:permissionSetArray)
                        AND organization_id = :organizationId
                        AND deleted_at IS NULL
                        $specificFeatureSQL
                GROUP BY feature_id
                ORDER BY feature_id;
        ";


        try {
            $em = $this->getEntityManager();

            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $resultSet = $stmt->fetchAll();
        return $resultSet[0];

    }

    /**
     * Return a list of student ids for which the faculty has public_create permissions
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param string $featureName
     * @param array $requestedStudentIds
     * @param int $orgAcademicYearId
     * @return array $records
     * @throws SynapseDatabaseException
     */
    public function getStudentsForFeature($organizationId, $facultyId, $featureName, $requestedStudentIds, $orgAcademicYearId)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'facultyId' => $facultyId,
            'featureName' => $featureName,
            'requestedStudentIds' => $requestedStudentIds,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $parameterTypes = [
            'requestedStudentIds' => Connection::PARAM_INT_ARRAY
        ];

        $accessCheck = 'AND opf.public_create = 1';

        if ($featureName == "Referrals") {
            $accessCheck = 'AND (opf.public_create = 1 OR opf.reason_referral_public_create = 1)';
        }

        $sql = "SELECT DISTINCT
                    ofspm.student_id,
                    p.firstname,
                    p.lastname
                FROM
                    org_faculty_student_permission_map ofspm
                    INNER JOIN
                    person p
                        ON p.id = ofspm.student_id
                    INNER JOIN
                    org_permissionset op
                        ON ofspm.permissionset_id = op.id
                    INNER JOIN
                    org_permissionset_features opf
                        ON opf.org_permissionset_id = op.id
                    INNER JOIN
                    feature_master_lang fml ON fml.feature_master_id = opf.feature_id
                    INNER JOIN
                     org_person_student_year opsy
                        ON opsy.person_id =  ofspm.student_id
                        AND opsy.organization_id = ofspm.org_id
                WHERE
                    ofspm.faculty_id = :facultyId
                    AND ofspm.student_id IN (:requestedStudentIds)
                    AND p.organization_id = :organizationId
                    AND fml.feature_name = :featureName
                    $accessCheck
                    AND op.accesslevel_ind_agg = 1
                    AND p.deleted_at IS NULL
                    AND op.deleted_at IS NULL
                    AND opf.deleted_at IS NULL
                    AND fml.deleted_at IS NULL
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.deleted_at IS NULL";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        return $records;
    }

}
