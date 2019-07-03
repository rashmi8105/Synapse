<?php

/**
 * Created by PhpStorm.
 * User: Seemanti Basu
 * Date: 5/5/16
 *
 */
class ReportsRepositoryTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\ReportsBundle\Repository\ReportsRepository
     */
    private $reportsRepository;

    public function testGetCampusActvityDetails()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->reportsRepository = $this->repositoryResolver->getRepository('SynapseReportsBundle:Reports');
        });

        

        $this->specify("Verify the functionality of the method getCampusActvityDetails", function($orgId, $yearStartDate, $yearEndDate, $type, $accessPersonId,$loggedInUserId, $sharingAccess, $expectedResultsSize, $expectedIds){

            $results = $this->reportsRepository->getCampusActvityDetails($orgId, $yearStartDate, $yearEndDate, $type, $accessPersonId,$loggedInUserId,$sharingAccess);
            verify(count($results))->equals($expectedResultsSize);

            for($i = 0; $i < count($expectedIds); $i++){
                verify($results[$i]['activity_id'])->notEmpty();
                verify($results[$i]['activity_id'])->equals($expectedIds[$i]);
            }
        }, ["examples"=>
            [
                [203,'2015-09-09','2016-09-09','referrals',4878750,4878750,$this->getSharingAccess(),25,[93780,93781,93782,93783,93784,93785,93774,93775,93776,93777,93778,93779,93767,93768,93769,93770,93771,93772,93773,93761,93762,93763,93764,93765,93766]],
                [203,'2013-01-01','2016-05-05','contact',4878750,4878750,$this->getSharingAccess(),0,NULL],
                [203,'2013-01-01','2016-05-05','note',4878750,4878750,$this->getSharingAccess(),0,NULL],
                [203,'2013-01-01','2016-05-05','appointment',4878750,4878750,$this->getSharingAccess(),3,[24998,5649,5650]],
                [203,'2013-01-01','2016-05-05','email',4878750,4878750,$this->getSharingAccess(),0,NULL]
            ]
        ]);
    }

    private function getSharingAccess()
    {
       $shrAccess =  Array( 'Referrals' => Array
                                (
                                    'public_view' => 1,
                                    'team_view' => 1
                                ),
                            'Referrals Reason Routed' => Array
                                (
                                    'public_view' => 1,
                                    'team_view' => 1
                                ),

                            'Notes' => Array
                                (
                                    'public_view' => 1,
                                    'team_view' => 1
                                ),

                            'Log Contacts' => Array
                                (
                                    'public_view' => 1,
                                    'team_view' => 1
                                ),

                            'Booking' => Array
                                (
                                    'public_view' => 1,
                                    'team_view' => 1
                                ),

                            'Email' => Array
                                (
                                    'public_view' => 1,
                                    'team_view' => 1
                                ),

                            'ReferralsReasonRouted' => Array
                                (
                                    'public_view' => 0,
                                    'team_view' => 0
                                 )
                        );
       return $shrAccess;
    }

}