<?php

/**
 * Class AppointmentsRepositoryTest
 */

use Codeception\TestCase\Test;

class AppointmentsRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Repository\AppointmentsRepository
     */
    private $appointmentsRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->appointmentsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Appointments');
    }

    public function testGetFutureAppointmentsModified()
    {
        $this->specify("Verify the functionality of the method getFutureAppointmentsModified", function ($orgId, $personId, $expectedCount) {

            $startDate = new \DateTime('now');
            $slotEndDate = new \DateTime($startDate->format('Y-m-d H:i:s'));
            $modifiedTime = $slotEndDate->sub(new \DateInterval("P1D"));

            $results = $this->appointmentsRepository->getFutureAppointmentsModified($orgId, $personId, $startDate->format('Y-m-d H:i:s'), $modifiedTime->format('Y-m-d H:i:s'), 'S');
            if (count($results) > 0) {
                verify(count($results))->greaterThan($expectedCount);

                foreach ($results as $appointment) {
                    verify($appointment)->notEmpty();
                    verify($appointment->getGoogleAppointmentId())->notEmpty();
                }

            } else {
                verify(count($results))->equals($expectedCount);
            }


        }, ["examples" =>
            [
                [203, 4878750, 0],
                [203, 4891668, 0]
            ]
        ]);
    }

    public function testIsOverlappingAppointments()
    {
        $this->specify('test is appointment overlapping with existing appointments', function ($expectedResult, $organizationId, $personId, $startDate, $endDate, $exclude) {
            $results = $this->appointmentsRepository->isOverlappingAppointments($personId, $organizationId, $startDate, $endDate, $exclude);
            verify($expectedResult)->equals($results);

        }, ['examples' => [
            [
                true,
                203,
                4891668,
                '2016-12-08 12:30:00',
                '2016-12-08 13:30:00',
                null
            ],
            [
                false,
                203,
                4891668,
                '2017-01-18 10:00:00',
                '2017-01-18 10:30:00',
                null
            ],
            [
                false,
                203,
                4891668,
                '2017-02-11 02:00:00',
                '2017-02-11 03:00:00',
                26168
            ]
        ]]);
    }

    public function testGetSyncedMafAppointments()
    {
        $this->specify("Verify the functionality of the method getSyncedMafAppointments", function ($organizationId, $personId, $expectedCount) {

            $results = $this->appointmentsRepository->getSyncedMafAppointments($organizationId, $personId);
            if (count($results) > 0) {
                verify(count($results))->greaterThan($expectedCount);
                foreach ($results as $appointment) {
                    verify($appointment)->notEmpty();
                    verify($appointment['appointment_id'])->notEmpty();
                }
            } else {
                verify(count($results))->equals($expectedCount);
            }
        }, ["examples" =>
            [
                [203, 4878750, 0],
                [203, 4891668, 0]
            ]
        ]);
    }

}