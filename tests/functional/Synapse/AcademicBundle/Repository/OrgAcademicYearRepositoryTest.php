<?php
class OrgAcademicYearRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var \Synapse\AcademicBundle\Repository\OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;



    public function testGetCurrentOrPreviousAcademicYearUsingCurrentDate()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicYear');
        });
        $this->specify("Verify the functionality of the method getCurrentOrPreviousAcademicYearUsingCurrentDate", function ($currentDate, $orgId, $expectedResult) {
            $results = $this->orgAcademicYearRepository->getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDate, $orgId);
            verify($results)->equals($expectedResult);
            }
        , [
                "examples" => [
                    [
                       '2017-08-04 00:00:01', 9,
                        [0 =>['org_academic_year_id' => 199,
                            'year_id' => '201617',
                            'start_date' => '2016-08-08',
                            'end_date' => '2017-08-01',
                            'year_name' => '201617']]
                    ],
                    [
                        '2014-05-12 00:00:01', 9,
                        []
                    ],
                    [
                        '2015-08-09 00:00:01', 9,
                        [0 =>['org_academic_year_id' => 165,
                            'year_id' => '201415',
                            'start_date' => '2014-05-13',
                            'end_date' => '2015-04-15',
                            'year_name' => '201415']]
                    ],
                ]
            ]);
    }


}