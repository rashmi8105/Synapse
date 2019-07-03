<?php

use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;

class ActivityLogRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     *
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var ActivityLogRepository
     */
    private $activityLogRepository;

    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->activityLogRepository = $this->repositoryResolver->getRepository(ActivityLogRepository::REPOSITORY_KEY);
    }

    public function testGetStudentAppointments()
    {
        $this->specify(
            "Verify the functionality of the method GetStudentAppointments, Fetching appointments of student",
            function ($studentId, $organizationId, $staffId, $sharingViewAccess, $expectedIds) {
                $appointmentActivityArray = $this->activityLogRepository->GetStudentAppointments(
                    $studentId,
                    $organizationId,
                    $staffId,
                    $sharingViewAccess
                );

                for ($i = 0; $i < count($appointmentActivityArray); $i++) {
                    verify($appointmentActivityArray[$i])->hasKey('activity_id');
                    verify($appointmentActivityArray[$i]['activity_log_id'])->notEmpty();
                    verify($appointmentActivityArray[$i]['activity_id'])->equals($expectedIds[$i]);
                }

            },
            [
                "examples" => [
                    [
                        4893137,
                        204,
                        4893111,
                        [
                            'public_view' => 1,
                            'team_view' => 1,
                        ],
                        [6850],
                    ],
                    [
                        4893127,
                        204,
                        4893111,
                        [
                            'public_view' => 1,
                            'team_view' => 1,
                        ],
                        [6852, 6850, 6846],
                    ],
                ],
            ]
        );
    }

    public function testGetActivityCount()
    {
        $this->specify("Verify the functionality of the method getActivityCount", function ($allVariablesInArrayInput, $expectedResults) {

            try {
                $activitiesCount = $this->activityLogRepository->getActivityCount($allVariablesInArrayInput);
                $activitiesCountArray = array();
                $activitiesCountArray['N'] = 0;
                $activitiesCountArray['A'] = 0;
                $activitiesCountArray['R'] = 0;
                $activitiesCountArray['C'] = 0;
                $activitiesCountArray['E'] = 0;

                foreach ($activitiesCount as $activityCount) {
                    $activitiesCountArray[$activityCount['activity_type']] = $activitiesCountArray[$activityCount['activity_type']] + 1;
                }
                verify($activitiesCountArray['N'])->equals($expectedResults['N']);
                verify($activitiesCountArray['A'])->equals($expectedResults['A']);
                verify($activitiesCountArray['R'])->equals($expectedResults['R']);
                verify($activitiesCountArray['C'])->equals($expectedResults['C']);
                verify($activitiesCountArray['E'])->equals($expectedResults['E']);
            } catch (Exception $e) {
                verify($e->getMessage())->equals($expectedResults);
            }
        }, [
            "examples" => [
                // Example 1 : Get all activities count for the student with respect to faculty and permission set
                [
                    [
                        'studentId' => 4893127,
                        'activityArray' => '"R","N","C","A","E"',
                        'faculty' => 4893111,
                        'orgId' => 204,
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
                        'N' => 1,
                        'A' => 3,
                        'R' => 6,
                        'C' => 1,
                        'E' => 0,
                    ]
                ],
                // Example 2 : Get Note,Referral and Interaction activities count for the student with respect to faculty and permission set
                [
                    [
                        'studentId' => 377452,
                        'activityArray' => '"R","N","C"',
                        'faculty' => 113802,
                        'orgId' => 99,
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
                        'N' => 2,
                        'R' => 1,
                        'C' => 1,
                        'A' => 0,
                        'E' => 0
                    ]
                ],
                // Example 3 : Get all activities count zero for the student id IS NULL with faculty_id=113802 and organization_id=99
                [
                    [
                        'studentId' => null,
                        'activityArray' => '"R","N","C","A","E"',
                        'faculty' => 113802,
                        'orgId' => 99,
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
                        'A' => 0,
                        'R' => 0,
                        'C' => 0,
                        'E' => 0,
                    ]
                ],
                // Example 4 : student_id not found throws exception
                [
                    [],
                    'Undefined index: studentId'
                ]
            ]
        ]);
    }

    public function testGetActivityAll()
    {
        $this->specify("Verify the functionality of the method getActivityAll", function ($allVariablesInArrayInput, $expectedNoteIds, $expectedAppointmentIds, $expectedReferralIds, $expectedContactIds, $expectedEmailIds, $expectedResults = '') {
            try {
                $activityArray = $this->activityLogRepository->getActivityAll($allVariablesInArrayInput);

                if (count($activityArray) > 0) {
                    for ($i = 0; $i < count($activityArray); $i++) {
                        verify($activityArray[$i])->hasKey('activity_type');
                        verify($activityArray[$i]['activity_type'])->notEmpty();
                        if ($activityArray[$i]['activity_type'] === 'A') {
                            verify($expectedAppointmentIds)->contains($activityArray[$i]['AppointmentId']);
                        } elseif ($activityArray[$i]['activity_type'] === 'R') {
                            verify($expectedReferralIds)->contains($activityArray[$i]['ReferralId']);
                        } elseif ($activityArray[$i]['activity_type'] === 'C') {
                            verify($expectedContactIds)->contains($activityArray[$i]['ContactId']);
                        } elseif ($activityArray[$i]['activity_type'] === 'E') {
                            verify($expectedEmailIds)->contains($activityArray[$i]['EmailId']);
                        } elseif ($activityArray[$i]['activity_type'] === 'N') {
                            verify($expectedNoteIds)->contains($activityArray[$i]['NoteId']);
                        }
                    }
                } else {
                    verify($activityArray)->isEmpty();
                }
            } catch (Exception $e) {
                verify($e->getMessage())->equals($expectedResults);
            }


        }, [
            "examples" => [
                // Example 1 : Get all activities for the student with respect to faculty and permission set
                [
                    [
                        'studentId' => 4893127,
                        'activityArr' => '"R","N","C","A","E"',
                        'faculty' => 4893111,
                        'orgId' => 204,
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
                    [256919],
                    [6852, 6850, 6846],
                    [102926, 102925, 102924, 102921, 102920, 102919],
                    [4897187],
                    []
                ],
                // Example 2 : Get Note,Referral and Interaction activities for the student with respect to faculty and permission set
                [
                    [
                        'studentId' => 377452,
                        'activityArr' => '"R","N","C"',
                        'faculty' => 113802,
                        'orgId' => 99,
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
                    [24528, 24527],
                    [],
                    [54990],
                    [185428],
                    []
                ],
                // Example 3 : Get empty activities for the student id IS NULL with faculty_id=113802 and organization_id=99
                [
                    [
                        'studentId' => null,
                        'activityArr' => '"R","N","C","A","E"',
                        'faculty' => 113802,
                        'orgId' => 99,
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
                    [],
                    [],
                    [],
                    [],
                    []
                ],
                // Example 4 : student_id not found throws exception
                [
                    [],
                    [],
                    [],
                    [],
                    [],
                    [],
                    'Undefined index: studentId'
                ]
            ]
        ]);
    }

    public function testGetActivityReferral()
    {
        $this->specify("Verify the functionality of the method GetActivityReferral, Fetching referral of student", function ($allVariablesInArrayInput, $expectedReferralIds) {
            try {
                $activityArray = $this->activityLogRepository->getActivityReferral($allVariablesInArrayInput);
                if (count($activityArray) > 0) {
                    for ($i = 0; $i < count($activityArray); $i++) {
                        verify($activityArray[$i])->hasKey('activity_id');
                        verify($activityArray[$i]['activity_log_id'])->notEmpty();
                        verify($expectedReferralIds)->contains($expectedReferralIds[$i]);
                    }
                } else {
                    verify($activityArray)->isEmpty();
                }
            } catch (Exception $e) {
                verify($e->getMessage())->equals($expectedReferralIds);
            }

        }, [
            // Example 1 : Get all referral activities for the student with respect to faculty and permission set
            "examples" => [
                [
                    [
                        'studentId' => 4893127,
                        'faculty' => 4893111,
                        'orgId' => 204,
                        'publicAccess' => 1,
                        'teamAccess' => 1,
                        'publicAccessReasonRouted' => 1,
                        'teamAccessReasonRouted' => 1,
                        'roleIds' => [1, 2, 3]
                    ],
                    [102926, 102925, 102924, 102921, 102920, 102919]
                ],
                // Example 2 : Get empty referral activities for the student not exist in this organization_id=204
                [
                    [
                        'studentId' => 377452,
                        'faculty' => 113802,
                        'orgId' => 204,
                        'publicAccess' => 1,
                        'teamAccess' => 1,
                        'publicAccessReasonRouted' => 1,
                        'teamAccessReasonRouted' => 1,
                        'roleIds' => [1, 2, 3]
                    ],
                    []
                ],
                // Example 3 : Get empty referral activities for the student id IS NULL with faculty_id=113802 and organization_id=99
                [
                    [
                        'studentId' => null,
                        'faculty' => 113802,
                        'orgId' => 99,
                        'publicAccess' => 1,
                        'teamAccess' => 1,
                        'publicAccessReasonRouted' => 1,
                        'teamAccessReasonRouted' => 1,
                        'roleIds' => [1, 2, 3]
                    ],
                    []
                ],
                // Example 4 : student_id not found throws exception
                [
                    [],
                    'Undefined index: studentId'
                ]
            ]
        ]);
    }

    public function testGetActivityAllInteraction()
    {
        $this->specify("Verify the functionality of the method getActivityAllInteraction", function ($allVariablesInArrayInput, $expectedContactIds) {
            try {
                $activityArray = $this->activityLogRepository->getActivityAllInteraction($allVariablesInArrayInput);
                if (count($activityArray) > 0) {
                    for ($i = 0; $i < count($activityArray); $i++) {
                        verify($activityArray[$i])->hasKey('activity_type');
                        verify($activityArray[$i]['activity_type'])->notEmpty();
                        verify($expectedContactIds)->contains($activityArray[$i]['ContactId']);
                    }
                } else {
                    verify($activityArray)->isEmpty();
                }
            } catch (Exception $e) {
                verify($e->getMessage())->equals($expectedContactIds);
            }

        }, [
            // Example 1 : Get all interaction activities for the student with respect to faculty and permission set
            "examples" => [
                [
                    [
                        'studentId' => 4893127,
                        'activityArr' => '"C"',
                        'faculty' => 4893111,
                        'orgId' => 204,
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
                        'roleIds' => [1, 2, 3]
                    ],
                    [4897187]
                ],
                // Example 2 : Get empty interaction activities for the student not exist in this organization_id=204
                [
                    [
                        'studentId' => 377452,
                        'activityArr' => '"C"',
                        'faculty' => 113802,
                        'orgId' => 204,
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
                        'roleIds' => [1, 2, 3]
                    ],
                    []
                ],
                // Example 3 : Get empty interaction activities for the student id IS NULL with faculty_id=113802 and organization_id=99
                [
                    [
                        'studentId' => null,
                        'activityArr' => '"C"',
                        'faculty' => 113802,
                        'orgId' => 99,
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
                        'roleIds' => [1, 2, 3]
                    ],
                    []
                ],
                // Example 4 : student_id not found throws exception
                [
                    [],
                    'Undefined index: studentId'
                ]
            ]
        ]);
    }

    public function testGetContactActivityCount()
    {
        $this->specify("Verify the functionality of the method getContactActivityCount", function ($facultyId, $studentId, $featureId, $academicYearId, $expectedResult) {
            $results = $this->activityLogRepository->getContactActivityCount($facultyId, $studentId, $featureId, $academicYearId);
            verify($results)->equals($expectedResult);
        }, [
            "examples" => [
                // count the contacts activity for student  4878907
                [4878750, 4878907, 3, 158, 0],
                // Passing empty academic year
                [138390, 159214, 3, '', 0],
                // count the contacts activity for student  4613996
                [4605389, 4613996, 3, 162, 2],
                // passing empty faculty id and student id.
                ['', '', 3, 84, 0],
                // count the contacts activity for student  159214
                [138390, 159214, 3, 69, 6],
                // passing empty faculty id.
                ['', 4878907, 3, 156, 0],
                // passing empty student id.
                [4878907, '', 3, 159, 0],
                // get the count of notes where the student is non participant.
                [4878750, 4878907, 3, 160, 0],
                // get count of contacts activity with private access
                [4893111, 4893127, 1, 167, 1]
            ]
        ]);
    }

    public function testGetNoteActivityCount()
    {
        $this->specify("Verify the functionality of the method getNoteActivityCount", function ($facultyId, $studentId, $featureId, $academicYearId, $expectedResult) {
            $results = $this->activityLogRepository->getNoteActivityCount($facultyId, $studentId, $featureId, $academicYearId);
            verify($results)->equals($expectedResult);
        }, [
            "examples" => [
                // Passing empty academic year id.
                [4605389, 4613996, 2, '', 0],
                // get the count of notes activity for student  4613996
                [4605389, 4613996, 2, 84, 4],
                // passing empty faculty id.
                ['', 4878907, 2, 161, 0],
                // get the count of notes where the student is non participant.
                [4878750, 4878907, 2, 160, 0],
                // passing empty student and faculty id.
                ['', '', 2, 84, 0],
                [53902, 4558328, 2, 69, 6],
                // passing empty student id.
                [4878907, '', 2, 161, 0],
                // get the count of notes saved with private access
                [249488, 4644151, 1, 60, 1]
            ]
        ]);
    }

    public function testGetAppointmentsActivityCount()
    {
        $this->specify("Verify the functionality of the method getAppointmentsActivityCount", function ($facultyId, $studentId, $featureId, $academicYearId, $expectedResult) {
            $results = $this->activityLogRepository->getAppointmentsActivityCount($facultyId, $studentId, $featureId, $academicYearId);
            verify($results)->equals($expectedResult);
        }, [
            "examples" => [
                // get the count of appointments activity for student  4878907
                [4878750, 4878907, 4, 161, 0],
                // passing empty student id and faculty id.
                ['', '', 4, 161, 0],
                // get the count of appointments activity for student  4613996
                [4605389, 4613996, 4, 162, 9],
                // passing empty academic year id.
                [4878750, 4878907, 4, '', 0],
                // passing empty faculty id.
                ['', 4878907, 4, 159, 0],
                // passing empty student id.
                [4878907, '', 4, 159, 0],
                // get the count of appointments activity where the student is non-participant
                [4878750, 4878907, 4, 161, 0],
                //
                [4605389, 4605389, 1, 84, 1]
            ]
        ]);
    }

    public function testGetEmailActivityCount()
    {
        $this->specify("Verify the functionality of the method getEmailActivityCount", function ($facultyId, $studentId, $featureId, $academicYearId, $expectedResult) {
            $results = $this->activityLogRepository->getEmailActivityCount($facultyId, $studentId, $featureId, $academicYearId);
            verify($results)->equals($expectedResult);
        }, [
            "examples" => [
                // get the count of email activity for student  489367
                [138390, 489367, 7, 69, 1],
                // passing empty academic year id
                [4878750, 4878907, 7, '', 0],
                // get the count of email activity where the student is non-participant
                [4878750, 4878907, 7, 161, 0],
                // passing empty faculty id.
                ['', 4878907, 7, 203, 0],
                // get the count of email activity for student  4558328
                [138390, 4558328, 7, 180, 4],
                // passing empty faculty id and student id.
                ['', '', 7, 180, 0],
                // passing empty student id.
                [4878907, '', 7, 203, 0],
                //get the count of email for access private
                [223674, 4752080, 1, 110, 10]
            ]
        ]);
    }

    public function testGetReferralsActivityCount()
    {
        $this->specify("Verify the functionality of the method getReferralsActivityCount", function ($facultyId, $studentId, $organizationId, $featureId, $academicYearId, $expectedResult) {
            $results = $this->activityLogRepository->getReferralsActivityCount($facultyId, $studentId, $organizationId, $featureId, $academicYearId);
            verify($results)->equals($expectedResult);
        }, [
            "examples" => [
                // get the count of referral activity for student  4878907
                [
                    4878750,
                    4878907,
                    203,
                    1,
                    159,
                    0,

                ],
                // Reason routed routing assigned to a faculty
                [
                    130332,
                    244734,
                    170,
                    1,
                    136,
                    1
                ],
                // get the count of referrals where the student is non participant for the academic year
                [
                    108575,
                    490231,
                    87,
                    1,
                    61,
                    0
                ],
                // Reason routed assigned to primary coordinator
                [
                    58112,
                    201589,
                    75,
                    1,
                    100,
                    1
                ],
                // Routed to Primary campus connection
                [
                    4551171,
                    4543347,
                    182,
                    1,
                    85,
                    0
                ],
                // passing empty faculty id and student id.
                [
                    '',
                    '',
                    203,
                    1,
                    159,
                    0
                ],
                // get the count of referrals where the student is non participant for the academic year
                [
                    132305,
                    4558984,
                    182,
                    1,
                    85,
                    0
                ],
                // passing empty faculty id.
                [
                    '',
                    4878907,
                    203,
                    1,
                    160,
                    0
                ],
                // passing empty student id.
                [
                    4878907,
                    '',
                    203,
                    1,
                    161,
                    0
                ],
                // Passing empty academic year id
                [
                    58112,
                    201589,
                    75,
                    1,
                    '',
                    0
                ],
                // Get Referral activity count for private access
                [
                    4893111,
                    4893127,
                    204,
                    1,
                    167,
                    6
                ]
            ]
        ]);
    }
}
