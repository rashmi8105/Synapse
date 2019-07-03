<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Synapse\CoreBundle\Entity\OrgFeatures;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgFeaturesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = "SynapseCoreBundle:OrgFeatures";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGFEATURE_REPO = "SynapseCoreBundle:OrgFeatures";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGANIZATION_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const FEATUREMASTER_REPO = "SynapseCoreBundle:FeatureMaster";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const FEATURELANG_REPO = "SynapseCoreBundle:FeatureMasterLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGPERSONSTUDENT_REPO = "SynapseCoreBundle:OrgPersonStudent";

    public function createOrgFeature(OrgFeatures $orgFeatures)
    {
        $em = $this->getEntityManager();
        $em->persist($orgFeatures);
        return $orgFeatures;
    }

    public function getListFeatures($organizationId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('f.id', 'f.connected', 'l.featureName', 'm.id as feature_id', 'o.id as organization_id')
        ->from('SynapseCoreBundle:OrgFeatures', 'f')
        ->LEFTJoin('SynapseCoreBundle:Organization','o',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'f.organization = o.id')
                ->LEFTJoin('SynapseCoreBundle:FeatureMaster','m',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'f.feature = m.id')
                        ->LEFTJoin('SynapseCoreBundle:FeatureMasterLang','l',
                                \Doctrine\ORM\Query\Expr\Join::WITH,
                                'l.featureMaster = m.id')
                                ->where('f.organization = :organizationid')
                                ->setParameters(array(
                                        'organizationid' => $organizationId
                                ))
                                ->getQuery();
                                return $qb->getResult();
    }
    
    public function getListFeaturesByStudent($studentId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('f.id', 'f.connected', 'l.featureName', 'm.id as feature_id', 'o.id as organization_id')
        ->from(self::REPOSITORY_KEY, 'f')
        ->LEFTJoin(self::ORGANIZATION_REPO,'o',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            'f.organization = o.id')
            ->LEFTJoin(self::FEATUREMASTER_REPO,'m',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'f.feature = m.id')
                ->LEFTJoin(self::FEATURELANG_REPO,'l',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    'l.featureMaster = m.id')
                    ->Join(self::ORGPERSONSTUDENT_REPO,'ops',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'ops.organization = o.id')
                    ->where('ops.person = :studentid')
                    ->setParameters(array(
                        'studentid' => $studentId
                    ))
                    ->orderBy('f.organization, f.feature')
                    ->getQuery();
                    return $qb->getResult();
    }


    /**
     * Check if specified feature is enabled for the organization.
     *
     * @param int $organizationId
     * @param string $featureName
     * @return array
     * @throws SynapseDatabaseException
     */
    public function isFeatureEnabledForOrganization($organizationId, $featureName)
    {
        $parameters = [
            "organizationId" => $organizationId,
            "featureName" => $featureName
        ];

        $sql = "
            SELECT
                of.connected
            FROM
                org_features of
                    JOIN
                feature_master_lang fml ON fml.feature_master_id = of.feature_id
            WHERE
                of.organization_id = :organizationId
                AND fml.feature_name = :featureName
                AND fml.deleted_at IS NULL
                AND of.deleted_at IS NULL;
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();

        if ($records[0]['connected'] == "1") {
            return true;
        } else {
            return false;
        }
    }
}