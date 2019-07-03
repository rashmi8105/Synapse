<?php
namespace Synapse\MultiCampusBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\MultiCampusBundle\Entity\OrgUsers;

class OrgUsersRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseMultiCampusBundle:OrgUsers';

    public function remove(OrgUsers $tierUsers)
    {
        $em = $this->getEntityManager();
        $em->remove($tierUsers);
    }

    public function create(OrgUsers $tierUsers)
    {
        $em = $this->getEntityManager();
        $em->persist($tierUsers);
        return $tierUsers;
    }

    public function usersCount($organization)
    {
        $em = $this->getEntityManager();
        $count = $em->createQueryBuilder()
            ->select('count(person.id)')
            ->from('SynapseCoreBundle:Organization', 'org')
            ->LEFTJoin('SynapseMultiCampusBundle:OrgUsers', 'person', \Doctrine\ORM\Query\Expr\Join::WITH, 'org.id = person.organization')
            ->where('person.organization = :organization')
            ->setParameters(array(
            'organization' => $organization
        ))
            ->getQuery()
            ->getSingleScalarResult();
        return $count;
    }
}