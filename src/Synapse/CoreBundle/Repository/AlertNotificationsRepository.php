<?php
namespace Synapse\CoreBundle\Repository;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\AlertNotifications;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;

class AlertNotificationsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:AlertNotifications';

    /**
     * Finds entities by a set of criteria.
     * Override added to inform PhpStorm about the return type.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return AlertNotifications[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param \Exception $exception
     * @param array|null $orderBy
     * @return AlertNotifications|object
     */
    public function findOneBy(array $criteria, $exception = null,  array $orderBy = null)
    {
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }


    public function getNotifications()
    {}

    public function create(AlertNotifications $alertNotification)
    {
        $em = $this->getEntityManager();
        $em->persist($alertNotification);
        $id = $alertNotification->getId();
    }

    public function remove($alertNotification)
    {
        $em = $this->getEntityManager();
        $em->remove($alertNotification);
    }

    /**
     * @param $alertids
     *
     * @deprecated API looks to no longer be used.
     */
    public function removeSelected($alertids)
    {
        try {
            $em = $this->getEntityManager();
            $q = $em->createQuery('update Synapse\CoreBundle\Entity\AlertNotifications alert set alert.isRead = 1 where alert.id in(' . $alertids . ')');
            $q->execute();
        } catch (\Exception $e) {         
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
    }

    /**
     * Gets the unread notification count and unseen notification count for the specified person. Note that
     * org announcements are not included in this query.
     *
     * TODO::This query is considering notifications against non-participating students. ESPRJ-16711 will address this.
     *
     *
     * @param $personId
     * @return array
     */
    public function getNotificationStatusCounts($personId)
    {
        $parameters = [
            'personId' => $personId
        ];

        $sql = "
            SELECT 
                SUM(IF(is_read = 0, 1, 0)) AS read_notification_count, 
                SUM(IF(is_seen = 0, 1, 0)) AS seen_notification_count
            FROM 
                synapse.alert_notifications
            WHERE
                deleted_at IS NULL 
                AND org_announcements_id IS NULL
                AND person_id = :personId; 
                
        ";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        return $result[0];
    }
    
    public function getAllToRemove($alertNotificationIds){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('IDENTITY(s.person) as person_id')
            ->from('SynapseCoreBundle:AlertNotifications', 's')
            ->where('s.id IN (:alertIds)')
            ->setParameters(array(
            'alertIds' => explode(',',$alertNotificationIds)
        ))
            ->groupBy('person_id')
            ->getQuery();
        //echo $qb->getDQL();exit;
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * Updates all unseen notifications as seen for the passed person ID
     *
     * @param int $personId
     * @return bool
     */
    public function updateAllUnseenNotificationsAsSeenForUser($personId)
    {
        $parameters = [
            'personId' => $personId
        ];

        $sql = "
            UPDATE
                alert_notifications
            SET
                is_seen = 1,
                modified_at = NOW(), 
                modified_by = :personId
            WHERE
                person_id = :personId
                AND is_seen = 0
                AND deleted_at IS NULL;
        ";

        $this->executeQueryStatement($sql, $parameters);
        return true;
    }

    /**
     * Updates all unread notifications as read and seen for the passed person ID
     *
     * @param int $personId
     * @return bool
     */
    public function updateAllUnreadNotificationsAsReadForUser($personId)
    {
        $parameters = [
            'personId' => $personId
        ];

        $sql = "
            UPDATE
                alert_notifications
            SET
                is_read = 1,
                is_seen = 1,
                modified_at = NOW(), 
                modified_by = :personId
            WHERE
                person_id = :personId
                AND deleted_at IS NULL
                AND is_read = 0;
        ";

        $this->executeQueryStatement($sql, $parameters);
        return true;
    }
}