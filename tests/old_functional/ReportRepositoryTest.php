<?php

use Synapse\ReportsBundle\Repository\ReportsRepository;

class ReportRepositoryTest extends \Codeception\TestCase\Test
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


    public function testGetRetentionData()
    {

        $this->beforeSpecify(function(){
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->reportsRepo = $this->repositoryResolver->getRepository('SynapseReportsBundle:Reports');
        });

        $this->specify("Test get retention data function in report repository", function($filteredStudents, $organizationId, $riskStartDate, $riskEndDate, $orgYearId, $retentionType, $expectedResults){
            $returnValues = $this->reportsRepo->getRetentionData($filteredStudents, $organizationId, $riskStartDate, $riskEndDate, $orgYearId, $retentionType);


            $this->assertEquals($expectedResults, $returnValues);

        }, ["examples"=>
            [//examples array
                //example 1
                [
                    "4673760, 4713727, 4714198, 4763575",
                    "62",
                    "2015-05-05 00:00:00",
                    "2016-02-08 00:00:00",
                    "48",
                    "PreYearCredTotal",
                    [
                        0=>
                            [
                                "risk_level_text"=>"gray",
                                "risk_level"=>null,
                                "numerator_count"=>"0",
                                "denominator_count"=>"1"
                            ]
                        ,
                        1=>
                            [
                                "risk_level_text"=>"green",
                                "risk_level"=>"4",
                                "numerator_count"=>"3",
                                "denominator_count"=>"3"
                            ]
                    ]
                ]
                ,
                [
                    "361341,361447,734967,734995,735010,735020,735035,735050,735065,735067,735091,735093,735120,735141,735146,735160,735171,735207,735218,735226,735227,735230,735248,735257,735269,735295,735298,735327,735341,735354,735367,735398,735410,735411,735455,735457,735461,735466,735472,735491,735495,735498,735527,735530,735538,735564,735568,735583,735584,735593,735596,735616,735619,735651,735655,735662,735672,906436,906455,906456,973376,973381,973389,973393,973397,973401,973410,973414,973424,973439,973443,973447,973451,973452,973453,973454,973455,973458,973459,973467,973469,973470,973473,973474,973475,973478,973479,973483,973484,973485,973486,973487,973488,973489,973493,973494,973498,973499,973500,973501,973502,973503,973504,973506,973509,973512,973513,973515,973517,973520,973521,973522,973523,973526,973527,973528,973529,973532,973533,973534,973535,973537,973538,973539,973540,973541,973542,973543,973544,973545,973546,973547,973549,973550,973551,973556,973557,973561,973562,973564,973565,973567,973570,973571,973574,973575,145573,189631,189632,189633,189635,189636,189639,189641,189642,189645,189648,189649,189650,189651,189652,189653,189657,189658,189659,189661,189663,189681,193034,260341,260404,260469,260492,260504,260626,271847,273553,273593,273748,273752,273834,274062,274069,274072,274082,274107,274128,274132,274142,274181,274186,274190,274197,274207,274211,274212,274213,274219,274223,274224,274225,274231,274267,274269,274283,274306,274309,274311,274329,274337,274343,274347,274366,274385,274387,274411,274422,274432,274433,274438,274441,274444,274467,274469,274488,274507,274509,275137,275156,275173,275183,275212,275213,275792,276645,286488,286491,286513,286518,286525,286526,286539,286541,333422,333558,361091,361092,361093,361099,361103,361106,361119,361120,361121,361122,361124,361126,361127,361130,361137,361140,361142,361143,361146,361150,361151,361157,361160,361163,361166,361167,361168,361169,361170,361171,361173,361174,361175,361177,361181,361183,361184,361185,361189,361190,361192,361193,361195,361196,361197,361198,361199",
                    "180",
                    "2015-05-05 00:00:00",
                    "2016-02-08 00:00:00",
                    "35",
                    "PellEligible",
                    [
                        0=>
                            [
                                "risk_level_text"=>"gray",
                                "risk_level"=>null,
                                "numerator_count"=>"52",
                                "denominator_count"=>"146"
                            ],
                        1=>
                            [
                                "risk_level_text"=>"green",
                                "risk_level"=>"4",
                                "numerator_count"=>"47",
                                "denominator_count"=>"146"
                            ]
                    ]
                ]

            ]
        ]);
    }

    public function testGetOverallCount()
    {
        $this->beforeSpecify(function(){
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->reportsRepo = $this->repositoryResolver->getRepository('SynapseReportsBundle:Reports');
        });

        $this->specify("test get overall count function in reports repository", function($orgYearId, $retentionType, $students, $expectedResult){

            $results = $this->reportsRepo->getOverallCount($orgYearId, $retentionType, $students);
            $this->assertEquals($expectedResult, $results);

        }, ["examples"=>
            [
                [
                    "48",
                    "PellEligible",
                    array(4673760, 4713727, 4714198, 4763575),
                    "0"
                ]
                ,
                [
                    "48",
                    "PreYearCredTotal",
                    array(4673760, 4713727, 4714198, 4763575),
                    "4"
                ]
                ,
                [
                    "57",
                    "PreYearCumGPA",
                    array(4761272,4761807,4763452,516773,4758807,1145246,4831213,1201657,1196558,4760308,4831585),
                    "11"
                ]
            ]
        ]);
    }

    public function testGetColorCodeNumber()
    {
        $this->beforeSpecify(function(){
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->reportsRepo = $this->repositoryResolver->getRepository('SynapseReportsBundle:Reports');
        });

        $this->specify("test get color code number for reports repository", function($filteredStudents, $riskStartDate, $riskEndDate, $expectedResult){
            $results = $this->reportsRepo->getColorCodeNumber($filteredStudents, $riskStartDate, $riskEndDate);
            $this->assertEquals($expectedResult, $results);
        },["examples"=>
            [
                [
                    array(4761272,4761807,4763452,516773,4758807,1145246,4831213,1201657,1196558,4760308,4831585),
                    "2015-05-05 00:00:00",
                    "2016-02-08 00:00:00",
                    [
                        0 =>
                            [
                                "risk_level_text"=>"gray",
                                "cnt"=>"3"
                            ]
                        ,
                        1 =>
                            [
                                "risk_level_text"=>"green",
                                "cnt"=>"3"
                            ]
                        ,
                        2 =>
                            [
                                "risk_level_text"=>"red",
                                "cnt"=>"2"
                            ]
                        ,
                        3 =>
                            [
                                "risk_level_text"=>"red2",
                                "cnt"=>"2"
                            ]
                        ,
                        4 =>
                            [
                                "risk_level_text"=>"yellow",
                                "cnt"=>"1"
                            ]
                    ]
                ]
                ,
                [
                    array(4673760, 4713727, 4714198, 4763575),
                    "2015-05-05 00:00:00",
                    "2016-02-08 00:00:00",
                    [
                        0 =>
                            [
                                "risk_level_text"=>"gray",
                                "cnt"=>"1"
                            ]
                        ,
                        1 =>
                            [
                                "risk_level_text"=>"green",
                                "cnt"=>"3"
                            ]
                    ]
                ]
            ]
        ]);
    }






}