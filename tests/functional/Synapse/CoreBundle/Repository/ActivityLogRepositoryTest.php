<?php

class ActivityLogRepositoryTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Repository\ActivityLogRepository
     */
    private $activityLogRepository;

    private $facultyId = 4891025;

    private $orgId = 214;


    public function testGetActivityCount()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->activityLogRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:ActivityLog');
        });

        $this->specify("Verify the functionality of the method getActivityCount", function ($allVariablesInArrayInput, $expectedResults) {
            $activityCount = $this->activityLogRepository->getActivityCount($allVariablesInArrayInput);

            $cntArr = array();
            $cntArr['N'] = 0;
            $cntArr['A'] = 0;
            $cntArr['R'] = 0;
            $cntArr['C'] = 0;
            $cntArr['E'] = 0;

            foreach ($activityCount as $cnt) {
                $cntArr[$cnt['activity_type']] = $cntArr[$cnt['activity_type']] + 1;
            }

            verify($cntArr['N'])->equals($expectedResults['N']);
            verify($cntArr['A'])->equals($expectedResults['A']);
            verify($cntArr['R'])->equals($expectedResults['R']);
            verify($cntArr['C'])->equals($expectedResults['C']);
            verify($cntArr['E'])->equals($expectedResults['E']);
        }, [
            "examples" => [
                [
                    [
                        'studentId' => 4891095,
                        'activityArray' => '"R","N","C","A"',
                        'faculty' => $this->facultyId,
                        'orgId' => $this->orgId,
                        'noteTeamAccess' => 1,
                        'notePublicAccess' => 1,
                        'contactTeamAccess' => 1,
                        'contactPublicAccess' => 1,
                        'referralTeamAccess' => 1,
                        'referralPublicAccess' => 1,
                        'referralPublicAccessReasonRouted' => 1,
                        'referralTeamAccessReasonRouted' => 1,
                        'appointmentTeamAccess' => 1,
                        'appointmentPublicAccess' => 1,
                        'emailTeamAccess' => 0,
                        'emailPublicAccess' => 0,
                        'roleIds' => [1, 2, 3],
                    ],
                    [
                        'N' => 0,
                        'A' => 100,
                        'R' => 1,
                        'C' => 9,
                        'E' => 0,
                    ]
                ],
                [
                    [
                        'studentId' => 4891132,
                        'activityArray' => '"R","N","C","A"',
                        'faculty' => 4891025,
                        'orgId' => 214,
                        'noteTeamAccess' => 1,
                        'notePublicAccess' => 1,
                        'contactTeamAccess' => 1,
                        'contactPublicAccess' => 1,
                        'referralTeamAccess' => 1,
                        'referralPublicAccess' => 1,
                        'referralPublicAccessReasonRouted' => 1,
                        'referralTeamAccessReasonRouted' => 1,
                        'appointmentTeamAccess' => 1,
                        'appointmentPublicAccess' => 1,
                        'emailTeamAccess' => 0,
                        'emailPublicAccess' => 0,
                        'roleIds' => [1, 2, 3],
                    ],
                    [
                        'N' => 0,
                        'A' => 33,
                        'R' => 1,
                        'C' => 12,
                        'E' => 0,
                    ]
                ]
            ]
        ]);
    }

    public function testGetActivityAll()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->activityLogRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:ActivityLog');
        });
        $this->specify("Verify the functionality of the method getActivityAll", function ($allVariablesInArrayInput, $expectedNoteIds, $expectedAppointmentIds, $expectedReferralIds, $expectedContactIds, $expectedEmailIds) {
            $activityArr = $this->activityLogRepository->getActivityAll($allVariablesInArrayInput);

            for ($i = 0; $i < count($activityArr); $i++) {
                verify($activityArr[$i])->hasKey('activity_type');
                verify($activityArr[$i]['activity_type'])->notEmpty();
                if ($activityArr[$i]['activity_type'] === 'A') {
                    verify($expectedAppointmentIds)->contains($activityArr[$i]['AppointmentId']);
                } elseif ($activityArr[$i]['activity_type'] === 'R') {
                    verify($expectedReferralIds)->contains($activityArr[$i]['ReferralId']);
                } elseif ($activityArr[$i]['activity_type'] === 'C') {
                    verify($expectedContactIds)->contains($activityArr[$i]['ContactId']);
                } elseif ($activityArr[$i]['activity_type'] === 'E') {
                    verify($expectedEmailIds)->contains($activityArr[$i]['EmailId']);
                } elseif ($activityArr[$i]['activity_type'] === 'N') {
                    verify($expectedNoteIds)->contains($activityArr[$i]['NoteId']);
                }
            }

        }, [
            "examples" => [
                [
                    [
                        'studentId' => 4891132,
                        'activityArr' => '"R","N","C","A"',
                        'faculty' => 4891025,
                        'orgId' => 214,
                        'noteTeamAccess' => 1,
                        'notePublicAccess' => 1,
                        'contactTeamAccess' => 1,
                        'contactPublicAccess' => 1,
                        'referralTeamAccess' => 1,
                        'referralPublicAccess' => 1,
                        'referralPublicAccessReasonRouted' => 1,
                        'referralTeamAccessReasonRouted' => 1,
                        'appointmentTeamAccess' => 1,
                        'appointmentPublicAccess' => 1,
                        'emailTeamAccess' => 0,
                        'emailPublicAccess' => 0,
                        'roleIds' => [1,2,3]
                    ],
                    [],
                    [14502, 8966, 8948, 8930, 8912, 8894, 8876, 8858, 14775, 8840, 14355, 8822, 8804, 6996, 8786, 8768, 8750, 8732, 8714, 8696, 8678, 14208, 14603, 8660, 23267, 8642, 8624, 8606, 8588, 6796, 6453, 6600, 6225],
                    [99168],
                    [4808563, 4808564, 4808565, 4808566, 4808567, 4808568, 4808569, 4808570, 4808582, 4808572, 4808573, 4808571],
                    []
                ]
            ]
        ]);
    }

       public function testGetActivityReferral()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->activityLogRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:ActivityLog');
        });
            $this->specify("Verify the functionality of the method GetActivityReferral, Fetching referral of student", function ($allVariablesInArrayInput, $expectedReferralIds) {
            $activityArr = $this->activityLogRepository->getActivityReferral($allVariablesInArrayInput);
            for ($i = 0; $i < count($activityArr); $i++) {
                verify($activityArr[$i])->hasKey('activity_id');
                verify($activityArr[$i]['activity_log_id'])->notEmpty();
                verify($activityArr[$i]['activity_id'])->equals($expectedReferralIds[$i]);
            }

        }, [
            "examples" => [
                [
                    [
                        'studentId' => 4891132,
                        'faculty' => 4891025,
                        'orgId' => 214,
                        'publicAccess' => 1,
                        'teamAccess' => 1,
                        'publicAccessReasonRouted' => 1,
                        'teamAccessReasonRouted' => 1,
                        'roleIds' => [1,2,3]
                    ],
                    [99168],
                    []
                ]
            ]
        ]);
    }

    public function testGetActivityAllInteraction()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->activityLogRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:ActivityLog');
        });
            $this->specify("Verify the functionality of the method getActivityAllInteraction", function ($allVariablesInArrayInput, $expectedNoteIds,$expectedContactIds) {
            $activityArr = $this->activityLogRepository->getActivityAllInteraction($allVariablesInArrayInput);

            for ($i = 0; $i < count($activityArr); $i++) {
                verify($activityArr[$i])->hasKey('activity_type');
                verify($activityArr[$i]['activity_type'])->notEmpty();
                verify($expectedContactIds)->contains($activityArr[$i]['ContactId']);
            }

        }, [
            "examples" => [
                [
                    [
                        'studentId' => 4891132,
                        'activityArr' => '"C"',
                        'faculty' => 4891025,
                        'orgId' => 214,
                        'noteTeamAccess' => 1,
                        'notePublicAccess' => 1,
                        'contactTeamAccess' => 1,
                        'contactPublicAccess' => 1,
                        'referralTeamAccess' => 1,
                        'referralPublicAccess' => 1,
                        'referralPublicAccessReasonRouted' => 1,
                        'referralTeamAccessReasonRouted' => 1,
                        'appointmentTeamAccess' => 1,
                        'appointmentPublicAccess' => 1,
                        'emailTeamAccess' => 0,
                        'emailPublicAccess' => 0,
                        'roleIds' => [1,2,3]
                    ],
                    [],
                    [4808563, 4808564, 4808565, 4808566, 4808567, 4808568, 4808569, 4808570, 4808582, 4808572, 4808573, 4808571]
                ]
            ]
        ]);
    }
}
