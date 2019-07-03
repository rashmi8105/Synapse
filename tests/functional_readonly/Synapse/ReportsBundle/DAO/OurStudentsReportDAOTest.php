<?php

use Codeception\TestCase\Test;

class OurStudentsReportDAOTest extends Test
{
    use \Codeception\Specify;

    private $org191Students = [4556012,4556013,4556014,4556015,4556016,4556017,4556018,4556019,4556020,4556021,4556022,4556023,4556024,4556025,4556026,4556028,4556029,4556030,4556031,4556032,4556033,4556034,4556035,4556036,4556037,4556038,4556039,4556040,4556041,4556042,4556043,4556044,4556045,4556046,4556047,4556048,4556049,4556050,4556051,4556052,4556053,4556054,4556055,4556056,4556057,4556058,4556059,4556060,4556061,4556062,4556063,4556064,4556065,4556066,4556067,4556068,4556069,4556070,4556071,4556072,4556073,4556074,4556075,4556076,4556077,4556078,4556079,4556080,4556081,4556082,4556083,4556084,4556085,4556086,4556087,4556088,4556089,4556090,4556091,4556092,4556093,4556094,4556095,4556096,4556097,4556099,4556100,4556101,4556102,4556103,4556105,4556106,4556107,4556108,4556109,4556110,4556111,4556112,4556113,4556114,4556115,4556117,4556118,4556119,4556120,4556121,4556122,4556123,4556124,4556125,4556126,4556127,4556128,4556129,4556130,4556131,4556132,4556133,4556134,4556135,4556136,4556137,4556138,4556139,4556140,4556141,4556142,4556143,4556144,4556145,4556146,4556147,4556148,4556149,4556150,4556151,4556152,4556153,4556154,4556155,4556156,4556157,4556158,4556159,4556160,4556161,4556162,4556163,4556164,4556165,4556166,4556167,4556168,4556169,4556170,4556171,4556172,4556173,4556174,4556175,4556176,4556177,4556178,4556179,4556180,4556181,4556183,4556184,4556186,4556187,4556188,4556189,4556190,4556191,4556192,4556193,4556194,4556195,4556196,4556197,4556198,4556199,4556200,4556201,4556202,4556203,4556204,4556205,4556208,4556209,4556210,4556211,4556417,4556418,4556419,4556420,4556421,4556422,4556423,4556424,4556425,4556426,4556427,4556428,4556429,4556430,4556431,4556432,4556433,4556435,4556436,4556437,4556438,4556439,4556440,4556441,4556442,4556443,4556444,4556445,4556446,4556447,4556448,4556449,4556450,4556451,4556452,4556453,4556455,4556456,4556457,4556458,4556459,4556460,4556461,4556462,4556463,4556464,4556465,4556466,4556467,4556468,4556469,4556470,4556471,4556472,4556473,4556474,4556475,4556476,4556477,4556478,4556479,4556480,4556481,4556482,4556483,4556484,4556485,4556486,4556487,4556488,4556489,4556490,4556491,4556492,4556494,4556495,4556496,4556497,4556498,4556499,4556500,4556501,4556502,4556503,4556504,4556505,4556506,4556507,4556508,4556509,4556510,4556511,4556512,4556513,4556514,4556515,4556516,4556517,4556518,4556519,4556520,4556521,4556522,4556523,4556524,4556525,4556526,4556527,4556528,4556529,4556530,4556531,4556532,4556533,4556534,4556535,4556536,4556537,4556538,4556539,4556540,4556541,4556542,4556543,4556544,4556545,4556546,4556547,4556548,4556549,4556550,4556551,4556552,4556553,4556554,4556555,4556556,4556557,4556558,4556559,4556560,4556561,4556562,4556563,4556564,4556565,4556566,4556567,4556568,4556569,4556570,4556571,4556572,4556573,4556574,4556575,4556576,4556577,4556578,4556580,4556581,4556582,4556583,4556584,4556585,4556586,4556587,4556588,4556589,4556590,4556591,4556592,4556593,4556595,4556596,4556597,4556598,4556599,4556600,4556601,4556602,4556603,4556604,4556605,4556606,4556607,4556608,4556609,4556610,4556611,4556612,4556613,4556614,4556615,4556616,4556617,4556618,4556619,4556620,4556621,4556622,4556624,4556625,4556626,4556627,4556628,4556629,4556630,4556631,4556632,4556633,4556634,4556635,4556636,4556637,4556638,4556639,4556640,4556641,4556642,4556643,4556644,4556645,4556646,4556647,4556648,4556649,4556650,4556651,4556653,4556654,4556655,4556656,4556657,4556658,4556659,4556660,4556661,4556662,4556663,4556664,4556665,4556666,4556667,4556668,4556669,4556670,4556671,4556672,4556673,4556674,4556675,4556676,4556677,4556678,4556679,4556680,4556681,4556682,4556683,4556684,4556685,4556686,4556687,4556688,4556689,4556690,4556691,4556692,4556693,4556694,4556695,4556696,4556697,4556698,4556699,4556700,4556701,4556702,4556703,4556704,4556705,4556707,4556708,4556709,4556710,4556711,4556712,4556713,4556714,4556715,4556716,4556718,4556719,4556720,4556721,4556722,4556723,4556724,4556725,4556726,4556727,4556728,4556729,4556730,4556731,4556732,4556733,4556734,4556735,4556736,4556737,4556738,4556739,4556740,4556741,4556742,4556743,4556744,4556745,4556746,4556747,4556748,4556749,4556750,4556751,4556752,4556753,4556754,4556755,4556756,4556757,4556758,4556759,4556760,4556761,4556762,4556763,4556764,4556765,4556766,4556767,4556768,4556769,4556770,4556771,4556772,4556773,4556774,4556775,4556776,4556777,4556778,4556779,4556780,4556781,4556782,4556783,4556784,4556785,4556786,4556787,4556788,4556789,4556790,4556791,4556792,4556793,4556794,4556795,4556796,4556797,4556798,4556799,4556800,4556801,4556802,4556803,4556804,4556805,4556806,4556807,4556808,4556809,4556810,4556811,4556812,4556813,4556814,4556815,4556816,4556818,4556819,4556820,4556821,4556822,4556823,4556824,4556825,4556826,4556827,4556828,4556829,4556830,4556831,4556832,4556833,4556834,4556835,4556836,4556837,4556838,4556839,4556840,4556841,4556842,4556843,4556844,4556845,4556846,4556847,4556848,4556849,4556850,4556851,4556852,4556853,4556854,4556855,4556856,4556857,4556858,4556859,4556860,4556861,4556862,4556863,4556864,4556866,4556867,4556868,4556869,4556870,4556871,4556872,4556873,4556874,4556875,4556876,4556877,4556878,4556879,4556880,4556881,4556882,4556883,4556884,4556885,4556886,4556887,4556888,4556889,4556890,4556891,4556892,4556893,4556894,4556895,4556896,4556897,4556898,4556899,4556900,4556901,4556902,4556903,4556904,4556905,4556906,4556907,4556908,4556909,4556910,4556911,4556912,4556913,4556914,4556915,4556916,4556917,4556918,4556919,4556920,4556921,4556922,4556923,4556924,4556925,4556926,4556927,4556928,4556929,4556930,4556931,4556933,4556934,4556936,4556937,4556938,4556939,4556940,4556941,4556942,4556943,4556944,4556945,4556946,4556947,4556948,4556949,4556950,4556951,4556952,4556953,4556954,4556955,4556956,4556957,4556958,4556959,4556960,4556961,4556962,4556963,4556964,4556965,4556966,4556967,4556968,4556969,4556970,4556971,4556972,4556973,4556974,4556975,4556978,4556979,4556980,4556981,4556982,4556984,4556985,4556986,4556987,4556988,4556989,4556990,4556991,4556992,4556993,4556994,4556995,4556996,4556997,4556998,4556999,4557000,4557001,4557002,4557003,4557004,4557005,4557006,4557007,4557008,4557009,4557010,4557011,4557013,4557014,4557015,4557016,4557017,4557018,4559079,4559080,4559081,4559082,4559083,4559084,4559085,4559086,4559087,4559088,4559089,4559090,4559091,4559092,4559093,4559094,4559095,4559096,4559097,4559098,4559099,4559100,4559101,4559102,4559103,4559104,4559105,4559106,4559107,4559108,4559110,4559111,4559112,4559113,4559114,4559115,4559116,4559117,4559118,4559119,4559120,4559121,4559122,4559123,4559124,4559125,4559126,4559127,4559128,4559129,4559130,4559131,4559132,4559133,4559134,4559135,4559136,4559137,4559138,4559139,4559140,4559141,4559142,4559143,4559144,4559145,4559146,4559147,4559148,4559149,4559150,4559151,4559152,4559153,4559154,4559155,4559156,4559157,4559158,4559159,4559160,4559161,4559162,4559163,4559164,4559165,4559166,4559167,4559168,4559169,4559170,4559171,4559172,4559173,4559174,4559175,4559176,4559177,4559178,4559179,4559180,4559181,4559182,4559183,4559184,4559185,4559186,4559187,4559188,4687162,4687164,4687165,4687166,4687167,4687168,4687169,4687171,4687172,4687174,4687730,4783311,4783314,4799540];

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\ReportsBundle\DAO\OurStudentsReportDAO
     */
    private $ourStudentsReportDAO;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->ourStudentsReportDAO = $this->container->get('our_students_report_dao');
    }


    public function testGetFactorResponseCounts()
    {
        $this->specify("Verify the functionality of the method getFactorResponseCounts", function ($facultyId, $organizationId, $surveyId, $studentIds, $expectedResults) {
            $results = $this->ourStudentsReportDAO->getFactorResponseCounts($facultyId, $organizationId, $surveyId, $studentIds);

            verify($results)->equals($expectedResults);


        }, ["examples" =>
            [
                // Example 1a:  Base example using the students from ESPRJ-11863
                [256049, 191, 11, $this->org191Students,
                    [
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 21,
                            'element_name' => 'High overall satisfaction with institution',
                            'numerator_count' => 304,
                            'denominator_count' => 574
                        ],
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 26,
                            'element_name' => 'High social integration',
                            'numerator_count' => 298,
                            'denominator_count' => 574
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 39,
                            'element_name' => 'Has high test anxiety',
                            'numerator_count' => 184,
                            'denominator_count' => 594
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 46,
                            'element_name' => 'High academic integration',
                            'numerator_count' => 339,
                            'denominator_count' => 574
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 52,
                            'element_name' => 'High basic academic behaviors',
                            'numerator_count' => 581,
                            'denominator_count' => 622
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 56,
                            'element_name' => 'High advanced academic behaviors',
                            'numerator_count' => 342,
                            'denominator_count' => 593
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 60,
                            'element_name' => 'Rates their self-discipline high',
                            'numerator_count' => 430,
                            'denominator_count' => 609
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 64,
                            'element_name' => 'Rates their time management skills high',
                            'numerator_count' => 231,
                            'denominator_count' => 609
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 68,
                            'element_name' => 'Has high academic self-efficacy (confidence)',
                            'numerator_count' => 231,
                            'denominator_count' => 605
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 72,
                            'element_name' => 'Has high academic resiliency',
                            'numerator_count' => 399,
                            'denominator_count' => 598
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 76,
                            'element_name' => 'Rates their analytical skills high',
                            'numerator_count' => 269,
                            'denominator_count' => 611
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 80,
                            'element_name' => 'Rates their communications skills high',
                            'numerator_count' => 189,
                            'denominator_count' => 611
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 86,
                            'element_name' => 'Rates residence hall social aspects high',
                            'numerator_count' => 211,
                            'denominator_count' => 472
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 90,
                            'element_name' => 'Rates residence hall environment high',
                            'numerator_count' => 267,
                            'denominator_count' => 469
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 94,
                            'element_name' => 'Strong roommate relationships',
                            'numerator_count' => 323,
                            'denominator_count' => 436
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 100,
                            'element_name' => 'Strong peer connections',
                            'numerator_count' => 299,
                            'denominator_count' => 587
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 104,
                            'element_name' => 'High homesickness (separation)',
                            'numerator_count' => 219,
                            'denominator_count' => 438
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 108,
                            'element_name' => 'High homesickness (distress)',
                            'numerator_count' => 58,
                            'denominator_count' => 436
                        ]
                    ]
                ],
                // Example 1b:  Survey 12 for the same students
                [256049, 191, 12, $this->org191Students,
                    [
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 21,
                            'element_name' => 'High overall satisfaction with institution',
                            'numerator_count' => 188,
                            'denominator_count' => 397
                        ],
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 26,
                            'element_name' => 'High social integration',
                            'numerator_count' => 175,
                            'denominator_count' => 396
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 39,
                            'element_name' => 'Has high test anxiety',
                            'numerator_count' => 136,
                            'denominator_count' => 409
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 46,
                            'element_name' => 'High academic integration',
                            'numerator_count' => 185,
                            'denominator_count' => 394
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 52,
                            'element_name' => 'High basic academic behaviors',
                            'numerator_count' => 386,
                            'denominator_count' => 436
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 56,
                            'element_name' => 'High advanced academic behaviors',
                            'numerator_count' => 97,
                            'denominator_count' => 418
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 86,
                            'element_name' => 'Rates residence hall social aspects high',
                            'numerator_count' => 138,
                            'denominator_count' => 317
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 90,
                            'element_name' => 'Rates residence hall environment high',
                            'numerator_count' => 188,
                            'denominator_count' => 311
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 94,
                            'element_name' => 'Strong roommate relationships',
                            'numerator_count' => 183,
                            'denominator_count' => 283
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 100,
                            'element_name' => 'Strong peer connections',
                            'numerator_count' => 174,
                            'denominator_count' => 398
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 104,
                            'element_name' => 'High homesickness (separation)',
                            'numerator_count' => 158,
                            'denominator_count' => 286
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 108,
                            'element_name' => 'High homesickness (distress)',
                            'numerator_count' => 35,
                            'denominator_count' => 287
                        ]
                    ]
                ],
                // Example 2:  Demonstrating that the survey blocks in the user's permission set(s) are used to restrict access.
                // Example 2a:  This faculty member has permission set 409, which has all the survey blocks.
                [75647, 70, 11, [4635811],
                    [
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 21,
                            'element_name' => 'High overall satisfaction with institution',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 26,
                            'element_name' => 'High social integration',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 39,
                            'element_name' => 'Has high test anxiety',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 46,
                            'element_name' => 'High academic integration',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 52,
                            'element_name' => 'High basic academic behaviors',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 56,
                            'element_name' => 'High advanced academic behaviors',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 60,
                            'element_name' => 'Rates their self-discipline high',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 64,
                            'element_name' => 'Rates their time management skills high',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 68,
                            'element_name' => 'Has high academic self-efficacy (confidence)',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 72,
                            'element_name' => 'Has high academic resiliency',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 76,
                            'element_name' => 'Rates their analytical skills high',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 80,
                            'element_name' => 'Rates their communications skills high',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 86,
                            'element_name' => 'Rates residence hall social aspects high',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 90,
                            'element_name' => 'Rates residence hall environment high',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 94,
                            'element_name' => 'Strong roommate relationships',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 100,
                            'element_name' => 'Strong peer connections',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 104,
                            'element_name' => 'High homesickness (separation)',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 108,
                            'element_name' => 'High homesickness (distress)',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ]
                    ]
                ],
                // Example 2b:  This faculty member only has permission set 1291, which only has survey blocks 30, 37, 40, 44.
                [4889646, 70, 11, [4635811],
                    [
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 39,
                            'element_name' => 'Has high test anxiety',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 104,
                            'element_name' => 'High homesickness (separation)',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 108,
                            'element_name' => 'High homesickness (distress)',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ]
                    ]
                ],
                // Example 3:  Demonstrating that permissions work correctly if the user has multiple permission sets with different survey blocks.
                // This faculty member has permission set 367, which gives her access to all 27 survey blocks, and permission set 369, which only gives her access to 13 of them.
                // She is only connected to student 4639372 with permission set 369, so not all of this student's factors are included.
                // For example, the student has a factor value corresponding to the first element (element_id 21, factor_id 16),
                // but this factor is in survey block 38, which is not included in permission set 369.
                // On the other hand, she is connected to the other student 4643513 via permission set 367, so all of that student's factors are included.
                [4668039, 63, 11, [4643513, 4639372],
                    [
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 21,
                            'element_name' => 'High overall satisfaction with institution',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 26,
                            'element_name' => 'High social integration',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 39,
                            'element_name' => 'Has high test anxiety',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 46,
                            'element_name' => 'High academic integration',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 52,
                            'element_name' => 'High basic academic behaviors',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 56,
                            'element_name' => 'High advanced academic behaviors',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 60,
                            'element_name' => 'Rates their self-discipline high',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 64,
                            'element_name' => 'Rates their time management skills high',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 68,
                            'element_name' => 'Has high academic self-efficacy (confidence)',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 72,
                            'element_name' => 'Has high academic resiliency',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 76,
                            'element_name' => 'Rates their analytical skills high',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 80,
                            'element_name' => 'Rates their communications skills high',
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 86,
                            'element_name' => 'Rates residence hall social aspects high',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 90,
                            'element_name' => 'Rates residence hall environment high',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 94,
                            'element_name' => 'Strong roommate relationships',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 100,
                            'element_name' => 'Strong peer connections',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 104,
                            'element_name' => 'High homesickness (separation)',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 108,
                            'element_name' => 'High homesickness (distress)',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ]
                    ]
                ],
            ]
        ]);
    }



    public function testGetSurveyResponseCounts()
    {
        $this->specify("Verify the functionality of the method getSurveyResponseCounts", function ($facultyId, $organizationId, $surveyId, $studentIds, $expectedResults) {
            $results = $this->ourStudentsReportDAO->getSurveyResponseCounts($facultyId, $organizationId, $surveyId, $studentIds);

            verify($results)->equals($expectedResults);


        }, ["examples" =>
            [
                // Example 1a:  The example from ESPRJ-11863
                [256049, 191, 11, $this->org191Students,
                    [
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 160,
                            'element_name' => 'Committed to getting a degree here',
                            'numerator_count' => 513,
                            'denominator_count' => 618
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 161,
                            'element_name' => 'Planning to return next term',
                            'numerator_count' => 579,
                            'denominator_count' => 624
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 162,
                            'element_name' => 'Planning to return next year',
                            'numerator_count' => 547,
                            'denominator_count' => 629
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 163,
                            'element_name' => 'Struggling in two or more courses',
                            'numerator_count' => 178,
                            'denominator_count' => 623
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 164,
                            'element_name' => 'Struggling in one course',
                            'numerator_count' => 217,
                            'denominator_count' => 623
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 165,
                            'element_name' => 'Plans to work more than 40 hours per week',
                            'numerator_count' => 0,
                            'denominator_count' => 619
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 166,
                            'element_name' => 'Plans to work 21 to 40 hours per week',
                            'numerator_count' => 23,
                            'denominator_count' => 619
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 167,
                            'element_name' => 'Plans to study 11 or more hours per week',
                            'numerator_count' => 402,
                            'denominator_count' => 622
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 168,
                            'element_name' => 'Has missed more than one class',
                            'numerator_count' => 74,
                            'denominator_count' => 620
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 169,
                            'element_name' => 'Expect to earn A`s',
                            'numerator_count' => 245,
                            'denominator_count' => 622
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 170,
                            'element_name' => 'Degree goal is a Bachelor`s degree',
                            'numerator_count' => 117,
                            'denominator_count' => 608
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 171,
                            'element_name' => 'Degree goal is a Master`s, doctorate, etc.',
                            'numerator_count' => 416,
                            'denominator_count' => 608
                        ],
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 172,
                            'element_name' => 'This institution was first choice',
                            'numerator_count' => 345,
                            'denominator_count' => 614
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 173,
                            'element_name' => 'Confident can afford tuition and fees next term',
                            'numerator_count' => 302,
                            'denominator_count' => 583
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 174,
                            'element_name' => 'Confident can afford monthly expenses',
                            'numerator_count' => 237,
                            'denominator_count' => 530
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 175,
                            'element_name' => 'Believes they are studying a sufficient amount',
                            'numerator_count' => 342,
                            'denominator_count' => 593
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 176,
                            'element_name' => 'Plans to get involved in student organizations',
                            'numerator_count' => 192,
                            'denominator_count' => 586
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 177,
                            'element_name' => 'Plans to get involved in a leadership role',
                            'numerator_count' => 68,
                            'denominator_count' => 577
                        ]
                    ]
                ],
                // Example 1b:  Survey 12 for the same students
                [256049, 191, 12, $this->org191Students,
                    [
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 160,
                            'element_name' => 'Committed to getting a degree here',
                            'numerator_count' => 354,
                            'denominator_count' => 450
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 161,
                            'element_name' => 'Planning to return next term',
                            'numerator_count' => 418,
                            'denominator_count' => 455
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 162,
                            'element_name' => 'Planning to return next year',
                            'numerator_count' => 366,
                            'denominator_count' => 458
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 163,
                            'element_name' => 'Struggling in two or more courses',
                            'numerator_count' => 123,
                            'denominator_count' => 447
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 164,
                            'element_name' => 'Struggling in one course',
                            'numerator_count' => 182,
                            'denominator_count' => 447
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 165,
                            'element_name' => 'Plans to work more than 40 hours per week',
                            'numerator_count' => 2,
                            'denominator_count' => 438
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 166,
                            'element_name' => 'Plans to work 21 to 40 hours per week',
                            'numerator_count' => 15,
                            'denominator_count' => 438
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 167,
                            'element_name' => 'Plans to study 11 or more hours per week',
                            'numerator_count' => 271,
                            'denominator_count' => 436
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 168,
                            'element_name' => 'Has missed more than one class',
                            'numerator_count' => 166,
                            'denominator_count' => 436
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 169,
                            'element_name' => 'Expect to earn A`s',
                            'numerator_count' => 131,
                            'denominator_count' => 435
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 173,
                            'element_name' => 'Confident can afford tuition and fees next term',
                            'numerator_count' => 217,
                            'denominator_count' => 403
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 174,
                            'element_name' => 'Confident can afford monthly expenses',
                            'numerator_count' => 170,
                            'denominator_count' => 356
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 176,
                            'element_name' => 'Plans to get involved in student organizations',
                            'numerator_count' => 102,
                            'denominator_count' => 398
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 177,
                            'element_name' => 'Plans to get involved in a leadership role',
                            'numerator_count' => 40,
                            'denominator_count' => 386
                        ]
                    ]
                ],
                // Example 2:  Demonstrating that the survey blocks in the user's permission set(s) are used to restrict access.
                // Example 2a:  This faculty member has permission set 409, which has all the survey blocks.
                [75647, 70, 11, [4635811],
                    [
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 160,
                            'element_name' => 'Committed to getting a degree here',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 161,
                            'element_name' => 'Planning to return next term',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 162,
                            'element_name' => 'Planning to return next year',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 163,
                            'element_name' => 'Struggling in two or more courses',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 164,
                            'element_name' => 'Struggling in one course',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 165,
                            'element_name' => 'Plans to work more than 40 hours per week',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 166,
                            'element_name' => 'Plans to work 21 to 40 hours per week',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 167,
                            'element_name' => 'Plans to study 11 or more hours per week',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 168,
                            'element_name' => 'Has missed more than one class',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 169,
                            'element_name' => 'Expect to earn A`s',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 170,
                            'element_name' => 'Degree goal is a Bachelor`s degree',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 171,
                            'element_name' => 'Degree goal is a Master`s, doctorate, etc.',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 172,
                            'element_name' => 'This institution was first choice',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 173,
                            'element_name' => 'Confident can afford tuition and fees next term',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 174,
                            'element_name' => 'Confident can afford monthly expenses',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 175,
                            'element_name' => 'Believes they are studying a sufficient amount',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 176,
                            'element_name' => 'Plans to get involved in student organizations',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 177,
                            'element_name' => 'Plans to get involved in a leadership role',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ]
                    ]
                ],
                // Example 2b:  This faculty member only has permission set 1291, which only has survey blocks 30, 37, 40, 44.
                [4889646, 70, 11, [4635811],
                    [
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 160,
                            'element_name' => 'Committed to getting a degree here',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 161,
                            'element_name' => 'Planning to return next term',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 162,
                            'element_name' => 'Planning to return next year',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ]
                    ]
                ],
                // Example 3:  Demonstrating that permissions work correctly if the user has multiple permission sets with different survey blocks.
                // This faculty member has permission set 367, which gives her access to all 27 survey blocks, and permission set 369, which only gives her access to 13 of them.
                // She is only connected to student 4639372 with permission set 369, so not all of this student's responses are included.
                // For example, the student answered the question corresponding to the first element (element_id 160, ebi_question_id 9, survey_question_id 274),
                // but this question is in survey block 30, which is not included in permission set 369.
                // On the other hand, she is connected to the other student 4643513 via permission set 367, so all of that student's responses are included.
                [4668039, 63, 11, [4643513, 4639372],
                    [
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 160,
                            'element_name' => 'Committed to getting a degree here',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 161,
                            'element_name' => 'Planning to return next term',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 162,
                            'element_name' => 'Planning to return next year',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 163,
                            'element_name' => 'Struggling in two or more courses',
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 164,
                            'element_name' => 'Struggling in one course',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 165,
                            'element_name' => 'Plans to work more than 40 hours per week',
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 166,
                            'element_name' => 'Plans to work 21 to 40 hours per week',
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 167,
                            'element_name' => 'Plans to study 11 or more hours per week',
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 168,
                            'element_name' => 'Has missed more than one class',
                            'numerator_count' => 0,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 169,
                            'element_name' => 'Expect to earn A`s',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 170,
                            'element_name' => 'Degree goal is a Bachelor`s degree',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 171,
                            'element_name' => 'Degree goal is a Master`s, doctorate, etc.',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 172,
                            'element_name' => 'This institution was first choice',
                            'numerator_count' => 1,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 173,
                            'element_name' => 'Confident can afford tuition and fees next term',
                            'numerator_count' => 2,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 174,
                            'element_name' => 'Confident can afford monthly expenses',
                            'numerator_count' => 2,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 175,
                            'element_name' => 'Believes they are studying a sufficient amount',
                            'numerator_count' => 1,
                            'denominator_count' => 2
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 176,
                            'element_name' => 'Plans to get involved in student organizations',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 7,
                            'section_name' => 'Peers / Co-Curricular',
                            'element_id' => 177,
                            'element_name' => 'Plans to get involved in a leadership role',
                            'numerator_count' => 0,
                            'denominator_count' => 1
                        ],
                        [
                            'section_id' => 5,
                            'section_name' => 'Goals and Expectations',
                            'element_id' => 178,
                            'element_name' => 'Have decided on a major',
                            'numerator_count' => 2,
                            'denominator_count' => 2
                        ],
                    ]
                ]
            ]
        ]);
    }

}