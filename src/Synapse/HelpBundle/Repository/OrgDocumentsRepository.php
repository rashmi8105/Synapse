<?php
namespace Synapse\HelpBundle\Repository;

use Synapse\HelpBundle\Entity\OrgDocuments;
use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgDocumentsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseHelpBundle:OrgDocuments';

    /**
     *
     * @param OrgDocuments $orgDocuments            
     * @return OrgDocuments
     */
    public function create(OrgDocuments $orgDocuments)
    {
        $em = $this->getEntityManager();
        $em->persist($orgDocuments);
        return $orgDocuments;
    }

    public function getOrgDoc($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('od.id as id', 'od.title as title', 'od.description as description', 'od.type as type', 'od.link as link', 'od.filePath as file_path', 'od.displayFilename as display_filename');
        $qb->from('SynapseHelpBundle:OrgDocuments', 'od');
        $qb->where('od.orgId = :orgId AND od.deletedBy IS NULL');
        $qb->setParameters(array(
            'orgId' => $orgId
        ));
        $qb->orderBy('od.createdAt', 'DESC');
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    public function getSingleHelpDetails($helpId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('od.id as id', 'od.title as title', 'od.description as description', 'od.type as type', 'od.link as link', 'od.filePath as file_path', 'od.displayFilename as display_filename');
        $qb->from('SynapseHelpBundle:OrgDocuments', 'od');
        $qb->where('od.id = :id AND od.deletedBy IS NULL');
        $qb->setParameters(array(
            'id' => $helpId
        ));
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    /**
     *
     * @param OrgDocuments $orgDocuments            
     */
    public function remove(OrgDocuments $orgDocuments)
    {
        $em = $this->getEntityManager();
        $em->remove($orgDocuments);
    }
}