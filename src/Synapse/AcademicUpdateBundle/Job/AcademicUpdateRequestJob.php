<?php
namespace Synapse\AcademicUpdateBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateRequestService;

class AcademicUpdateRequestJob extends ContainerAwareJob
{
    const JOB_KEY = 'AcademicUpdateRequestJob';

    /**
     * @var AcademicUpdateRequestService
     */
    private $academicUpdateRequestService;

    public function run($args)
    {
        $academicUpdateCreateDto = unserialize($args['academicUpdateCreateDto']);
        $userId = $args['userId'];
        $this->academicUpdateRequestService = $this->getContainer()->get('academicupdaterequest_service');
        $logger = $this->getContainer()->get('logger');
        try {
            $this->academicUpdateRequestService->createAcademicUpdateRequest($academicUpdateCreateDto, $userId);
            $this->flushSpooler();
        } catch (\Exception $e) {
            $logger->error("Academic Update Request Creation Failed" . $e->getMessage());
        }
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
        
        $spool->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
    }
}