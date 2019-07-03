<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\CalendarSharingDto;

interface AppointmentsProxyServiceInterface
{

    public function createDelegateUser(CalendarSharingDto $calendarSharingDto);

    public function listManagedUsers($orgId, $proxyUserId);

    public function listSelectedProxyUsers($orgId, $userId);

    public function listProxyAppointments($proxyPersonId, $frequency, $managed_person_id);
}