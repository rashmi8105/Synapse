<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\CoreBundle\Service\Impl\AppointmentsService;

/**
 * Add multiple attendess
 */
class BulkAppointmentJob extends ContainerAwareJob
{

    /**
     * @var AppointmentsService
     */
    private $appointmentsService;

    public function run($args)
    {
        $attendees = unserialize($args['attendees']);
        $appointmentId  = $args['appointments'];
        $personId = $args['person'];
        $personIdProxy = $args['personIdProxy'];
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $this->appointmentsService = $this->getContainer()->get('appointments_service');
        $personService = $this->getContainer()->get('person_service');
        $person = $personService->findPerson($personId);
        $appRepo = $repositoryResolver->getRepository('SynapseCoreBundle:Appointments');
        $appointments =  $appRepo->find($appointmentId);
        $logger = $this->getContainer()->get('logger');
        try {
            $this->appointmentsService->updateAppointmentRecipientAndStatus($attendees, $appointments, $person, false, 'create', [],$personIdProxy,true);
            
        } catch (\Exception $e) {
            $logger->error("Appointment Creation Failed" . $e->getMessage());
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