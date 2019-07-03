<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class DatablockMasterRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:DatablockMaster';

    public function removeDataBlockMaster($datablock)
    {
        $em = $this->getEntityManager();
        $em->remove($datablock);
    }

    public function deleteSurveyBlock($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('SynapseCoreBundle:DatablockMasterLang', 'm');
        $qb->set('m.deletedAt', 'CURRENT_TIMESTAMP()');
        $qb->where($qb->expr()
            ->eq('m.datablock', ':id'));
        $qb->setParameters(array(
            'id' => $id
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
}