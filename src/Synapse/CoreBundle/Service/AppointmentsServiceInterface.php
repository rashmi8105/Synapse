<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\CalendarSharingDto;

interface AppointmentsServiceInterface
{

    /**
     *
     * @param AppointmentsDto $appointmentsDto            
     */
    public function create(AppointmentsDto $appointmentsDto);

    public function checkIfActAsProxy($userId);

    public function cancelAppointment($orgId, $appointmentId);

    /**
     *
     * @param AppointmentsDto $appointmentsDto            
     */
    public function editAppointment(AppointmentsDto $appointmentsDto);

    public function getAppointmentsByUser($orgId, $personId, $filter);

    public function viewAppointment($orgId, $personId, $appointmentId, $loggedInPerson);

    public function saveAppointmentAttendees(AppointmentsDto $appointmentsDto);

    public function viewTodayAppointment($filter, $orgId, $personId, $timezone);
}
