<?php
use Synapse\ReportsBundle\Job\ActivityReportJob;

class ActivityReportJobTest extends \Codeception\TestCase\Test
{
    /**
     * @var \FunctionalTester
     */
    protected $tester;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    private $expectedResult = [
        'section_id' => 3,
        'title' => 'Activity Overview',
        'value' => null,
        'elements' => [
            0 => [
                'element_id' => 6,
                'title' => '# of activities logged',
                'value' => [
                    0 => [
                        'value' => 2,
                        'id' => 'interaction-contacts'
                    ],

                    1 => [
                        'value' => 1,
                        'id' => 'notes'
                    ],

                    2 => [
                        'value' => 1,
                        'id' => 'referrals'
                    ],

                    3 => [
                        'value' => 2,
                        'id' => 'contacts'
                    ],

                    4 => [
                        'value' => 26,
                        'id' => 'academic-updates'
                    ]

                ]

            ],

            1 => [
                'element_id' => 7,
                'title' => '# of students involved',
                'value' => [
                    0 => [
                        'value' => 2,
                        'id' => 'interaction-contacts'
                    ],

                    1 => [
                        'value' => 1,
                        'id' => 'notes'
                    ],

                    2 => [
                        'value' => 1,
                        'id' => 'referrals'
                    ],

                    3 => [
                        'value' => 2,
                        'id' => 'contacts'
                    ],

                    4 => [
                        'value' => 16,
                        'id' => 'academic-updates'
                    ]

                ]

            ],

            2 => [
                'element_id' => 8,
                'title' => '% of students',
                'value' => [
                    0 => [
                        'value' => 200,
                        'id' => 'interaction-contacts'
                    ],

                    1 => [
                        'value' => 100,
                        'id' => 'notes'
                    ],

                    2 => [
                        'value' => 100,
                        'id' => 'referrals'
                    ],

                    3 => [
                        'value' => 200,
                        'id' => 'contacts'
                    ],

                    4 => [
                        'value' => 1600,
                        'id' => 'academic-updates'
                    ]

                ]

            ],

            3 => [
                'element_id' => 9,
                'title' => '# of Faculty/Staff logged',
                'value' => [
                    0 => [
                        'value' => 2,
                        'id' => 'interaction-contacts'
                    ],

                    1 => [
                        'value' => 1,
                        'id' => 'notes'
                    ],

                    2 => [
                        'value' => 1,
                        'id' => 'referrals'
                    ],

                    3 => [
                        'value' => 2,
                        'id' => 'contacts'
                    ],

                    4 => [
                        'value' => 11,
                        'id' => 'academic-updates'
                    ]

                ]

            ],

            4 => [
                'element_id' => 10,
                'title' => '# of Faculty/Staff received',
                'value' => [
                    0 => [
                        'value' => 0,
                        'id' => 'interaction-contacts'
                    ],

                    1 => [
                        'value' => 0,
                        'id' => 'notes'
                    ],

                    2 => [
                        'value' => 1,
                        'id' => 'referrals'
                    ],

                    3 => [
                        'value' => 0,
                        'id' => 'contacts'
                    ],

                    4 => [
                        'value' => 0,
                        'id' => 'academic-updates'
                    ]

                ]

            ]

        ]

    ];


    use \Codeception\specify;

    protected function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get("repository_resolver");
    }


    // tests
    public function testBuildActivityOverviewSection()
    {
        $args = [];
        $args['reporting_on_student_ids'] = '415060,415065,384462,720283,276044,384473,384494,
        384494,384495,739920,384552,528388,256459,415089,415092,256485,256487,256499,256506,415098,
        384647,415100,619288,260671,415102,260695,260756,259997,259997,384736,260742,147171,619293,
        260762,415107,415107,260709,384755,619295,275954,384770,276037,276037,275831,276036,271826,271826,
        275997,275815,275815,275815,384809,147152,275797,275797,275953,275953,275794,276058,275908,
        415131,333703,275898,384833,275860,275860,275807,403148,403148,940992,275842,275842,276033,276026,
        276050,275937,403161,275822,403177,403194,403205,909649,403215,720220,403231,280795,403239,403241,
        619310,415115,403272,403278,940655,403308,403312,403312,403319,403327,403332,403339,403346,403348,
        403348,403351,403357,403357,403380,720331,720331,403410,403411,403428,415137,415137,415137,415137,
        403444,403446,403471,403472,619319,194379,403503,415142,719986,720357,720357,720357,720357,720357,
        720357,619321,403542,403551,415139,415139,403553,619324,403575,720000,403586,403590,403592,403592,1042637,1042637';

        $args['reporting_on_faculty_ids']= '84230,84230,84230,84230,84230,84230,84230,84230,
        86498,84230,86286,86286,86498,86391,86286,86286,162263,162240,86444,86473,229530,
        86286,86286,86286,112458,86473,86473,86286,112458,84230,86473,112483,112458,86473,
        86473,162240,162252,194400,86498,86391,86498,86473,86328,84230,86286,86320,162252,
        84230,84230,147159,112465,147163,86379,86335,86286,86286,84230,86286,194395,194395,
        86498,84230,86498,86286,86473,86473,84230,86379,86379,86372,84230,229529,86421,162263,
        84230,86394,86391,194400,84230,86473,86498,86473,86498,162240,86473,86498,86498,86444,86286,
        112458,96236,86498,86498,98189,86437,147172,86498,86286,84230,147184,84230,86498,94495,86317,
        84230,84230,162252,84230,86498,86498,84230,86286,84230,86498,112433,84230,96236,112483,86394,
        84230,96236,86317,86317,86343,86317,86372,86498,86498,86498,86394,195830,86444,86315,86421,86394,
        194376,194394,86394,84230,229530,86416,86498,96236,86286,84407,84230,86498,96236,86329,84230,86473,
        84230,84230,86498,112433,229530,215373,84230,84230,227338,147186,162252,86421,112458';

        $args['orgId'] = 155;

        $args['start_date'] = '2015-08-31';
        $args['end_date'] = '2016-04-30';

        $totalStudents = 1;
        $searchRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:EbiSearch');


        $activityReportJob = new ActivityReportJob();

        $activityOverviewSectionJSON = $activityReportJob->buildActivityOverviewSection($searchRepository, $args, $totalStudents);

        $this->assertEquals($this->expectedResult, $activityOverviewSectionJSON);



    }



}