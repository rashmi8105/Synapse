<?php

/**
 * Class OfficeHoursRepositoryTest
 */

use Codeception\TestCase\Test;

class OfficeHoursRepositoryTest extends Test
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
     * @var \Synapse\CoreBundle\Repository\OfficeHoursRepository
     */
    private $officeHoursRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->officeHoursRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OfficeHours');
    }

    public function testGetFutureOfficeHoursForFaculty()
    {
        $this->specify("Verify the functionality of the method getFutureOfficeHoursForFaculty", function ($organizationId, $personId, $expectedCount) {

            $startDate = new \DateTime('now');
            $startTime = $startDate->format('Y-m-d H:i:s');
            $results = $this->officeHoursRepository->getFutureOfficeHoursForFaculty($organizationId, $personId, $startTime);
            verify(count($results))->equals($expectedCount);
            if (count($results) > 0) {
                foreach ($results as $officeHour) {
                    verify($officeHour)->notEmpty();
                }
            }
        }, ["examples" =>
            [
                [203, 4878750, 0],
                [203, 4891668, 3]
            ]
        ]);
    }


    public function testGetFutureOfficeHoursModified()
    {
        $this->specify("Verify the functionality of the method getFutureOfficeHoursModified", function ($orgId, $personId, $expectedCount) {

            $startDate = new \DateTime('now');
            $slotEndDate = new \DateTime($startDate->format('Y-m-d H:i:s'));
            $modifiedTime = $slotEndDate->sub(new \DateInterval("P1D"));

            $results = $this->officeHoursRepository->getFutureOfficeHoursModified($orgId, $personId, $startDate, $modifiedTime, 'S');
            if(count($results) > 0) {
                verify(count($results))->greaterThan($expectedCount);

                foreach($results as $officeHour) {
                    verify($officeHour)->notEmpty();
                    verify($officeHour->getGoogleAppointmentId())->notEmpty();
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


    public function testGetDeletedFutureOfficeHours()
    {
        $this->specify("Verify the functionality of the method getDeletedFutureOfficeHours", function ($orgId, $personId, $expectedCount) {

            $startDate = new \DateTime('now');
            $slotEndDate = new \DateTime($startDate->format('Y-m-d H:i:s'));
            $modifiedTime = $slotEndDate->sub(new \DateInterval("P1D"));

            $results = $this->officeHoursRepository->getDeletedFutureOfficeHours($orgId, $personId, $startDate->format('Y-m-d H:i:s'), $modifiedTime->format('Y-m-d H:i:s'));
            if(count($results) > 0) {
                verify(count($results))->greaterThan($expectedCount);

                foreach($results as $officeHour) {
                    verify($officeHour)->notEmpty();
                    verify($officeHour['google_appointment_id'])->notEmpty();
                    verify($officeHour['deleted_at'])->notEmpty();
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


    public function testUpdateOfficeHoursWithNULL()
    {
        $this->specify("Verify the functionality of the method updateOfficeHoursWithNULL", function ($orgId, $personId, $expectedCount) {

            $result = $this->officeHoursRepository->findOneBy(array('person' => $personId, 'organization' => $orgId));
            if($result) {
                $table = 'office_hours';
                $field = 'google_appointment_id';
                $this->officeHoursRepository->updateOfficeHoursWithNULL($table, $field, $result->getId());
                $officeHour = $this->officeHoursRepository->findOneBy(array('id' => $result->getId()));

                verify($officeHour)->notEmpty();
                verify($officeHour->getGoogleAppointmentId())->equals(NULL);
                verify($officeHour->getLastSynced())->equals(NULL);

            } else {
                verify(count($result))->equals($expectedCount);
            }


        }, ["examples" =>
            [
                [203, 4878750, 0]
            ]
        ]);
    }

}