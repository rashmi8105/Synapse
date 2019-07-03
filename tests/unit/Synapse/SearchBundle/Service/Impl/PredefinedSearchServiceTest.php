<?php

use Codeception\TestCase\Test;
use Synapse\CoreBundle\Entity\EbiSearch;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\SearchBundle\Service\Impl\PredefinedSearchService;


class PredefinedSearchServiceTest extends Test
{
    use\Codeception\Specify;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;

    /**
     * @var array
     */
    private $allMyStudentsList = [];

    /**
     * @var array
     */
    private $primaryCampusConnectionsList = [];

    /**
     * @var array
     */
    private $atRiskStudentsList = [];

    /**
     * @var array
     */
    private $studentsWithHighIntentToLeaveList = [];

    /**
     * @var array
     */
    private $highPriorityStudentList = [];

    /**
     * @var int
     */
    private $loggedInPersonId = 5048809;

    /**
     * @var int
     */
    private $organizationId = 62;

    /**
     * @var int
     */
    private $org_academic_year_id = 330; //201617

    /**
     * @var string|null
     */
    private $sortBy = null;

    /**
     * @var int
     */
    private $pageNum = 1;

    private $recordsPerPage = 10;
    /**
     * @var array
     */
    private $allStudentsMetaData = [];

    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockContainer = $this->getMock('Container', ['get']);
        $this->allMyStudentsList = [
            'active_only' => [4958587,4953587,4955587,4957587,4961587,4957823,4954823,4958823,4956823,4961823],
            'all' => [4716587,341587,4956587,4712587,387587,4718587,4676587,749587,1039587,725587]
        ];
        $this->primaryCampusConnectionsList = [
            'active_only' => [],
            'all' => []
        ];

        $this->atRiskStudentsList = [
            'active_only' => [4954823, 4956823, 4955823, 4958376, 4956038, 4961212, 4955212, 4953507, 4956110, 4954875],
            'all' => [4673587, 4954823, 4956823, 4955823, 4958376, 4678376, 4679376, 4675038, 4674038, 4956038]
        ];

        $this->studentsWithHighIntentToLeaveList = [
            'active_only' => [4957618, 4958829, 4961854, 4954825, 4958730, 4956855, 4954901, 4954228, 4958097, 4958419],
            'all' => [4956587, 1037851, 4957618, 4675240, 4673448, 4677037, 4675502, 4956375, 4678829, 4958829]
        ];

