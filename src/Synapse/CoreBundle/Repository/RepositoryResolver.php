<?php

namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;

/**
 *
 * @DI\Service("repository_resolver")
 *
 */
class RepositoryResolver {


    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @DI\InjectParams({
     *     "em" = @DI\Inject("doctrine.orm.entity_manager"),
     * })
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Doctrine repository
     * @param $model
     * @return EntityRepository
     */
    public function getRepository($model)
    {
        return $this->em->getRepository($model);
    }




}