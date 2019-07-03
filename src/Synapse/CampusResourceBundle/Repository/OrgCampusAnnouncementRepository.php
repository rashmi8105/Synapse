<?php
namespace Synapse\CampusResourceBundle\Repository;

use Synapse\CampusResourceBundle\Entity\OrgAnnouncements;
use Synapse\CampusResourceBundle\Util\Constants\CampusResourceConstants;
use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgCampusAnnouncementRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCampusResourceBundle:OrgAnnouncements';

    /**
     * @param mixed $id
     * @param \Exception $exception
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|OrgAnnouncements
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }


    public function deleteCampusAnnouncement($orgAnnouncements)
    {
        $em = $this->getEntityManager();
        $em->remove($orgAnnouncements);
        return $orgAnnouncements;
    }
}