        $this->highPriorityStudentList = [
            'active_only' => [4954823, 4956823, 4955823, 4958376, 4956038, 4961212, 4955212, 4953507, 4956110, 4954875],
            'all' => [4673587, 4954823, 4956823, 4955823, 4958376, 4678376, 4679376, 4675038, 4674038, 4956038]
        ];
    }

    public function testGetPredefinedSearchResults()
    {

        $this->specify("testing predefined search results Method", function ($exprectedResult, $expectedStudentIds, $predefinedSearchKey, $onlyIncludeActiveStudents) {
            $methodsMockPredefinedSearchDao = [
                'getAllMyStudents',
                'getMyPrimaryCampusConnections',
                'getAtRiskStudents',
                'getStudentsWithHighIntentToLeave',
                'getHighPriorityStudents',
            ];
            $mockPredefinedSearchDao = $this->getMock('PredefinedSearchDAO', $methodsMockPredefinedSearchDao);
            $student_id_array_key = ($onlyIncludeActiveStudents ? 'active_only' : 'all');
            $student_ids = [];
            switch ($predefinedSearchKey) {
                case 'all_my_students':
                    $student_ids = $this->allMyStudentsList[$student_id_array_key];
                    break;
                case 'my_primary_campus_connections':
                    $student_ids = $this->primaryCampusConnectionsList[$student_id_array_key];
                    break;
                case 'at_risk_students':
                    $student_ids = $this->atRiskStudentsList[$student_id_array_key];
                    break;
                case 'high_intent_to_leave':
                    $student_ids = $this->studentsWithHighIntentToLeaveList[$student_id_array_key];
                    break;
                case 'high_priority_students':
                    $student_ids = $this->highPriorityStudentList[$student_id_array_key];
                    break;
            }
            $mockPredefinedSearchDao->method('getAllMyStudents')->willReturn($student_ids);
            $mockPredefinedSearchDao->method('getMyPrimaryCampusConnections')->willReturn($student_ids);
            $mockPredefinedSearchDao->method('getAtRiskStudents')->willReturn($student_ids);
            $mockPredefinedSearchDao->method('getStudentsWithHighIntentToLeave')->willReturn($student_ids);
            $mockPredefinedSearchDao->method('getHighPriorityStudents')->willReturn($student_ids);

            $mockAcademicYearService = $this->getMock('AcademicYearService', ['findCurrentAcademicYearForOrganization']);
            $mockAcademicYearService->method('findCurrentAcademicYearForOrganization')->willReturn($this->org_academic_year_id);

            $mockStudentListService = $this->getMock('StudentListService', ['getStudentListWithMetadata']);
            $studentMetaData = $this->getMetaDataArray($predefinedSearchKey, $onlyIncludeActiveStudents, $this->pageNum, $this->recordsPerPage);
            $mockStudentListService->method('getStudentListWithMetadata')->willReturn($studentMetaData);

            $mockPersonRepository = $this->getMock('Person', ['find']);
            $personObject = new Person();
            $ebiSearchObject = new EbiSearch();
            $mockPersonRepository->method('find')->willReturn($personObject);

            $mockEbiSearchRepository = $this->getMock('EbiSearch', ['findOneBy']);
            $mockEbiSearchRepository->method('findOneBy')->with(['queryKey' => $predefinedSearchKey])->willReturn($ebiSearchObject);

            $mockEbiSearchHistoryRepository = $this->getMock('EbiSearchHistory', ['findOneBy', 'delete', 'persist']);
            $mockEbiSearchHistoryRepository->method('findOneBy')->with(['person' => $personObject, 'ebiSearch' => $ebiSearchObject]);
            $mockEbiSearchHistoryRepository->method('delete')->willReturn(true);
            $mockEbiSearchHistoryRepository->method('persist')->willReturn(true);

            $mockOrgAcademicTermsRepository = $this->getMock('OrgAcademicTerms', ['getAcademicTermsForYear']);
            $mockOrgAcademicTermsRepository->method('getAcademicTermsForYear')->willReturn([]);
            $this->mockContainer->method('get')->willReturnMap(
                [
                    [
                        'academicyear_service',
                        $mockAcademicYearService,
                    ],
                    [
                        'student_list_service',
                        $mockStudentListService
                    ],
                    [
                        'predefined_search_dao',
                        $mockPredefinedSearchDao
                    ]


                ]
            );
            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    'SynapseCoreBundle:Person',
                    $mockPersonRepository
                ],
                [
                    'SynapseCoreBundle:EbiSearch',
                    $mockEbiSearchRepository
                ],
                [
                    'SynapseCoreBundle:EbiSearch',
                    $mockEbiSearchRepository
                ],
                [
                    'SynapseCoreBundle:EbiSearchHistory',
                    $mockEbiSearchHistoryRepository
                ],
                [
                    'SynapseAcademicBundle:OrgAcademicTerms',
                    $mockOrgAcademicTermsRepository
                ]
            ]);
            $predefinedSearchService = new PredefinedSearchService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $data = $predefinedSearchService->getPredefinedSearchResults($predefinedSearchKey, $this->loggedInPersonId, $this->organizationId, $this->sortBy, $this->pageNum, $this->recordsPerPage, $onlyIncludeActiveStudents);
            // verify total result count
            $this->assertEquals($exprectedResult, $data['total_records']);
            // verify student ids
            $differece = array_diff($expectedStudentIds, array_column($data['search_result'], 'student_id'));
            $this->assertEquals(count($differece), 0);

        }, [
            'examples' => [
                // these examples check the diffrences in count and student ids when onlyIncludeActiveStudents filter is applied
                // get all students with onlyIncludeActiveStudents filter is set false
                [
                    38834, // expected count of total results
                    $this->allMyStudentsList['all'],
                    'all_my_students', // predefinedSearchKey
                    false // onlyIncludeActiveStudents
                ],
                // get all students with onlyIncludeActiveStudents filter is set true
                [
                    7747,
                    $this->allMyStudentsList['active_only'],
                    'all_my_students',
                    true
                ],
                // get students primary campus connection for logged in faculty with onlyIncludeActiveStudents filter is set false
                [
                    0,
                    $this->primaryCampusConnectionsList['all'],
                    'my_primary_campus_connections',
                    false
                ],
                // get students primary campus connection for logged in faculty with onlyIncludeActiveStudents filter is set true
                [
                    0,
                    $this->primaryCampusConnectionsList['active_only'],
                    'my_primary_campus_connections',
                    true
                ],
                // get at risk students with onlyIncludeActiveStudents filter is set false
                [
                    3298,
                    $this->atRiskStudentsList['all'],
                    'at_risk_students',
                    false
                ],
                // get at risk students with onlyIncludeActiveStudents filter is set true
                [
                    1374,
                    $this->atRiskStudentsList['active_only'],
                    'at_risk_students',
                    true
                ],
                // get high intent to leave students with onlyIncludeActiveStudents filter is set false
                [
                    202,
                    $this->studentsWithHighIntentToLeaveList['all'],
                    'high_intent_to_leave',
                    false
                ],
                // get high intent to leave students with onlyIncludeActiveStudents filter is set true
                [
                    75,
                    $this->studentsWithHighIntentToLeaveList['active_only'],
                    'high_intent_to_leave',
                    true
                ],
                // get high priority students with onlyIncludeActiveStudents filter is set false
                [
                    3292,
                    $this->highPriorityStudentList['all'],
                    'high_priority_students',
                    false
                ],
                // get high priority students with onlyIncludeActiveStudents filter is set true
                [
                    1373,
                    $this->highPriorityStudentList['active_only'],
                    'high_priority_students',
                    true
                ]
            ]
        ]);
    }

    /**
     * @param string $predefinedSearchKey
     * @param int $loggedInUserId
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @return array $metadata
     */
    private function getMetaDataArray($predefinedSearchKey, $onlyIncludeActiveStudents, $pageNumber, $recordsPerPage)
    {
        $metadata = ['total_records' => 0, 'records_per_page' => $recordsPerPage, 'current_page' => $pageNumber, 'search_result' => []];
        switch ($predefinedSearchKey) {
            case 'all_my_students';
                if ($onlyIncludeActiveStudents) {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 7747,
                        'records_per_page' => '10',
                        'total_pages' => 775,
                        'current_page' => '1',
                        'search_result' =>
                            array(

                                array(
                                    'student_id' => '4958587',
                                    'student_first_name' => 'Jace',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4958587',
                                    'student_primary_email' => 'MapworksBetaUser04958587@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4953587',
                                    'student_first_name' => 'Madelyn',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4953587',
                                    'student_primary_email' => 'MapworksBetaUser04953587@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4955587',
                                    'student_first_name' => 'Madison',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4955587',
                                    'student_primary_email' => 'MapworksBetaUser04955587@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => 'Sophomore',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4957587',
                                    'student_first_name' => 'Maya',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4957587',
                                    'student_primary_email' => 'MapworksBetaUser04957587@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4961587',
                                    'student_first_name' => 'Mia',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4961587',
                                    'student_primary_email' => 'MapworksBetaUser04961587@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'yellow',
                                    'student_risk_image_name' => 'risk-level-icon-y.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Sophomore',
                                    'student_logins' => '1',
                                    'last_activity_date' => '08/09/2017',
                                    'last_activity' => 'Contact',
                                ),
                                array(
                                    'student_id' => '4957823',
                                    'student_first_name' => 'Adelynn',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4957823',
                                    'student_primary_email' => 'MapworksBetaUser04957823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '3',
                                    'last_activity_date' => '08/09/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4954823',
                                    'student_first_name' => 'Alexis',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4954823',
                                    'student_primary_email' => 'MapworksBetaUser04954823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4958823',
                                    'student_first_name' => 'Anderson',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4958823',
                                    'student_primary_email' => 'MapworksBetaUser04958823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'yellow',
                                    'student_risk_image_name' => 'risk-level-icon-y.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956823',
                                    'student_first_name' => 'Archer',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4956823',
                                    'student_primary_email' => 'MapworksBetaUser04956823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '1',
                                    'last_activity_date' => '07/27/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4961823',
                                    'student_first_name' => 'Ashlyn',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4961823',
                                    'student_primary_email' => 'MapworksBetaUser04961823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => 'Sophomore',
                                    'student_logins' => '1',
                                    'last_activity_date' => '09/22/2016',
                                    'last_activity' => 'Note',
                                ),),);
                } else {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 38834,
                        'records_per_page' => '10',
                        'total_pages' => 3884,
                        'current_page' => '1',
                        'search_result' =>
                            array(

                                array(
                                    'student_id' => '4716587',
                                    'student_first_name' => 'Alannah',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4716587',
                                    'student_primary_email' => 'iphoneisiphone@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Senior',
                                    'student_logins' => '20',
                                    'last_activity_date' => '04/26/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '341587',
                                    'student_first_name' => 'Alexzander',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '341587',
                                    'student_primary_email' => 'MapworksTestingUser00341586@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Senior',
                                    'student_logins' => '10',
                                    'last_activity_date' => '07/27/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4956587',
                                    'student_first_name' => 'Angel',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4956587',
                                    'student_primary_email' => 'MapworksTestingUser01630086@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '2',
                                    'last_activity_date' => '04/06/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4712587',
                                    'student_first_name' => 'Ann',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4712587',
                                    'student_primary_email' => 'MapworksTestingUser01386091@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Senior',
                                    'student_logins' => '7',
                                    'last_activity_date' => '04/13/2017',
                                    'last_activity' => 'Contact',
                                ),
                                array(
                                    'student_id' => '387587',
                                    'student_first_name' => 'Ares',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '387587',
                                    'student_primary_email' => 'MapworksTestingUser00387586@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Senior',
                                    'student_logins' => '5',
                                    'last_activity_date' => '04/19/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4718587',
                                    'student_first_name' => 'Avalynn',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4718587',
                                    'student_primary_email' => 'MapworksTestingUser01392091@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Senior',
                                    'student_logins' => '5',
                                    'last_activity_date' => '04/20/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4676587',
                                    'student_first_name' => 'Azaria',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4676587',
                                    'student_primary_email' => 'MapworksTestingUser01350098@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '5',
                                    'last_activity_date' => '04/19/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '749587',
                                    'student_first_name' => 'Camilo',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '749587',
                                    'student_primary_email' => 'MapworksTestingUser00749586@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Sophomore',
                                    'student_logins' => '3',
                                    'last_activity_date' => '04/11/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '1039587',
                                    'student_first_name' => 'Clara',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '1039587',
                                    'student_primary_email' => 'MapworksBetaUser01039587@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Sophomore',
                                    'student_logins' => '4',
                                    'last_activity_date' => '04/19/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '725587',
                                    'student_first_name' => 'Coleman',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '725587',
                                    'student_primary_email' => 'MapworksTestingUser00725586@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Junior',
                                    'student_logins' => '3',
                                    'last_activity_date' => '04/11/2017',
                                    'last_activity' => 'Appointment',
                                ),)
                    );
                }
                break;
            case 'my_primary_campus_connections';
                if ($onlyIncludeActiveStudents) {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 0,
                        'records_per_page' => '10',
                        'total_pages' => 0,
                        'current_page' => '1',
                        'search_result' =>
                            array(),);
                } else {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 0,
                        'records_per_page' => '10',
                        'total_pages' => 0,
                        'current_page' => '1',
                        'search_result' =>
                            array(),);
                }
                break;
            case 'at_risk_students';
                if ($onlyIncludeActiveStudents) {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 1374,
                        'records_per_page' => '10',
                        'total_pages' => 138,
                        'current_page' => '1',
                        'search_result' =>
                            array(

                                array(
                                    'student_id' => '4954823',
                                    'student_first_name' => 'Alexis',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4954823',
                                    'student_primary_email' => 'MapworksBetaUser04954823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956823',
                                    'student_first_name' => 'Archer',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4956823',
                                    'student_primary_email' => 'MapworksBetaUser04956823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '1',
                                    'last_activity_date' => '07/27/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4955823',
                                    'student_first_name' => 'Madilyn',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4955823',
                                    'student_primary_email' => 'MapworksBetaUser04955823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '2',
                                    'last_activity_date' => '11/11/2016',
                                    'last_activity' => 'Email',
                                ),
                                array(
                                    'student_id' => '4958376',
                                    'student_first_name' => 'Abbigail',
                                    'student_last_name' => 'Acosta',
                                    'external_id' => '4958376',
                                    'student_primary_email' => 'MapworksBetaUser04958376@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956038',
                                    'student_first_name' => 'Madalyn',
                                    'student_last_name' => 'Adams',
                                    'external_id' => '4956038',
                                    'student_primary_email' => 'MapworksBetaUser04956038@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4961212',
                                    'student_first_name' => 'Cayson',
                                    'student_last_name' => 'Aguilar',
                                    'external_id' => '4961212',
                                    'student_primary_email' => 'MapworksBetaUser04961212@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Junior',
                                    'student_logins' => '1',
                                    'last_activity_date' => '11/07/2016',
                                    'last_activity' => 'Contact',
                                ),
                                array(
                                    'student_id' => '4955212',
                                    'student_first_name' => 'Lennon',
                                    'student_last_name' => 'Aguilar',
                                    'external_id' => '4955212',
                                    'student_primary_email' => 'MapworksBetaUser04955212@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4953507',
                                    'student_first_name' => 'Yehuda',
                                    'student_last_name' => 'Aguirre',
                                    'external_id' => '4953507',
                                    'student_primary_email' => 'MapworksBetaUser04953507@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '1',
                                    'last_activity_date' => '07/27/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4956110',
                                    'student_first_name' => 'Hattie',
                                    'student_last_name' => 'Alexander',
                                    'external_id' => '4956110',
                                    'student_primary_email' => 'MapworksBetaUser04956110@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4954875',
                                    'student_first_name' => 'Pedro',
                                    'student_last_name' => 'Ali',
                                    'external_id' => '4954875',
                                    'student_primary_email' => 'MapworksBetaUser04954875@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'yellow',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),)
                    );
                } else {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 3298,
                        'records_per_page' => '10',
                        'total_pages' => 330,
                        'current_page' => '1',
                        'search_result' =>
                            array(

                                array(
                                    'student_id' => '4673587',
                                    'student_first_name' => 'Kymani',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4673587',
                                    'student_primary_email' => 'MapworksBetaUser04673587@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '5',
                                    'last_activity_date' => '04/21/2017',
                                    'last_activity' => 'Referral',
                                ),
                                array(
                                    'student_id' => '4954823',
                                    'student_first_name' => 'Alexis',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4954823',
                                    'student_primary_email' => 'MapworksBetaUser04954823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956823',
                                    'student_first_name' => 'Archer',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4956823',
                                    'student_primary_email' => 'MapworksBetaUser04956823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '1',
                                    'last_activity_date' => '07/27/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4955823',
                                    'student_first_name' => 'Madilyn',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4955823',
                                    'student_primary_email' => 'MapworksBetaUser04955823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '2',
                                    'last_activity_date' => '11/11/2016',
                                    'last_activity' => 'Email',
                                ),
                                array(
                                    'student_id' => '4958376',
                                    'student_first_name' => 'Abbigail',
                                    'student_last_name' => 'Acosta',
                                    'external_id' => '4958376',
                                    'student_primary_email' => 'MapworksBetaUser04958376@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4678376',
                                    'student_first_name' => 'Isabela',
                                    'student_last_name' => 'Acosta',
                                    'external_id' => '4678376',
                                    'student_primary_email' => 'MapworksBetaUser04678376@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4679376',
                                    'student_first_name' => 'Nickolas',
                                    'student_last_name' => 'Acosta',
                                    'external_id' => '4679376',
                                    'student_primary_email' => 'MapworksBetaUser04679376@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4675038',
                                    'student_first_name' => 'Abram',
                                    'student_last_name' => 'Adams',
                                    'external_id' => '4675038',
                                    'student_primary_email' => 'MapworksBetaUser04675038@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4674038',
                                    'student_first_name' => 'Lia',
                                    'student_last_name' => 'Adams',
                                    'external_id' => '4674038',
                                    'student_primary_email' => 'MapworksBetaUser04674038@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956038',
                                    'student_first_name' => 'Madalyn',
                                    'student_last_name' => 'Adams',
                                    'external_id' => '4956038',
                                    'student_primary_email' => 'MapworksBetaUser04956038@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),));
                }
                break;
            case 'high_intent_to_leave';
                if ($onlyIncludeActiveStudents) {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 75,
                        'records_per_page' => '10',
                        'total_pages' => 8,
                        'current_page' => '1',
                        'search_result' =>
                            array(

                                array(
                                    'student_id' => '4957618',
                                    'student_first_name' => 'Zoey',
                                    'student_last_name' => 'Atkins',
                                    'external_id' => '4957618',
                                    'student_primary_email' => 'MapworksBetaUser04957618@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4958829',
                                    'student_first_name' => 'Dominick',
                                    'student_last_name' => 'Bautista',
                                    'external_id' => '4958829',
                                    'student_primary_email' => 'MapworksBetaUser04958829@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4961854',
                                    'student_first_name' => 'Leslie',
                                    'student_last_name' => 'Best',
                                    'external_id' => '4961854',
                                    'student_primary_email' => 'MapworksBetaUser04961854@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => 'Senior',
                                    'student_logins' => '1',
                                    'last_activity_date' => '10/06/2016',
                                    'last_activity' => 'Note',
                                ),
                                array(
                                    'student_id' => '4954825',
                                    'student_first_name' => 'Anderson',
                                    'student_last_name' => 'Blackburn',
                                    'external_id' => '4954825',
                                    'student_primary_email' => 'MapworksBetaUser04954825@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'yellow',
                                    'student_risk_image_name' => 'risk-level-icon-y.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4958730',
                                    'student_first_name' => 'Xander',
                                    'student_last_name' => 'Blankenship',
                                    'external_id' => '4958730',
                                    'student_primary_email' => 'MapworksBetaUser04958730@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956855',
                                    'student_first_name' => 'Aden',
                                    'student_last_name' => 'Blevins',
                                    'external_id' => '4956855',
                                    'student_primary_email' => 'MapworksBetaUser04956855@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4954901',
                                    'student_first_name' => 'Solomon',
                                    'student_last_name' => 'Booker',
                                    'external_id' => '4954901',
                                    'student_primary_email' => 'MapworksBetaUser04954901@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4954228',
                                    'student_first_name' => 'Zaniyah',
                                    'student_last_name' => 'Burke',
                                    'external_id' => '4954228',
                                    'student_primary_email' => 'MapworksBetaUser04954228@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4958097',
                                    'student_first_name' => 'Cameron',
                                    'student_last_name' => 'Butler',
                                    'external_id' => '4958097',
                                    'student_primary_email' => 'MapworksBetaUser04958097@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4958419',
                                    'student_first_name' => 'Bryleigh',
                                    'student_last_name' => 'Campos',
                                    'external_id' => '4958419',
                                    'student_primary_email' => 'MapworksBetaUser04958419@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),));
                } else {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 202,
                        'records_per_page' => '10',
                        'total_pages' => 21,
                        'current_page' => '1',
                        'search_result' =>
                            array(

                                array(
                                    'student_id' => '4956587',
                                    'student_first_name' => 'Angel',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4956587',
                                    'student_primary_email' => 'MapworksTestingUser01630086@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '2',
                                    'last_activity_date' => '04/06/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '1037851',
                                    'student_first_name' => 'Myla',
                                    'student_last_name' => 'Ashley',
                                    'external_id' => '1037851',
                                    'student_primary_email' => 'MapworksTestingUser01037850@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'gray',
                                    'student_risk_image_name' => 'risk-level-icon-gray.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => 'Sophomore',
                                    'student_logins' => '3',
                                    'last_activity_date' => '09/23/2014',
                                    'last_activity' => 'Contact',
                                ),
                                array(
                                    'student_id' => '4957618',
                                    'student_first_name' => 'Zoey',
                                    'student_last_name' => 'Atkins',
                                    'external_id' => '4957618',
                                    'student_primary_email' => 'MapworksBetaUser04957618@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4675240',
                                    'student_first_name' => 'Johan',
                                    'student_last_name' => 'Austin',
                                    'external_id' => '4675240',
                                    'student_primary_email' => 'MapworksBetaUser04675240@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4673448',
                                    'student_first_name' => 'Jamie',
                                    'student_last_name' => 'Ayala',
                                    'external_id' => '4673448',
                                    'student_primary_email' => 'MapworksBetaUser04673448@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4677037',
                                    'student_first_name' => 'Abram',
                                    'student_last_name' => 'Baker',
                                    'external_id' => '4677037',
                                    'student_primary_email' => 'MapworksBetaUser04677037@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4675502',
                                    'student_first_name' => 'Brent',
                                    'student_last_name' => 'Ballard',
                                    'external_id' => '4675502',
                                    'student_primary_email' => 'MapworksBetaUser04675502@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'green',
                                    'student_risk_image_name' => 'risk-level-icon-g.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956375',
                                    'student_first_name' => 'Alissa',
                                    'student_last_name' => 'Barber',
                                    'external_id' => '4956375',
                                    'student_primary_email' => 'MapworksBetaUser04956375@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'yellow',
                                    'student_risk_image_name' => 'risk-level-icon-y.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4678829',
                                    'student_first_name' => 'Alan',
                                    'student_last_name' => 'Bautista',
                                    'external_id' => '4678829',
                                    'student_primary_email' => 'MapworksBetaUser04678829@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4958829',
                                    'student_first_name' => 'Dominick',
                                    'student_last_name' => 'Bautista',
                                    'external_id' => '4958829',
                                    'student_primary_email' => 'MapworksBetaUser04958829@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'red',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),));
                }
                break;
            case 'high_priority_students';
                if ($onlyIncludeActiveStudents) {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 1373,
                        'records_per_page' => '10',
                        'total_pages' => 138,
                        'current_page' => '1',
                        'search_result' =>
                            array(

                                array(
                                    'student_id' => '4954823',
                                    'student_first_name' => 'Alexis',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4954823',
                                    'student_primary_email' => 'MapworksBetaUser04954823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956823',
                                    'student_first_name' => 'Archer',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4956823',
                                    'student_primary_email' => 'MapworksBetaUser04956823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '1',
                                    'last_activity_date' => '07/27/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4955823',
                                    'student_first_name' => 'Madilyn',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4955823',
                                    'student_primary_email' => 'MapworksBetaUser04955823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '2',
                                    'last_activity_date' => '11/11/2016',
                                    'last_activity' => 'Email',
                                ),
                                array(
                                    'student_id' => '4958376',
                                    'student_first_name' => 'Abbigail',
                                    'student_last_name' => 'Acosta',
                                    'external_id' => '4958376',
                                    'student_primary_email' => 'MapworksBetaUser04958376@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956038',
                                    'student_first_name' => 'Madalyn',
                                    'student_last_name' => 'Adams',
                                    'external_id' => '4956038',
                                    'student_primary_email' => 'MapworksBetaUser04956038@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4961212',
                                    'student_first_name' => 'Cayson',
                                    'student_last_name' => 'Aguilar',
                                    'external_id' => '4961212',
                                    'student_primary_email' => 'MapworksBetaUser04961212@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => 'Junior',
                                    'student_logins' => '1',
                                    'last_activity_date' => '11/07/2016',
                                    'last_activity' => 'Contact',
                                ),
                                array(
                                    'student_id' => '4955212',
                                    'student_first_name' => 'Lennon',
                                    'student_last_name' => 'Aguilar',
                                    'external_id' => '4955212',
                                    'student_primary_email' => 'MapworksBetaUser04955212@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4953507',
                                    'student_first_name' => 'Yehuda',
                                    'student_last_name' => 'Aguirre',
                                    'external_id' => '4953507',
                                    'student_primary_email' => 'MapworksBetaUser04953507@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '1',
                                    'last_activity_date' => '07/27/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4956110',
                                    'student_first_name' => 'Hattie',
                                    'student_last_name' => 'Alexander',
                                    'external_id' => '4956110',
                                    'student_primary_email' => 'MapworksBetaUser04956110@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4954875',
                                    'student_first_name' => 'Pedro',
                                    'student_last_name' => 'Ali',
                                    'external_id' => '4954875',
                                    'student_primary_email' => 'MapworksBetaUser04954875@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'yellow',
                                    'student_intent_to_leave_image_name' => 'leave-intent-leave-implied.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),),);
                } else {
                    $metadata = array(
                        'person_id' => '5048809',
                        'total_records' => 3292,
                        'records_per_page' => '10',
                        'total_pages' => 330,
                        'current_page' => '1',
                        'search_result' =>
                            array(

                                array(
                                    'student_id' => '4673587',
                                    'student_first_name' => 'Kymani',
                                    'student_last_name' => 'Abbott',
                                    'external_id' => '4673587',
                                    'student_primary_email' => 'MapworksBetaUser04673587@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '5',
                                    'last_activity_date' => '04/21/2017',
                                    'last_activity' => 'Referral',
                                ),
                                array(
                                    'student_id' => '4954823',
                                    'student_first_name' => 'Alexis',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4954823',
                                    'student_primary_email' => 'MapworksBetaUser04954823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956823',
                                    'student_first_name' => 'Archer',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4956823',
                                    'student_primary_email' => 'MapworksBetaUser04956823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '1',
                                    'last_activity_date' => '07/27/2017',
                                    'last_activity' => 'Appointment',
                                ),
                                array(
                                    'student_id' => '4955823',
                                    'student_first_name' => 'Madilyn',
                                    'student_last_name' => 'Acevedo',
                                    'external_id' => '4955823',
                                    'student_primary_email' => 'MapworksBetaUser04955823@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '2',
                                    'last_activity_date' => '11/11/2016',
                                    'last_activity' => 'Email',
                                ),
                                array(
                                    'student_id' => '4958376',
                                    'student_first_name' => 'Abbigail',
                                    'student_last_name' => 'Acosta',
                                    'external_id' => '4958376',
                                    'student_primary_email' => 'MapworksBetaUser04958376@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4678376',
                                    'student_first_name' => 'Isabela',
                                    'student_last_name' => 'Acosta',
                                    'external_id' => '4678376',
                                    'student_primary_email' => 'MapworksBetaUser04678376@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4679376',
                                    'student_first_name' => 'Nickolas',
                                    'student_last_name' => 'Acosta',
                                    'external_id' => '4679376',
                                    'student_primary_email' => 'MapworksBetaUser04679376@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4675038',
                                    'student_first_name' => 'Abram',
                                    'student_last_name' => 'Adams',
                                    'external_id' => '4675038',
                                    'student_primary_email' => 'MapworksBetaUser04675038@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4674038',
                                    'student_first_name' => 'Lia',
                                    'student_last_name' => 'Adams',
                                    'external_id' => '4674038',
                                    'student_primary_email' => 'MapworksBetaUser04674038@mailinator.com',
                                    'student_status' => '0',
                                    'student_risk_status' => 'red2',
                                    'student_risk_image_name' => 'risk-level-icon-r2.png',
                                    'student_intent_to_leave' => 'dark gray',
                                    'student_intent_to_leave_image_name' => 'intent-to-leave-icons-dark-grey.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                ),
                                array(
                                    'student_id' => '4956038',
                                    'student_first_name' => 'Madalyn',
                                    'student_last_name' => 'Adams',
                                    'external_id' => '4956038',
                                    'student_primary_email' => 'MapworksBetaUser04956038@mailinator.com',
                                    'student_status' => '1',
                                    'student_risk_status' => 'red',
                                    'student_risk_image_name' => 'risk-level-icon-r1.png',
                                    'student_intent_to_leave' => 'green',
                                    'student_intent_to_leave_image_name' => 'leave-intent-stay-stated.png',
                                    'student_classlevel' => '1st Year/Freshman',
                                    'student_logins' => '0',
                                    'last_activity_date' => NULL,
                                    'last_activity' => NULL,
                                )));
                }
                break;
        }
        return $metadata;
    }
}