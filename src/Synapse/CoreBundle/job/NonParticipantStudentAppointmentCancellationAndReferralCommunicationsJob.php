<?php

namespace Synapse\CoreBundle\job;

use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Service\Impl\AppointmentsService;
use Synapse\CoreBundle\Service\Impl\ReferralService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Job\ContainerAwareQueueJob;
use Synapse\StudentViewBundle\Service\Impl\StudentAppointmentService;

class NonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob extends ContainerAwareQueueJob
{

    const JOB_KEY = 'NonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob';

    //services

    /**
     * @var AppointmentsService
     */
    private $appointmentService;

    /**
     * @var ReferralService
     */
    private $referralService;

    /**
     * NonParticipantStudentAppointmentCancellationAndReferralCommunicationsJob constructor.
     */
    public function __construct()
    {
        $this->queue = SynapseConstant::DEFAULT_QUEUE;
        $this->setJobType(self::JOB_KEY);
        $this->setAction('failed');
        $this->setRecipientType('creator');
        $this->setEventType('appointment_cancellation_and_referral_communication');
        $this->setNotificationReason('An error occurred while cancelling appointments and sending referral related mails while updating student participation status');

    }

    /**
     * This will get executed from the run method in ContainerAwareQueueJob
     *
     * @param $args
     */
    public function executeJob($args)
    {
        $studentId = $args['studentId'];
        $currentDate = $args['currentDate'];
        $organizationId = $args['organizationId'];

        $this->appointmentService = $this->getContainer()->get(AppointmentsService::SERVICE_KEY);
        $this->referralService = $this->getContainer()->get(ReferralService::SERVICE_KEY);

        $this->appointmentService->cancelStudentAppointments($studentId, $currentDate);
        $mapworksAction = 'student_made_nonparticipant';

        $communicationsSent = $this->referralService->sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate($studentId, $organizationId, $mapworksAction);
        if (!$communicationsSent) {
            throw new SynapseValidationException('Communication failed for notifying faculty with active referrals on the student');
        }
    }


}