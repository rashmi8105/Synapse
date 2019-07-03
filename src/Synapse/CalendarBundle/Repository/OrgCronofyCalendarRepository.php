<?php
namespace Synapse\CalendarBundle\Repository;

use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CalendarBundle\Entity\OrgCronofyCalendar;

class OrgCronofyCalendarRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCalendarBundle:OrgCronofyCalendar';

    /**
     * @param int $id
     * @param \Exception $exception
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|object
     * @throws \Exception
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @param \Exception $exception
     * @return array
     * @throws \Exception
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $array = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($array, $exception);
    }

    /**
     * @param array $criteria
     * @param \Exception $exception
     * @param array|null $orderBy
     * @return null|BaseEntity | OrgCronofyCalendar
     * @throws \Exception
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }


    public function create(OrgCronofyCalendar $orgCronofyCalendar)
    {
        $em = $this->getEntityManager();
        $em->persist($orgCronofyCalendar);
        return $orgCronofyCalendar;
    }

    /**
     * Gets the list of users per organization using Cronofy to sync their calendars
     *
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getListOfCronofyCalendarSyncUsers($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "SELECT 
                    person_id
                FROM
                    org_cronofy_calendar
                WHERE
                    organization_id = :organizationId
                        AND status = 1
                        AND deleted_at IS NULL";

        try {
            $em = $this->getEntityManager();
            $statement = $em->getConnection()->prepare($sql);
            $statement->execute($parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $statement->fetchAll();
        return $result;
    }
}