<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\OrgPersonPushNotificationChannel;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgPersonPushNotificationChannelRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPersonPushNotificationChannel';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgPersonPushNotificationChannel|null
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
     *
     * @return OrgPersonPushNotificationChannel[]
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
     *
     * @return OrgPersonPushNotificationChannel|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * Gets all the notification channels for the person
     *
     * @param $personId
     * @param $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getChannelNameForUser($personId, $organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'personId' => $personId
        ];

        $sql = "SELECT 
                    channel_name
                FROM
                    org_person_push_notification_channel
                WHERE
                    organization_id = :organizationId 
                    AND person_id = :personId
                    AND deleted_at IS NULL
                    ORDER BY created_at DESC, id DESC 
                    LIMIT 5";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        if (count($result) > 0) {
            $result = array_column($result, 'channel_name');
            return $result;
        } else {
            return [];
        }
    }
}
