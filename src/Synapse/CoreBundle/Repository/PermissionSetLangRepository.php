<?php
namespace Synapse\CoreBundle\Repository;
use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Synapse\CoreBundle\Entity\PermissionSetLang;

class PermissionSetLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:PermissionSetLang';

    public function createPermissionSetLang(PermissionSetLang $permissionSetLang){

        $em = $this->getEntityManager();
        $em->persist($permissionSetLang);

        return $permissionSetLang;
    }
}