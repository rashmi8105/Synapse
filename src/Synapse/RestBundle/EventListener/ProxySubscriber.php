<?php

namespace Synapse\RestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Util\Constants\PersonConstant;

class ProxySubscriber
{
    /**
     * Symfony Container
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

   public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $switchUser = $request->headers->get('switch-user');
        if(!$switchUser)
        {
            $switchUser = $request->get('switch-user');
        }
        if(trim($switchUser) != ""){
            $person = $this->container->get('person_service');
            $switchUserObj = $person->find($switchUser);
            if ($switchUserObj) {
                $orgRoleRepository = $this->container->get('repository_resolver')->getRepository(PersonConstant::ORG_ROLE_REPO);
                $ebiUser = $this->container->get('security.context')
                    ->getToken()
                    ->getUser();
                $loggedinUserOrgId = $ebiUser->getOrganization()->getId();
                $proxyUserOrgId = $switchUserObj->getOrganization()->getId();
                $coordinators = $orgRoleRepository->findBy(array(
                    PersonConstant::FIELD_ORGANIZATION => $loggedinUserOrgId,
                    PersonConstant::FIELD_PERSON => $ebiUser
                ));
                $isCorridnator = false;
                if ($loggedinUserOrgId == - 1) {
                    $isCorridnator = true;
                } elseif ($coordinators) {
                    $isCorridnator = true;
                }else{
                    $isCorridnator = false;
                }
                
                if ($loggedinUserOrgId != - 1 && ($loggedinUserOrgId != $proxyUserOrgId || ! $isCorridnator)) {
                    
                    throw new AccessDeniedException();
                }
                $this->container->set('ebi_user', $ebiUser);
                $this->container->set('proxy_user', $switchUserObj);
            }
        }
    }
}