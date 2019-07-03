<?php
namespace Synapse\CoreBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\Organization;

class FutureIsqPermissionSet extends ContainerAwareJob
{

    public function run($args)
    {
        $organizationId = $args['organization'];
        $langId = $args['langId'];
        $this->repositoryResolver = $this->getContainer()->get('repository_resolver');
        $this->organizationRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Organization');
        
        $this->getContainer()->get('orgpermissionset_service')->FutureIsqPermissionSet();
    }
}