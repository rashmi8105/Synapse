<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Entity\OfficeHoursSeries;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Doctrine\DBAL\Connection;

class OfficeHoursSeriesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OfficeHoursSeries';

    /**
     * Parent class override for typing.
     *
     * @param int $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return OfficeHoursSeries
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Parent class override for typing.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return OfficeHoursSeries[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Parent class override for typing.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return OfficeHoursSeries
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


    public function remove(OfficeHoursSeries $officeHoursSeries)
    {
        $this->getEntityManager()->remove($officeHoursSeries);
    }

    /**
     * Get the list of future office hour series which has not synced in Google
     * @param int $orgId
     * @param int $personId
     * @return array
     */
    public function getFutureOfficeHourSeriesForFaculty($orgId, $personId)
    {
        $sql = "SELECT 
                    DISTINCT(oh.office_hours_series_id)
                FROM
                    office_hours oh
                WHERE
                    oh.slot_start >= now()
                        AND oh.office_hours_series_id IN (SELECT 
                            id
                        FROM
                            office_hours_series
                        WHERE
                            organization_id = :orgId
                                AND person_id = :personId
                                AND google_master_appointment_id IS NULL
                                AND deleted_at IS NULL) and oh.deleted_at IS NULL";

        $parameters = ['orgId' => $orgId, 'personId' => $personId];
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Get the list of future office hours series which has modified in mapworks and not synced in google.
     * @param int $orgId
     * @param int $personId
     * @param string $modifiedTime
     * @return array
     */
    public function getFutureOfficeHoursSeriesModified($orgId, $personId, $modifiedTime)
    {
        $sql = "SELECT 
                    DISTINCT(oh.office_hours_series_id)
                FROM
                    office_hours oh
                WHERE
                    oh.slot_start >= now()
                        AND oh.office_hours_series_id IN (SELECT 
                            id
                        FROM
                            office_hours_series
                        WHERE
                            organization_id = :orgId
                                AND person_id = :personId                                
                                AND deleted_at IS NULL 
                                AND modified_at >= :modifiedTime 
                                AND last_synced <= :modifiedTime)";

        $parameters = ['orgId' => $orgId, 'personId' => $personId, 'modifiedTime' => $modifiedTime];
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Get the list of office hours series which has synced in google
     * @param int $orgId
     * @param int $personId
     * @return array
     */
    public function getSyncedOfficeHourSeries($orgId, $personId)
    {
        $sql = "SELECT 
                    *
                FROM
                    office_hours_series
                WHERE
                    organization_id = :orgId 
                      AND person_id = :personId
                      AND google_master_appointment_id IS NOT NULL";
        $parameters = ['orgId' => $orgId, 'personId' => $personId];
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute($parameters);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Get the list of office hours based on office hour series id.
     *
     * @param integer $officeHourSeriesId
     * @param string $currentTime
     * @param bool $deletedAtCheck
     * @return array
     */
    public function getOfficeHourSlotBySeriesId($officeHourSeriesId, $currentTime, $deletedAtCheck)
    {
        if ($deletedAtCheck) {
            $deletedAtCondition = " AND deleted_at IS NULL AND google_appointment_id IS NULL ";
        } else {
            $deletedAtCondition = " AND deleted_at IS NOT NULL AND google_appointment_id IS NOT NULL ";
        }

        $parameters = [
            'officeHourSeriesId' => $officeHourSeriesId,
            'currentTime' => $currentTime
        ];

        $sql = "SELECT 
                    id, 
                    google_appointment_id, 
                    appointments_id, 
                    slot_start, 
                    slot_end, 
                    location
                FROM
                    office_hours
                WHERE
                    office_hours_series_id = :officeHourSeriesId                    
                    $deletedAtCondition
                    AND slot_start > :currentTime
                ORDER BY slot_start ";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }
}