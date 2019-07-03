<?php

namespace Synapse\CoreBundle\Repository;


use Synapse\CoreBundle\Entity\EmailTemplate;

class EmailTemplateRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:EmailTemplate';

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return EmailTemplate|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

}