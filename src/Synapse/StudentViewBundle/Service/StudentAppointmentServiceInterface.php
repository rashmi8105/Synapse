<?php
namespace Synapse\StudentViewBundle\Service;

use Synapse\RestBundle\Entity\AppointmentsDto;

interface StudentAppointmentServiceInterface
{

    public function getStudentsUpcomingAppointments($studentId, $timezone);

    public function getStudentCampuses($studentId);

    public function getStudentCampusConnections($studentId, $orgId, $timezone);

    public function getFacultyOfficeHours($orgId, $timezone, $facultyId, $filter);

    public function createStudentAppointment($studentId, AppointmentsDto $appointmentsDto, $timezone);

    public function cancelStudentAppointment($studentId, $appointmentId);
}