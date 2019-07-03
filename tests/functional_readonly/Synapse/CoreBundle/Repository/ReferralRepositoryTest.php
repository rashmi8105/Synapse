<?php

/**
 * Class ReferralRepositoryTest
 */

use Codeception\TestCase\Test;

class ReferralRepositoryTest extends Test
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
     * @var \Synapse\CoreBundle\Repository\ReferralRepository
     */
    private $referralRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->referralRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Referrals');
    }

    public function testGetRecievedReferralDetails()
    {
        $this->markTestSkipped("Referral data not available for the current academic year 201617(2016-07-13 - 2017-07-28)");
        $this->specify("Verify the functionality of the method getRecievedReferralDetails", function ($personId, $orgId, $status, $startDate, $endDate, $expectedResultsSize, $expectedOutput) {
            $results = $this->referralRepository->getRecievedReferralDetails($personId, $orgId, $status, '', '', false, false, '', '', $startDate, $endDate, '', false);

            verify(count($results))->equals($expectedResultsSize);

            for ($i = 0; $i < count($expectedOutput); $i++) {
                verify($results[$i]['referral_id'])->notEmpty();
                verify($results[$i]['referral_id'])->equals($expectedOutput[$i][0]);
                verify($results[$i]['student_id'])->notEmpty();
                verify($results[$i]['student_id'])->equals($expectedOutput[$i][1]);
                verify($results[$i]['student_first_name'])->notEmpty();
                verify($results[$i]['student_first_name'])->equals($expectedOutput[$i][2]);
                verify($results[$i]['student_last_name'])->notEmpty();
                verify($results[$i]['student_last_name'])->equals($expectedOutput[$i][3]);
                verify($results[$i]['student_email'])->notEmpty();
                verify($results[$i]['student_email'])->equals($expectedOutput[$i][4]);
                verify($results[$i]['referral_date'])->notEmpty();
                verify($results[$i]['referral_date'])->equals($expectedOutput[$i][5]);
            }


        }, ["examples" =>
            [
                [4878750, 203, 'O', '2015-01-01', '2016-12-30', 25,
                    [
                        ['93761', '4878833', 'Troy', 'Valentine', 'MapworksTestingUser01552333@mailinator.com', '2016-02-04 15:22:44'],
                        ['93762', '4878835', 'Trevor', 'Gould', 'MapworksTestingUser01552335@mailinator.com', '2016-02-04 15:22:44'],
                        ['93763', '4878837', 'Kameron', 'Fry', 'MapworksTestingUser01552337@mailinator.com', '2016-02-04 15:22:44'],
                        ['93764', '4878972', 'Gannon', 'Braun', 'MapworksTestingUser01552472@mailinator.com', '2016-02-04 15:22:44']
                    ]
                ]

            ]
        ]);
    }

    public function testGetAllReferralDetails()
    {
        $this->markTestSkipped("Referral data not available for the current academic year 201617(2016-07-13 - 2017-07-28)");
        $this->specify("test get all referral details", function ($userId, $organizationId, $expectedResultsSize, $expectedIds) {

            $results = $this->referralRepository->getAllReferralDetails($userId, $organizationId, 'all', '', '', false, false, '', '', null, null, '');

            verify(count($results))->equals($expectedResultsSize);
            if (count($results) > 0) {
                for ($i = 0; $i < count($expectedIds); $i++) {
                    verify($results[$i])->hasKey('student_id');
                    verify($results[$i]['student_id'])->notEmpty();
                    if (count($results) > 0) {
                        $studentIds = array_column($results, 'student_id');
                        verify($studentIds)->contains($expectedIds[$i]);
                    }
                }
            }
        }, ["examples" =>
            [
                [4878750, 203, 25, [4879829, 4879848]],
                [4883148, 203, 0, [4879829, 4879848]]
            ]
        ]);
    }

    public function testGetReceiveReferralDetails()
    {
        $this->markTestSkipped("Referral data not available for the current academic year 201617(2016-07-13 - 2017-07-28)");
        $this->specify("test get receive referral details", function ($userId, $organizationId, $expectedResultsSize, $expectedIds) {

            $results = $this->referralRepository->getRecievedReferralDetails($userId, $organizationId, 'O', '', '', false, false, '', '', null, null, '');
            verify(count($results))->equals($expectedResultsSize);

            for ($i = 0; $i < count($expectedIds); $i++) {
                verify($results[$i])->hasKey('student_id');
                verify($results[$i]['student_id'])->notEmpty();
                if (count($results) > 0) {
                    $studentIds = array_column($results, 'student_id');
                    verify($studentIds)->contains($expectedIds[$i]);
                }
            }
        }, ["examples" =>
            [
                [4878750, 203, 25, [4879829, 4879848]],
                ['4878810', 203, 0, []],
                ['4878751', 203, 0, []]
            ]
        ]);
    }

}