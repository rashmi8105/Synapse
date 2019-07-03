<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Repository\ReportsRunningStatusRepository;

class ReportsRunningStatusRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var ReportsRunningStatusRepository
     */
    private $reportsRunningStatusRepository;

    /**
     * @var int
     */
    private $organizationId=189;

    /**
     * @var int
     */
    private $personId=256048;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->reportsRunningStatusRepository = $this->repositoryResolver->getRepository(ReportsRunningStatusRepository::REPOSITORY_KEY);
    }

    public function testGetLastRunDateForMyReport()
    {
        $this->specify("Verify the functionality of the method getLastRunDateForMyReport", function ($reportId, $expectedResult) {
            $result = $this->reportsRunningStatusRepository->getLastRunDateForMyReport($this->organizationId, $reportId, $this->personId);
            $this->assertEquals($expectedResult, $result);
        }, [
            "examples" => [
                // examples for the report where there is last run date exists
                [
                    7,
                    ['modified_at' => '2015-12-07 16:59:34']
                ],
                // examples for the report where there is no last run date exists
                [
                    10, // report_id,
                    false // expected result
                ]
            ]
        ]);

    }

    public function testGetFilteredStudents()
    {

        $this->specify("Verify the functionality of the method getFilteredStudents", function ($saveSearch, $organizationId, $expectedResults) {
            try {
                $results = $this->reportsRunningStatusRepository->getFilteredStudents($saveSearch, $organizationId);
                verify($results)->equals($expectedResults);
            }catch(Exception $e){
                verify($e->getMessage())->equals($expectedResults);
            }
        }, [
            "examples" =>
                [
//                    Example 1: Valid org, valid search
                    [' EXISTS (

                        SELECT DISTINCT
                            merged.student_id
                        FROM
                            (
                                SELECT
                                    ofspm.student_id,
                                    ofspm.permissionset_id
                                FROM
                                    org_faculty_student_permission_map ofspm
                                WHERE
                                    ofspm.org_id = 16
                                    AND ofspm.faculty_id = 10009
                            ) AS merged
                                INNER JOIN
                            org_permissionset OPS
                                    ON OPS.id = merged.permissionset_id
                                    AND OPS.deleted_at IS NULL
                     WHERE student_id = p.id
                    )', 16, [
                            ['person_id' => 1048566],
                            ['person_id' => 1048586],
                            ['person_id' => 1048622],
                            ['person_id' => 1048649],
                            ['person_id' => 1048799],
                            ['person_id' => 1048872],
                            ['person_id' => 1049023],
                            ['person_id' => 1049052],
                            ['person_id' => 1049101],
                            ['person_id' => 1049204],
                            ['person_id' => 1049275],
                            ['person_id' => 1049302],
                            ['person_id' => 1049376],
                            ['person_id' => 1049910],
                            ['person_id' => 1049942],
                            ['person_id' => 1049971],
                            ['person_id' => 1050214],
                            ['person_id' => 1050282],
                            ['person_id' => 1050577],
                            ['person_id' => 1050705],
                            ['person_id' => 1050763],
                            ['person_id' => 1050972],
                            ['person_id' => 1051207],
                            ['person_id' => 1051249],
                            ['person_id' => 1051380],
                            ['person_id' => 1051432],
                            ['person_id' => 1051542],
                            ['person_id' => 1051545],
                            ['person_id' => 1051635],
                            ['person_id' => 1051649],
                            ['person_id' => 1051670],
                            ['person_id' => 1051683],
                            ['person_id' => 1051753],
                            ['person_id' => 1051825],
                            ['person_id' => 1051826],
                            ['person_id' => 1051854],
                            ['person_id' => 1051901],
                            ['person_id' => 4746624],
                            ['person_id' => 4746630],
                            ['person_id' => 4746633],
                            ['person_id' => 4746651],
                            ['person_id' => 4746681],
                            ['person_id' => 4746682],
                            ['person_id' => 4746696],
                            ['person_id' => 4746710],
                            ['person_id' => 4746716],
                            ['person_id' => 4746738],
                            ['person_id' => 4746782],
                            ['person_id' => 4746808],
                            ['person_id' => 4746833],
                            ['person_id' => 4746844],
                            ['person_id' => 4746860],
                            ['person_id' => 4746864],
                            ['person_id' => 4746900],
                            ['person_id' => 4746998],
                            ['person_id' => 4747040],
                            ['person_id' => 4747043],
                            ['person_id' => 4747050],
                            ['person_id' => 4747062],
                            ['person_id' => 4747070],
                            ['person_id' => 4747088],
                            ['person_id' => 4747128],
                            ['person_id' => 4747161],
                            ['person_id' => 4747194],
                            ['person_id' => 4747210],
                            ['person_id' => 4747233],
                            ['person_id' => 4747258],
                            ['person_id' => 4747294],
                            ['person_id' => 4747297],
                            ['person_id' => 4747301],
                            ['person_id' => 4747311],
                            ['person_id' => 4747323],
                            ['person_id' => 4747346],
                            ['person_id' => 4747353],
                            ['person_id' => 4747369],
                            ['person_id' => 4747371],
                            ['person_id' => 4747382],
                            ['person_id' => 4747388],
                            ['person_id' => 4747393],
                            ['person_id' => 4747403],
                            ['person_id' => 4747411],
                            ['person_id' => 4747418],
                            ['person_id' => 4747427],
                            ['person_id' => 4747430],
                            ['person_id' => 4747470],
                            ['person_id' => 4747473],
                            ['person_id' => 4747476],
                            ['person_id' => 4747493],
                            ['person_id' => 4747499],
                            ['person_id' => 4747517],
                            ['person_id' => 4747555],
                            ['person_id' => 4747567],
                            ['person_id' => 4747573],
                            ['person_id' => 4747610],
                            ['person_id' => 4747613],
                            ['person_id' => 4747616],
                            ['person_id' => 4747621],
                            ['person_id' => 4747627],
                            ['person_id' => 4747637],
                            ['person_id' => 4747656],
                            ['person_id' => 4747666],
                            ['person_id' => 4747671],
                            ['person_id' => 4747701],
                            ['person_id' => 4747725],
                            ['person_id' => 4747791],
                            ['person_id' => 4747806],
                            ['person_id' => 4747827],
                            ['person_id' => 4747849],
                            ['person_id' => 4747852],
                            ['person_id' => 4747880],
                            ['person_id' => 4747897],
                            ['person_id' => 4747906],
                            ['person_id' => 4747938],
                            ['person_id' => 4747945],
                            ['person_id' => 4747968],
                            ['person_id' => 4747982],
                            ['person_id' => 4747993],
                            ['person_id' => 4747995],
                            ['person_id' => 4748036],
                            ['person_id' => 4748044],
                            ['person_id' => 4748054],
                            ['person_id' => 4748056],
                            ['person_id' => 4748075],
                            ['person_id' => 4748082],
                            ['person_id' => 4748086],
                            ['person_id' => 4748110],
                            ['person_id' => 4748114],
                            ['person_id' => 4748197],
                            ['person_id' => 4748215],
                            ['person_id' => 4748229],
                            ['person_id' => 4748239],
                            ['person_id' => 4748250],
                            ['person_id' => 4748257],
                            ['person_id' => 4748260],
                            ['person_id' => 4748267],
                            ['person_id' => 4748276],
                            ['person_id' => 4748292],
                            ['person_id' => 4748299],
                            ['person_id' => 4748312],
                            ['person_id' => 4748313],
                            ['person_id' => 4748330],
                            ['person_id' => 4748334],
                            ['person_id' => 4748346],
                            ['person_id' => 4748373],
                            ['person_id' => 4748399],
                            ['person_id' => 4748420],
                            ['person_id' => 4748434],
                            ['person_id' => 4748446],
                            ['person_id' => 4748480],
                            ['person_id' => 4748526],
                            ['person_id' => 4748537],
                            ['person_id' => 4748564],
                            ['person_id' => 4748570],
                            ['person_id' => 4748595],
                            ['person_id' => 4748628],
                            ['person_id' => 4748629],
                            ['person_id' => 4748639],
                            ['person_id' => 4748687],
                            ['person_id' => 4748724],
                            ['person_id' => 4748725],
                            ['person_id' => 4748756],
                            ['person_id' => 4748764],
                            ['person_id' => 4748770],
                            ['person_id' => 4748800],
                            ['person_id' => 4748823],
                            ['person_id' => 4748825],
                            ['person_id' => 4748832],
                            ['person_id' => 4748845],
                            ['person_id' => 4748847],
                            ['person_id' => 4748848],
                            ['person_id' => 4748853],
                            ['person_id' => 4748859],
                            ['person_id' => 4748863],
                            ['person_id' => 4748868],
                            ['person_id' => 4748901],
                            ['person_id' => 4748944],
                            ['person_id' => 4748958],
                            ['person_id' => 4748989],
                            ['person_id' => 4748999],
                            ['person_id' => 4749000],
                            ['person_id' => 4749006],
                            ['person_id' => 4749053],
                            ['person_id' => 4749064],
                            ['person_id' => 4749085],
                            ['person_id' => 4749095],
                            ['person_id' => 4749106],
                            ['person_id' => 4749115],
                            ['person_id' => 4749128],
                            ['person_id' => 4749131],
                            ['person_id' => 4749133],
                            ['person_id' => 4749159],
                            ['person_id' => 4749242],
                            ['person_id' => 4749254],
                            ['person_id' => 4749255],
                            ['person_id' => 4749260],
                            ['person_id' => 4749261],
                            ['person_id' => 4749284],
                            ['person_id' => 4749288],
                            ['person_id' => 4749294],
                            ['person_id' => 4749303],
                            ['person_id' => 4749326],
                            ['person_id' => 4749331],
                            ['person_id' => 4749345],
                            ['person_id' => 4749361],
                            ['person_id' => 4749369],
                            ['person_id' => 4749373],
                            ['person_id' => 4749380],
                            ['person_id' => 4749428],
                            ['person_id' => 4749443],
                            ['person_id' => 4749498],
                            ['person_id' => 4749499],
                            ['person_id' => 4749520],
                            ['person_id' => 4749531],
                            ['person_id' => 4749535],
                            ['person_id' => 4749536],
                            ['person_id' => 4749542],
                            ['person_id' => 4749554],
                            ['person_id' => 4749574],
                            ['person_id' => 4749585],
                            ['person_id' => 4749614],
                            ['person_id' => 4749640],
                            ['person_id' => 4749652],
                            ['person_id' => 4749729],
                            ['person_id' => 4749774],
                            ['person_id' => 4749790],
                            ['person_id' => 4749799],
                            ['person_id' => 4749820],
                            ['person_id' => 4749836],
                            ['person_id' => 4749855],
                            ['person_id' => 4749860],
                            ['person_id' => 4749878],
                            ['person_id' => 4749890],
                            ['person_id' => 4749898],
                            ['person_id' => 4749961],
                            ['person_id' => 4749970],
                            ['person_id' => 4749973],
                            ['person_id' => 4749976],
                            ['person_id' => 4750015],
                            ['person_id' => 4750062],
                            ['person_id' => 4750086],
                            ['person_id' => 4750107],
                            ['person_id' => 4750108],
                            ['person_id' => 4750109],
                            ['person_id' => 4750138],
                            ['person_id' => 4750148],
                            ['person_id' => 4750282],
                            ['person_id' => 4750296],
                            ['person_id' => 4750423],
                            ['person_id' => 4750441],
                            ['person_id' => 4750510],
                            ['person_id' => 4750560],
                            ['person_id' => 4750563],
                            ['person_id' => 4750572],
                            ['person_id' => 4750590],
                            ['person_id' => 4750594],
                            ['person_id' => 4750598],
                            ['person_id' => 4750605],
                            ['person_id' => 4750609],
                            ['person_id' => 4750627],
                            ['person_id' => 4750651],
                            ['person_id' => 4750656],
                            ['person_id' => 4750702],
                            ['person_id' => 4750704],
                            ['person_id' => 4750753],
                            ['person_id' => 4750769],
                            ['person_id' => 4750823],
                            ['person_id' => 4750849],
                            ['person_id' => 4750873],
                            ['person_id' => 4750886],
                            ['person_id' => 4750989],
                            ['person_id' => 4751012],
                            ['person_id' => 4751054]
                        ]
                    ],

//                  Example 2:  Invalid org, valid search
                    [' EXISTS (

                        SELECT DISTINCT
                            merged.student_id
                        FROM
                            (
                                SELECT
                                    ofspm.student_id,
                                    ofspm.permissionset_id
                                FROM
                                    org_faculty_student_permission_map ofspm
                                WHERE
                                    ofspm.org_id = 1600
                                    AND ofspm.faculty_id = 10009
                            ) AS merged
                                INNER JOIN
                            org_permissionset OPS
                                    ON OPS.id = merged.permissionset_id
                                    AND OPS.deleted_at IS NULL
                        WHERE student_id = p.id
                        )', 1600, []
                    ],

//                   Example 3: Valid org, invalid search
                    [' EXISTS (

                        SELECT DISTINCT
                            merged.student_id
                        FROM
                            (
                                SELECT
                                    ofspm.student_id,
                                    ofspm.permissionset_id
                                FROM
                                    org_faculty_student_permission_map ofspm
                                WHERE
                                    ofspm.org_id = 16
                                    AND ofspm.faculty_id = 100091
                            ) AS merged
                                INNER JOIN
                            org_permissionset OPS
                                    ON OPS.id = merged.permissionset_id
                                    AND OPS.deleted_at IS NULL
                        WHERE student_id = p.id
                        )', 16, []
                    ],

//                  Example 4:  invalid org, invalid search
                    [' EXISTS (

                        SELECT DISTINCT
                            merged.student_id
                        FROM
                            (
                                SELECT
                                    ofspm.student_id,
                                    ofspm.permissionset_id
                                FROM
                                    org_faculty_student_permission_map ofspm
                                WHERE
                                    ofspm.org_id = 1600
                                    AND ofspm.faculty_id = 100091
                            ) AS merged
                                INNER JOIN
                            org_permissionset OPS
                                    ON OPS.id = merged.permissionset_id
                                    AND OPS.deleted_at IS NULL
                        WHERE student_id = p.id
                        )', 1600, []
                    ],

//                  Example 4:  Valid org, blank search
                    ['', 16, "Array to string conversion"]
                ]
        ]);
    }
}