<?php
namespace Synapse\CoreBundle\Repository;
use Synapse\CoreBundle\Entity\Email;

class EmailRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:Email';

    public function createEmail($email){

        $em = $this->getEntityManager();
        $em->persist($email);
        return $email;
    }

    public function deleteEmail(Email $email)
    {
        $em = $this->getEntityManager();
        $em->remove($email);
        return $email;
    }

}