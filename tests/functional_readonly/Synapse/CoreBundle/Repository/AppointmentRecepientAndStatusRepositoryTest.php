<?php
use Codeception\TestCase\Test;

/**
 * Class AppointmentRecepientAndStatusRepositoryTest
 */
class AppointmentRecepientAndStatusRepositoryTest extends Test
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
     * @var \Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository
     */
    private $appointmentRecipientAndStatusRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->appointmentRecipientAndStatusRepository = $this->repositoryResolver->getRepository(\Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
    }

    public function testGetParticipantAttendeesForAppointment()
    {
        $this->specify("Verify the functionality of the method GetParticipantAttendeesForAppointment", function ($organizationId, $facultyId, $appointmentId, $orgAcademicYearId, $expectedResult) {
            $results = $this->appointmentRecipientAndStatusRepository->getParticipantAttendeesForAppointment($organizationId, $facultyId, $appointmentId, $orgAcademicYearId);
            verify($results)->equals($expectedResult);
        }, [
            "examples" => [
                // checks for the list of participating student  for  appointment id 6850 ,participant in the year 167 , 1 participating student (considering 167 as current academic year)
                [204, 4893111, 6850, 167, [
                    [
                        'student_id' => 4893137,
                        'student_first_name' => 'Lennox',
                        'student_last_name' => 'Washington',
                        'is_attended' => 0
                    ]
                ]],
                // checks for the list of participating student  for an appointment id 6850 ,participant in the year 182 , 0 participating student (considering 182 as current academic year)
                [204, 4893111, 6850, 182, []],
                // checks for the list of participating student  for an appointment id 6846 ,participant in the year 167 , 1 participating student (considering 167 as current academic year)
                [204, 4893111, 6846, 167, [
                    [
                        'student_id' => 4893127,
                        'student_first_name' => 'Noe',
                        'student_last_name' => 'Harrison',
                        'is_attended' => 0
                    ]
                ]],
                // checks for the list of participating student  for an appointment id 6846 ,participant in the year 182 , 0 participating student (considering 182 as current academic year)
                [204, 4893111, 6846, 182, []],
                // checks for the list of participating student  for an appointment id 6834 ,participant in the year 3 , 1 participating student (considering 3 as current academic year)
                [181, 4884542, 6834, 3, [
                    [
                        'student_id' => 4604211,
                        'student_first_name' => 'Lauryn',
                        'student_last_name' => 'Perkins',
                        'is_attended' => 0
                    ]
                ]]
            ]
        ]);
    }

}
