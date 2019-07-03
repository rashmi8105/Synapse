<?php
namespace Synapse\AuditTrailBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

use Synapse\AuditTrailBundle\Entity\AuditTrail;

class WriteAuditTrail extends ContainerAwareJob
{

    public function run($args)
    {
        extract($args);

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $auditTrailRepository = $repositoryResolver->getRepository('SynapseAuditTrailBundle:AuditTrail');
        $personService = $this->getContainer()->get('person_service');

        if ($person) {
            $person = $personService->find($person);
        }
        
        if (is_numeric($ebiUser)) {
            $ebiUser = $personService->find($ebiUser);
        }else{
            $ebiUser = null;
        }

        $auditTrail = new AuditTrail;

        $auditTrail->setRoute($route);
        $auditTrail->setClass($class);
        $auditTrail->setMethod($method);
        $auditTrail->setRequest($request);
        $auditTrail->setUnitOfWork($unitOfWork);
        $auditTrail->setAuditedAt(\DateTime::createFromFormat('U', $time));
        $auditTrail->setPerson($person);
        $auditTrail->setProxyBy($ebiUser);
        $auditTrailRepository->persist($auditTrail);
    }
}