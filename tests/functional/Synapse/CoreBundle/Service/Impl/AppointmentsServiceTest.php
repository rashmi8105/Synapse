<?php

/**
 * Class AppointmentsServiceTest
 */

use Codeception\TestCase\Test;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Entity\AttendeesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\CalendarBundle\Util\CalendarHelper;
use \DateTime;

class AppointmentsServiceTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Service\Impl\AppointmentsService
     */
    private $appointmentsService;

    /**
     * @var int
     */
    private $personId = 4878750;

    /**
     * @var int
     */
    private $orgId = 203;

    public function testViewAppointment()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->appointmentService = $this->container->get('appointments_service');
            $rbacMan = $this->container->get('tinyrbac.manager');
            $rbacMan->initializeForUser($this->personId);
        });

        $this->specify("Verify the functionality of the method viewAppointment", function ($appointmentId ,$expectedResult)
        {
            $resultSet = $this->appointmentService->viewAppointment($this->orgId, $this->personId, $appointmentId, $this->personId);
            verify($resultSet['appointment_id'])->equals($expectedResult[0]);
            verify($resultSet['person_id'])->equals($expectedResult[1]);
            verify($resultSet['organization_id'])->equals($expectedResult[2]);
            verify($resultSet['detail'])->equals($expectedResult[3]);
            verify($resultSet['location'])->equals($expectedResult[4]);
            verify($resultSet['description'])->equals($expectedResult[5]);

        }, ["examples" =>
            [
                ['5650', ['5650','4878750','203',"Academic performance concern ", "Kurt's Office","Test Text"]],
                ['5649', ['5649','4878750','203',"Class attendance positive", "Kurt's Office","Very Long Appointment"]]
            ]
        ]);
    }

    public function testCancelAppointment()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->appointmentsService = $this->container->get('appointments_service');
            $this->appointmentsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Appointments');
            $rbacMan = $this->container->get('tinyrbac.manager');
            $rbacMan->initializeForUser(4878750); // so that user has the proper access
        });

        $this->specify("Verify the functionality of the method cancelAppointment", function ($orgId, $slotStartDate) {

            $createAppointmentsDto = $this->createAppointmentsDto($orgId, $slotStartDate);
            $appointmentDetails = $this->appointmentsService->create($createAppointmentsDto);
            $results = $this->appointmentsService->cancelAppointment($orgId, $appointmentDetails->getAppointmentId());
            verify($results)->notEmpty();
            verify($results)->equals($appointmentDetails->getAppointmentId());
            verify($results)->notInternalType('string');
        }, ["examples" =>
            [
                [203, $slotStartDate = new \DateTime('now')],
                [203, $slotStartDate = new \DateTime('+1 hour')]
            ]
        ]);

    }

    private function createAppointmentsDto($orgId, $slotStartDate)
    {

        $appointmentsDto = new AppointmentsDto();
        $appointmentsDto->setOrganizationId($orgId);
        $appointmentsDto->setPersonId($this->personId);
        $appointmentsDto->setDetail('Class attendance positive');
        $appointmentsDto->setDetailId(20);
        $appointmentsDto->setLocation('Bangalore');
        $appointmentsDto->setIsFreeStanding(true);
        $appointmentsDto->setType('F');
        $appointmentsDto->setAttendees($this->createAttendeesDto());
        $slotStartDate->add(new \DateInterval('PT24H'));
        $slotEndtDate = clone $slotStartDate;
        $slotEndtDate->add(new \DateInterval('PT24H'));
        $appointmentsDto->setSlotStart($slotStartDate);
        $appointmentsDto->setSlotEnd($slotEndtDate);
        $appointmentsDto->setShareOptions([$this->createShareOptionsDto()]);


        return $appointmentsDto;
    }


    private function createAttendeesDto()
    {

        $attendeesDto = new AttendeesDto();
        $attendeesDto->setStudentId(4879508);
        $attendeesDto->setStudentFirstName('Augustine');
        $attendeesDto->setStudentLastName('Morton');
        $attendeesDto->setIsSelected(true);

        return $attendeesDto;
    }

    private function createShareOptionsDto()
    {

        $shareOptionsDto = new ShareOptionsDto();
        $shareOptionsDto->setPrivateShare(false);
        $shareOptionsDto->setPublicShare(true);
        $shareOptionsDto->setTeamsShare(false);
        $shareOptionsDto->setTeamIds($this->createTeamIdsDto());

        return $shareOptionsDto;
    }

    private function createTeamIdsDto()
    {

        $teamIdsDto = new TeamIdsDto();
        $teamIdsDto->setId(0);
        $teamIdsDto->setIsTeamSelected(false);

        return $teamIdsDto;
    }
}