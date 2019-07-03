<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\CoreBundle\Util\Constants\UsersConstant;

class BulkInviteJob extends ContainerAwareJob
{

    public function run($args)
    {
        $organizationId = $args['organization'];
        $type = $args['type'];
        
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $orgRoleRepository = $repositoryResolver->getRepository(UsersConstant::ORG_ROLE_REPO);
        $orgPersonFacultyRepository = $repositoryResolver->getRepository(UsersConstant::ORG_PERSON_FACULTY_REPO);
        $usersService = $this->getContainer()->get('users_service');
        if ($type == UsersConstant::FILTER_COORDINATOR) {
            $getNonLoggedInUserList = $orgRoleRepository->getNonLoggedInCoordinator($organizationId);
        } else {
            $getNonLoggedInUserList = $orgPersonFacultyRepository->getNonLoggedInFaculty($organizationId);
        }
        
        if (! empty($getNonLoggedInUserList)) {
            foreach ($getNonLoggedInUserList as $getNonLoggedInUser) {
                
                $usersService->sendInvitation($organizationId, $getNonLoggedInUser['person_id'], $type);
            }
        }
        $this->flushSpooler();
    }
    
    public function flushSpooler()
    {
        $mailer = $this->getContainer()->get('mailer');
        $transport = $mailer->getTransport();
        if (! $transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }
        $spool = $transport->getSpool();
        if (! $spool instanceof \Swift_MemorySpool) {
            return;
        }
    
        $spool->flushQueue($this->getContainer()
            ->get('swiftmailer.transport.real'));
    }
}