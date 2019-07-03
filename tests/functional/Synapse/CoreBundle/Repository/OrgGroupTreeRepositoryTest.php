<?php

use Synapse\CoreBundle\Repository\OrgGroupTreeRepository;

class OrgGroupTreeRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;
    /**
     * @var \UnitTester
     */
    protected $tester;

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
     * @var \Synapse\CoreBundle\Repository\OrgGroupTreeRepository
     */
    private $orgGroupTreeRepository;


    /**
     *
     * This will test to see if given an group id
     * to bring back all children groups for the
     * group id. Includes self in function
     */
    public function testGetEachGeneration(){

        $this->beforeSpecify(function(){
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->orgGroupTreeRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupTree');
        });

        $this->specify("Test to get all of a group's generational information ", function($parentGroupId, $orgId, $expectedResults){
            $returnValues = $this->orgGroupTreeRepository->getEachGeneration($parentGroupId, $orgId);

            $this->assertEquals(count($returnValues), count($expectedResults));

            // make sure that the headers returned in the array equal
            // the expected returned array headers
            $returnValuesKeys = array_keys($returnValues[0]);
            $expectedResultsKeys = array_keys($expectedResults[0]);
            $this->assertEquals($returnValuesKeys, $expectedResultsKeys);

            // Seeing as this is not that challenging of to copy,
            // I am doing a direct assertEquals
            $this->assertEquals($expectedResults, $returnValues);

            // This will loop over each record given and
            // and see if the parentGroupId is within the
            // list, if it is not, the assert Below will
            // fail.
            $parentGroupIdLocation = 0;
            $parentGroupIdCount = 0;
            foreach($returnValues as $returnValue){
                if($returnValue['id'] == $parentGroupId){
                     $parentGroupIdLocation = $parentGroupIdCount;
                }
                $parentGroupIdCount ++;
            }
            $this->assertContains($parentGroupId, $returnValues[$parentGroupIdLocation]);

        }, ["examples"=>
            [//examples array
                //example 1
                [
                    "370628",
                    "203",
                    [
                     ['id'=>'370628'],
                     ['id'=>'370629'],
                     ['id'=>'370630'],
                     ['id'=>'370631'],
                     ['id'=>'370632'],
                     ['id'=>'370633'],
                     ['id'=>'370634'],
                     ['id'=>'370635'],
                     ['id'=>'370636'],
                     ['id'=>'370637'],
                     ['id'=>'370638'],
                     ['id'=>'370639'],
                     ['id'=>'370640'],
                     ['id'=>'370641'],
                     ['id'=>'370642'],
                     ['id'=>'370643'],
                     ['id'=>'370644'],
                     ['id'=>'370645'],
                     ['id'=>'370646'],
                     ['id'=>'370647'],
                     ['id'=>'370648'],
                     ['id'=>'370649'],
                     ['id'=>'370650'],
                     ['id'=>'370651'],
                     ['id'=>'370652'],
                     ['id'=>'370653'],
                    ]
                ],
                [
                    // All students group
                    "369206",
                    "203",
                    [
                        ['id'=>'369206'] ,
                    ]

                ]
            ]
        ]);
    }

    /**
     *
     * This will see if is ancestor
     * function will return true or
     * false, depending on whether or not
     * the two groups given are related
     */
    public function testIsAncestor()
    {

        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->orgGroupTreeRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupTree');
        });

        $this->specify("Test to see if a two group ids are related with one being the parent of the other ", function ($descendantId, $ancestorId, $expectedResults) {
            $returnValues = $this->orgGroupTreeRepository->isAncestor($descendantId, $ancestorId);
            $this->assertEquals($expectedResults, $returnValues);

        }, ["examples" =>
            [//examples array
                //example 1
                [
                    // These are group ids that are in 203
                    370639,
                    370628,
                    true
                ],
                [
                    // This group is within 203
                    370639,
                    // This group is within organization 2
                    42,
                    false
                ]
            ]
        ]);
    }

    /**
     *
     * This will test the query that needs to be ran for
     * the Download Existing Student Group File.
     */
    public function testGetAllStudentGroupCombinationsWithAncestorForAnOrganization()
    {

        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->orgGroupTreeRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupTree');
        });

        $this->specify("Test to return A list of the all students top level group, child group the student is in for the organization", function ($organizationId, $countOfExpectedResults, $expectedResultsSampling) {
            $returnValues = $this->orgGroupTreeRepository->getAllStudentGroupCombinationsWithAncestorForAnOrganization($organizationId);

            $this->assertEquals(count($returnValues), $countOfExpectedResults);


            // make sure that the headers returned in the array equal
            // the expected returned array headers
            $returnValuesKeys = array_keys($returnValues[0]);
            $expectedResultsKeys = array_keys($expectedResultsSampling[0]);
            $this->assertEquals($returnValuesKeys, $expectedResultsKeys);

            // I grabbed a random sample of students for the check
            // I am going to make sure that the random sample is within
            // the results returned.
            foreach($expectedResultsSampling as $expectedResultRow){
                $this->assertContains($expectedResultRow, $returnValues);
            }

        }, ["examples" =>
            [//examples array
                //example 1
                [
                    // organization
                    203,
                    // number of results
                    1190,
                    [
                        ['external_id'=>'4878822', 'firstname'=>'Fernando'  , 'lastname'=>'Harding',    'username'=>'MapworksBetaUser04878822@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'XC'  ],
                        ['external_id'=>'4878886', 'firstname'=>'Walker'    , 'lastname'=>'Hanna',      'username'=>'MapworksBetaUser04878886@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'XC'  ],
                        ['external_id'=>'4878950', 'firstname'=>'Phillip'   , 'lastname'=>'Galloway',   'username'=>'MapworksBetaUser04878950@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'XC'  ],
                        ['external_id'=>'4879098', 'firstname'=>'Arlo'      , 'lastname'=>'Barnes',     'username'=>'MapworksBetaUser04879098@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'XC'  ],
                        ['external_id'=>'4878863', 'firstname'=>'Anderson'  , 'lastname'=>'Bernard',    'username'=>'MapworksBetaUser04878863@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'SB'  ],
                        ['external_id'=>'4879154', 'firstname'=>'Alvin'     , 'lastname'=>'Hicks',      'username'=>'MapworksBetaUser04879154@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'SB'  ],
                        ['external_id'=>'4879177', 'firstname'=>'Zackary'   , 'lastname'=>'Fernandez',  'username'=>'MapworksBetaUser04879177@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'SB'  ],
                        ['external_id'=>'4878854', 'firstname'=>'Lane'      , 'lastname'=>'Best',       'username'=>'MapworksBetaUser04878854@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'ITF' ],
                        ['external_id'=>'4879738', 'firstname'=>'Daisy'     , 'lastname'=>'Gaines',     'username'=>'MapworksBetaUser04879738@mailinator.com',  'header_name'=>'NCAA'   ,'person_in'=>'FB'  ],
                        ['external_id'=>'4879765', 'firstname'=>'Kate'      , 'lastname'=>'Livingston', 'username'=>'MapworksBetaUser04879765@mailinator.com',  'header_name'=> 'OFF'   ,'person_in'=>'OFF' ],
                        ['external_id'=>'4879774', 'firstname'=>'Arya'      , 'lastname'=>'Howe',       'username'=>'MapworksBetaUser04879774@mailinator.com',  'header_name'=> 'OFF'   ,'person_in'=>'OFF' ],
                        ['external_id'=>'4879779', 'firstname'=>'Tessa'     , 'lastname'=>'Reilly',     'username'=>'MapworksBetaUser04879779@mailinator.com',  'header_name'=> 'OFF'   ,'person_in'=>'OFF' ],
                        ['external_id'=>'4879783', 'firstname'=>'Daniela'   , 'lastname'=>'Noble',      'username'=>'MapworksBetaUser04879783@mailinator.com',  'header_name'=> 'OFF'   ,'person_in'=>'OFF' ],
                        ['external_id'=>'4879768', 'firstname'=>'Genevieve'	, 'lastname'=>'Bradshaw',   'username'=>'MapworksBetaUser04879768@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'SH1' ],
                        ['external_id'=>'4879800', 'firstname'=>'Lexi'  	, 'lastname'=>'Walls',      'username'=>'MapworksBetaUser04879800@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'SH1' ],
                        ['external_id'=>'4879809', 'firstname'=>'Noelle'	, 'lastname'=>'Mcmillan',   'username'=>'MapworksBetaUser04879809@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'SH1' ],
                        ['external_id'=>'4879832', 'firstname'=>'Chelsea'	, 'lastname'=>'Arroyo',     'username'=>'MapworksBetaUser04879832@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'SH1' ],
                        ['external_id'=>'4879856', 'firstname'=>'Phoebe'	, 'lastname'=>'Friedman',   'username'=>'MapworksBetaUser04879856@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'SH1' ],
                        ['external_id'=>'4879879', 'firstname'=>'Raegan'	, 'lastname'=>'Estes',      'username'=>'MapworksBetaUser04879879@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'SH1' ],
                        ['external_id'=>'4878809', 'firstname'=>'Seth'  	, 'lastname'=>'Mcmillan',   'username'=>'MapworksBetaUser04878809@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'LH3' ],
                        ['external_id'=>'4878841', 'firstname'=>'Tyson' 	, 'lastname'=>'Dodson',     'username'=>'MapworksBetaUser04878841@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'LH3' ],
                        ['external_id'=>'4878850', 'firstname'=>'Hector'	, 'lastname'=>'Benton',     'username'=>'MapworksBetaUser04878850@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'LH3' ],
                        ['external_id'=>'4878873', 'firstname'=>'Dante' 	, 'lastname'=>'Yu',         'username'=>'MapworksBetaUser04878873@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'LH3' ],
                        ['external_id'=>'4878882', 'firstname'=>'Shawn'	    , 'lastname'=>'Stuart',     'username'=>'MapworksBetaUser04878882@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'LH3' ],
                        ['external_id'=>'4878905', 'firstname'=>'Barrett'	, 'lastname'=>'Maddox',     'username'=>'MapworksBetaUser04878905@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'LH3' ],
                        ['external_id'=>'4878914', 'firstname'=>'Phoenix'	, 'lastname'=>'Dickson',    'username'=>'MapworksBetaUser04878914@mailinator.com',  'header_name'=>	 'RL'   ,'person_in'=>'LH3' ],

                    ]

                ]
            ]
        ]);
    }




}
