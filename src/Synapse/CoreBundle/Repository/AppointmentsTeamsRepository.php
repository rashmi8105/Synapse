<?php
namespace Synapse\CoreBundle\Repository;
use Synapse\CoreBundle\Entity\AppointmentsTeams;

class AppointmentsTeamsRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:AppointmentsTeams';

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return AppointmentsTeams[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function createAppointmentsTeams($appointmentTeams){
        $em = $this->getEntityManager();
        $em->persist($appointmentTeams);

        return $appointmentTeams;
    }
    public function deleteAppointmentTeam($appointmentTeams){

        $em = $this->getEntityManager();
        $em->remove($appointmentTeams);
        return $appointmentTeams;
    }
    
    public function getAppointmentsTeamIds($appointmentId)
    {
        $em = $this->getEntityManager();
        $records = $em->createQueryBuilder()
        ->select('IDENTITY(at.teamsId) as teams_id')
        ->from('SynapseCoreBundle:AppointmentsTeams', 'at')
        ->where('at.appointmentsId = :id')
        ->setParameter('id', $appointmentId)
        ->getQuery()->getResult();
        return $records;
    }
}