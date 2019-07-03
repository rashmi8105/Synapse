<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\CalendarBundle\Service\Impl\CronofyWrapperService;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\ReferralRepository;
use Synapse\CoreBundle\Service\Impl\AppointmentsService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\ReferralConstant;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;

class InactiveFacultyJob extends ContainerAwareJob
{

    private $logger;

    public function run($args)
    {
        // Remove the Group associations
        $organizationId = $args['orgId'];
        $facultyIds = $args['facultyIds'];
        $this->logger = $this->getContainer()->get('logger');
        $jobNumber = $args['jobNumber'];
        
        $repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        
        $orgGroupFacultyRepository = $repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $orgCourseFacultyRepository = $repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
        $appointmentRepository = $repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $appointmentService = $this->getContainer()->get(AppointmentsService::SERVICE_KEY);
        $cronofyWrapperService = $this->getContainer()->get(CronofyWrapperService::SERVICE_KEY);
        $mapworksActionService = $this->getContainer()->get(MapworksActionService::SERVICE_KEY);
        $referralRepository = $repositoryResolver->getRepository(ReferralRepository::REPOSITORY_KEY);
        $studentAppointmentService = $this->getContainer()->get(StudentAppointmentService::SERVICE_KEY);

        if (count($facultyIds) > 0) {
            foreach ($facultyIds as $facultyId) {
                $this->logger->info("----------------- Inactive Faculty Association Remove --: start faculty " . $facultyId);

                // Appoitment cancellation
                try {
                    $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment Start");
                    $userAppointments = $appointmentRepository->getUpcomingAppointments($facultyId);
                    $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment count : " . count($userAppointments));
                    if ($userAppointments) {
                        foreach ($userAppointments as $userAppointment) {
                            $appointmentService->cancelAppointment($organizationId, $userAppointment->getId(), true);
                        }
                    }
                } catch (\Exception $appExp) {
                    $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment Exception " . $appExp->getMessage());
                    $this->logger->info($appExp->getTraceAsString());
                }
                $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment End");
                
                // Student Creation appointment Canceltaion
                try {
                    $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment Student Created Start");
                    $studentCreatedAppointments = $appointmentRepository->getUpcomingAppointmentsStudentCreated($facultyId);
                    $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment Student Created count : ".count($studentCreatedAppointments));
                    $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment Student Created type : ".gettype($studentCreatedAppointments));
                    if($studentCreatedAppointments)
                    {
                        foreach ($studentCreatedAppointments as $studentCreatedAppointment)
                        {
                            $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment Student Created Start Cancelling : ".gettype($studentCreatedAppointment));
                            $studentAppointmentService->cancelStudentAppointment($studentCreatedAppointment->getPersonIdStudent()->getId(), $studentCreatedAppointment->getAppointments()->getId(),true);
                        }
                        $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment Student Created Start Canceled");
                    }
                    
                }catch (\Exception $appStudExp) {
                    $this->logger->info("----------------- Inactive Faculty Association Remove --: Appointment Exception " . $appExp->getMessage());
                    $this->logger->info($appExp->getTraceAsString());
                }
                
                // Referral Cancelation
                $this->logger->info("----------------- Inactive Faculty Association Remove --: Referral Start");
                $referrals = $referralRepository->findBy([
                    'status' => 'O',
                    'personAssignedTo' => $facultyId
                ]);
                $this->logger->info("----------------- Inactive Faculty Association Remove --: Referral count : " . count($referrals));
                
                if ($referrals) {
                    try {
                        foreach ($referrals as $referral) {
                            $this->logger->info("----------------- Inactive Faculty Association Remove --: Referral Reassign for " . $referral->getId());
                            $this->reAssignReferral($referral);
                        }
                        $referralRepository->flush();
                    } catch (\Exception $refExp) {
                        $this->logger->info("----------------- Inactive Faculty Association Remove --: Referral Exception " . $refExp->getMessage());
                        $this->logger->info($refExp->getTraceAsString());
                    }
                }
                $orgGroupFacultyRepository->deleteBulkFacultyEnrolledGroups($facultyId, $organizationId);
                $orgCourseFacultyRepository->deleteBulkFacultyEnrolledCourse($facultyId, $organizationId);
                $this->logger->info("----------------- Inactive Faculty Association Remove --: end faculty " . $facultyId);

                // Revoke access for external calendar
                $this->logger->info("----------------- Inactive Faculty Access Revoked --: start faculty " . $facultyId);
                try {
                    $cronofyWrapperService->removeEventByFaculty($organizationId, true, $facultyId, false);
                } catch (\Exception $exception) {
                    $tokenValues['$$event_id$$'] = $jobNumber;
                    $mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'desync_failed', 'creator', 'calendar_sync', $facultyId, "A calendar sync error occurred", NULL, NULL, $tokenValues);
                }
                $this->logger->info("----------------- Inactive Faculty Access Revoked --: end faculty " . $facultyId);
            }
        }
        $this->flushSpooler();
    }

    private function reAssignReferral($referral)
    {
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $referralRoutingRulesRepo = $repositoryResolver->getRepository(ReferralConstant::REFERRAL_ROUTE_RULE_REPO);
        
        $activityRef = $referralRoutingRulesRepo->findOneBy(array(
            'organization' => $referral->getOrganization(),
            'activityCategory' => $referral->getActivityCategory()
        ));
        if ($activityRef->getPerson()) {
            $this->logger->info("----------------- Inactive Faculty Association Remove --: Referral Reassinged to Specific Person: ");
            
            $referral->setPersonAssignedTo($activityRef->getPerson());
        } elseif ($activityRef->getIsPrimaryCampusConnection()) {
            
            $this->setPrimaryAssignee($referral);
        } else {
            $this->logger->info("----------------- Inactive Faculty Association Remove --: Referral Central Coord: ");
            $referral->setPersonAssignedTo(null);
        }
    }

    public function setPrimaryAssignee($referral)
    {
        $personService = $this->getContainer()->get('person_service');
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $orgPersonStudentRepository = $repositoryResolver->getRepository(ReferralConstant::ORG_PERSON_STUDENT_REPO);
        $studentDetails = $orgPersonStudentRepository->findOneBy(array(
            'organization' => $referral->getOrganization(),
            'person' => $referral->getPersonStudent()
        ));
        if ($studentDetails->getPersonIdPrimaryConnect()) {
            $this->logger->info("----------------- Inactive Faculty Association Remove --: Referral Reassinged to Primary Connect: ");
            $personAssignedTo = $personService->findPerson($studentDetails->getPersonIdPrimaryConnect());
            $referral->setPersonAssignedTo($personAssignedTo);
        } else {
            $this->logger->info("----------------- Inactive Faculty Association Remove --: Referral Reassinged to Cetral Coord - no primary connect ");
            $referral->setPersonAssignedTo(null);
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
    
        $spool->flushQueue($this->getContainer()
            ->get('swiftmailer.transport.real'));
    }
}