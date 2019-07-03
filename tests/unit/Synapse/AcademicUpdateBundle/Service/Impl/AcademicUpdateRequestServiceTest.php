<?php
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto;
use Synapse\AcademicUpdateBundle\EntityDto\CoursesDto;
use Synapse\AcademicUpdateBundle\EntityDto\GroupsDto;
use Synapse\AcademicUpdateBundle\EntityDto\ProfileDto;
use Synapse\AcademicUpdateBundle\EntityDto\StaffDto;
use Synapse\AcademicUpdateBundle\EntityDto\StaticListDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentsDto;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateService;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateRequestService;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\ReportsBundle\Service\Impl\ReportDrilldownService;

class AcademicUpdateRequestServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    /**
     * @var \DateTime
     */
    private $requestDueDate ;

    /**
     * @expectedException  \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testInitiateAcademicUpdateRequestJob()
    {
        $this->specify("Initiate Academic Update Request Job", function ($loggedInPersonId, $selectedAttributes)
        {
            $this->requestDueDate = $selectedAttributes['request_due_date'];
            $this->requestDueDate = $this->requestDueDate->add(new DateInterval('P2D'));
            $selectedAttributes['request_due_date'] = $selectedAttributes['request_due_date']->format('m/d/Y');

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);

            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mocking Services

            // Mocking Validator Service
            $mockValidatorService = $this->getMock('Validator', ['validate']);

            // Mocking Resque Object
            $mockResque = $this->getMock('resque', ['enqueue']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::VALIDATOR, $mockValidatorService],
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque],
                ]);

            // Mocking Repositories

            // Mocking Person Object
            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization']);
            // Mocking Organization Object
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($selectedAttributes['organization_id']);
            $mockPerson->method('getId')->willReturn($loggedInPersonId);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);

            // Mocking OrganizationRole Object
            $mockOrganizationRole = $this->getMock('OrganizationRole', ['getId']);
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRole);

            $mockOrgAcademicYearRepository = $this->getMock("OrgAcademicYearRepository", ['getCountCurrentAcademic']);
            $currentAcademicCount = ['oayCount' => 20];
            $mockOrgAcademicYearRepository->method('getCountCurrentAcademic')->willReturn([$currentAcademicCount]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                ]
            );

            $academicUpdateCreateDto = $this->getAcademicUpdateCreateDto($selectedAttributes);
            $academicUpdateRequestService = new AcademicUpdateRequestService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $academicUpdateCreateDtoDetails = $academicUpdateRequestService->initiateAcademicUpdateRequestJob($academicUpdateCreateDto, $mockPerson);
            $this->assertInstanceOf("Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto", $academicUpdateCreateDtoDetails);
            $this->assertEquals($academicUpdateCreateDtoDetails->getOrganizationId(), $selectedAttributes['organization_id']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestDescription(), $selectedAttributes['request_description']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestDueDate()->format('m/d/Y'), $selectedAttributes['request_due_date']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestEmailSubject(), $selectedAttributes['request_email_subject']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestEmailOptionalMessage(), $selectedAttributes['request_email_optional_message']);

        }, [
            'examples' => [

                // Setting students, staff and static_list value as true and groups, courses, profile_items as false
                [
                   95,
                    [
                        "organization_id" =>"2",
                        "request_name" => "Testing academic update",
                        "request_description" => "Testing academic update",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> true,
                            "selected_student_ids"=> ""
                        ],
                        "staff"=> [
                            "is_all"=> "1",
                            "selected_staff_ids"=> ""
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> ""
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> "1",
                            "selected_static_ids"=> ""
                        ]
                    ]
                ],
                // Setting selected value for students and staff along with groups, courses, profile_items as false and static_list value as true
                [
                    98,
                    [
                        "organization_id" =>"9",
                        "request_name" => "Testing academic for selected students and staffs",
                        "request_description" => "Testing academic for selected students",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> false,
                            "selected_student_ids"=> "1,2,3"
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123,456"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> ""
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> "1",
                            "selected_static_ids"=> ""
                        ]
                    ]
                ],
                // Setting selected value for students, staff, groups and courses along with profile_items as false and static_list as true
                [
                    98,
                    [
                        "organization_id" =>"9",
                        "request_name" => "Testing academic for selected courses",
                        "request_description" => "Testing academic for selected courses",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> false,
                            "selected_student_ids"=> "1,2,3"
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123,456"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> "901"
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> "678,910"
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> "1",
                            "selected_static_ids"=> ""
                        ]
                    ]
                ],
                // Setting selected value for students, staff and courses along with groups, profile_items as false and static_list as true
                // Invalid organization will throw SynapseValidationException
                [
                    98,
                    [
                        "organization_id" =>"-1",
                        "request_name" => "Testing academic for selected courses",
                        "request_description" => "Testing academic for selected courses",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> false,
                            "selected_student_ids"=> "1,2,3"
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123,456"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> "678,910"
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> "1",
                            "selected_static_ids"=> ""
                        ]
                    ]
                ]
            ]
        ]);
    }

    private function getAcademicUpdateCreateDto($selectedAttributes)
    {
        $academicUpdateCreateDto = new AcademicUpdateCreateDto();
        $selectedAttributes['organization_id'] = ($selectedAttributes['organization_id'] != -1) ? $selectedAttributes['organization_id'] : 0;
        $academicUpdateCreateDto->setOrganizationId($selectedAttributes['organization_id']);
        $academicUpdateCreateDto->setRequestName($selectedAttributes['request_name']);
        $academicUpdateCreateDto->setRequestDescription($selectedAttributes['request_description']);
        $requestDueDate = new \DateTime($selectedAttributes["request_due_date"]);
        $academicUpdateCreateDto->setRequestDueDate($requestDueDate);
        $academicUpdateCreateDto->setRequestEmailSubject($selectedAttributes['request_email_subject']);
        $academicUpdateCreateDto->setRequestEmailOptionalMessage($selectedAttributes['request_email_optional_message']);
        $academicUpdateCreateDto->setStudents($this->getStudentDto($selectedAttributes['students']));
        $academicUpdateCreateDto->setStaff($this->getStaffDto($selectedAttributes['staff']));
        $academicUpdateCreateDto->setGroups($this->getGroupsDto($selectedAttributes['groups']));
        $academicUpdateCreateDto->setCourses($this->getCoursesDto($selectedAttributes['courses']));
        $academicUpdateCreateDto->setProfileItems($this->getProfileDto($selectedAttributes['profile_items']));
        $academicUpdateCreateDto->setStaticList($this->getStaticListDto($selectedAttributes['static_list']));
        return $academicUpdateCreateDto;
    }

    private function getStudentDto($selectedAttributesForStudents)
    {
        $studentsDto = new StudentsDto();
        $studentsDto->setIsAll($selectedAttributesForStudents['is_all']);
        $studentsDto->setSelectedStudentIds($selectedAttributesForStudents['selected_student_ids']);
        return $studentsDto;
    }

    private function getStaffDto($selectedAttributesForStaff)
    {
        $staffDto = new StaffDto();
        $staffDto->setIsAll($selectedAttributesForStaff['is_all']);
        $staffDto->setSelectedStaffIds($selectedAttributesForStaff['selected_staff_ids']);
        return $staffDto;
    }

    private function getGroupsDto($selectedAttributesForGroups)
    {
        $groupsDto = new GroupsDto();
        $groupsDto->setIsAll($selectedAttributesForGroups['is_all']);
        $groupsDto->setSelectedGroupIds($selectedAttributesForGroups['selected_group_ids']);
        return $groupsDto;
    }

    private function getCoursesDto($selectedAttributesForCourses)
    {
        $coursesDto = new CoursesDto();
        $coursesDto->setIsAll($selectedAttributesForCourses['is_all']);
        $coursesDto->setSelectedCourseIds($selectedAttributesForCourses['selected_course_ids']);
        return $coursesDto;
    }

    private function getProfileDto($selectedAttributesForProfiles)
    {
        $profileDto = new ProfileDto();
        $profileDto->setIsps($selectedAttributesForProfiles['isps']);
        $profileDto->setEbi($selectedAttributesForProfiles['ebi']);
        return $profileDto;
    }

    private function getStaticListDto($selectedAttributesForStaticLists)
    {
        $staticListDto = new StaticListDto();
        $staticListDto->setIsAll($selectedAttributesForStaticLists['is_all']);
        $staticListDto->setSelectedStaticIds($selectedAttributesForStaticLists['selected_static_ids']);
        return $staticListDto;
    }

    /**
     * @expectedException  \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testCreateAcademicUpdateRequest()
    {
        $this->specify("Create Academic Update Request", function ($loggedInPersonId, $selectedAttributes)
        {
            $this->requestDueDate = $selectedAttributes['request_due_date'];
            $this->requestDueDate  = $this->requestDueDate->add(new DateInterval('P2D'));
            $selectedAttributes['request_due_date'] = $selectedAttributes['request_due_date']->format('m/d/Y');

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);

            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mocking Services

            $mockDateUtilityService = $this->getMock('DateUtilityService', ['adjustOrganizationDateTimeStringToUtcDateTimeObject']);
            $mockDateUtilityService->method('adjustOrganizationDateTimeStringToUtcDateTimeObject')->willReturn($this->requestDueDate);

            $mockAcademicUpdateService = $this->getMock('academicUpdateService', ['updateAcademicUpdateDataFile']);
            $mockAcademicUpdateService->method('updateAcademicUpdateDataFile')->willReturn('');

            $mockReportDrilldownService = $this->getMock('reportDrilldownService', ['getIndividuallyAccessibleParticipants']);

            $mockAcademicYearService = $this->getMock('AcademicYearService', ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(123);

            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl']);
            $mockEbiConfigService->method('getSystemUrl')->willReturn('www.skyfactor.com');

            // Mocking EmailService
            $mockEmailService = $this->getMock('EmailService', ['generateEmailMessage', 'sendEmailNotification', 'sendEmail']);
            $mockEmailService->method('generateEmailMessage')->willReturn('This is email body');

            //Mocking alertNotificationsService
            $mockAlertNotificationsService = $this->getMock('AlertNotificationsService', ['createNotification']);
            $mockAlertNotificationsService->method('createNotification')->willReturn('');

            // Mocking Resque Object
            $mockResque = $this->getMock('resque', ['enqueue']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [DateUtilityService::SERVICE_KEY, $mockDateUtilityService],
                    [ReportDrilldownService::SERVICE_KEY, $mockReportDrilldownService],
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [EmailService::SERVICE_KEY, $mockEmailService],
                    [AlertNotificationsService::SERVICE_KEY, $mockAlertNotificationsService],
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque],
                    [AcademicUpdateService::SERVICE_KEY, $mockAcademicUpdateService]
                ]);

            // Mocking Repositories

            // Mocking Organization Object
            $mockOrganization = $this->getMock('\Synapse\CoreBundle\Entity\Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($selectedAttributes['organization_id']);
            // Mocking Person Object
            $mockPerson = $this->getMock('\Synapse\CoreBundle\Entity\Person', ['getId', 'getOrganization']);
            $mockPerson->method('getId')->willReturn($loggedInPersonId);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);

            $personDetailsArray = [['user_id' => 123, 'username' => 'newnew@mailinator.com']];
            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'getId', 'getUsersByUserIds']);
            $mockPersonRepository->method('find')->willReturn($mockPerson);
            $mockPersonRepository->method('getUsersByUserIds')->willReturn($personDetailsArray);

            // Mocking OrganizationRole Object
            $mockOrganizationRole = $this->getMock('OrganizationRole', ['getId']);

            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRole);

            $currentAcademicCount = ['oayCount' => 20];
            $mockOrgAcademicYearRepository = $this->getMock("OrgAcademicYearRepository", ['getCountCurrentAcademic', 'find']);
            $mockOrgAcademicYearRepository->method('getCountCurrentAcademic')->willReturn([$currentAcademicCount]);

            // Mocking AcademicUpdateRequest Object
            $mockAcademicUpdateRequest = $this->getMock('\Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest', ['getId', 'getPerson', 'getSelectStudent']);
            $mockAcademicUpdateRequest->method('getPerson')->willReturn($mockPerson);

            $mockAcademicUpdateRequestRepository = $this->getMock('AcademicUpdateRequestRepository', ['persist', 'flush']);
            $mockAcademicUpdateRequestRepository->method('persist')->willReturn($mockAcademicUpdateRequest);

            $studentList = [['student_id' => 1], ['student_id' => 2], ['student_id' => 3], ['student_id' => 4]];
            $courseForStudent = [['person_id' => 123, 'org_courses_id' => 543]];
            $courseForStudentDifferentKeys = [['studentId' => 123, 'courseId' => 543]];
            $mockOrgCourseStudentRepository = $this->getMock('OrgCourseStudentRepository', ['getStudentsInAnyCurrentCourse', 'getCoursesForStudent', 'getStudentsByCourse']);
            $mockOrgCourseStudentRepository->method('getStudentsInAnyCurrentCourse')->willReturn($studentList);
            $mockOrgCourseStudentRepository->method('getCoursesForStudent')->willReturn($courseForStudent);
            $mockOrgCourseStudentRepository->method('getStudentsByCourse')->willReturn($courseForStudentDifferentKeys);

            // Mocking OrgCourseFaculty Object
            $mockOrgCourseFaculty = $this->getMock('OrgCourseFaculty', ['getId']);
            $coursesForStaffArray = [['facultyId' => 123, 'courseId' => 456]];
            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', ['getFacultiesForCourse', 'getCoursesForStaff']);
            $mockOrgCourseFacultyRepository->method('getFacultiesForCourse')->willReturn($mockOrgCourseFaculty);
            $mockOrgCourseFacultyRepository->method('getCoursesForStaff')->willReturn($coursesForStaffArray);

            // Mocking OrgCourses Object
            $mockOrgCourses = $this->getMock('\Synapse\AcademicBundle\Entity\OrgCourses', ['getId']);
            $mockOrgCoursesRepository = $this->getMock('OrgCoursesRepository', ['find']);
            $mockOrgCoursesRepository->method('find')->willReturn($mockOrgCourses);

            $staffList = [['staff_id' => 123], ['staff_id' => 456]];
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['getFacultiesByOrganizationCourse']);
            $mockOrgPersonFacultyRepository->method('getFacultiesByOrganizationCourse')->willReturn($staffList);

            $mockOrgGroupStudentsRepository = $this->getMock('OrgGroupStudentsRepository', ['getNonArchivedStudentsByGroups']);
            $mockOrgGroupStudentsRepository->method('getNonArchivedStudentsByGroups')->willReturn([]);

            //Mocking OrgStaticList Object
            $mockOrgStaticList = $this->getMock('\Synapse\StaticListBundle\Entity\OrgStaticList', ['getId']);
            $mockOrgStaticListRepository = $this->getMock('OrgStaticListRepository', ['getAllStaticLists', 'getStaticListReferance', 'persist']);
            $mockOrgStaticListRepository->method('getAllStaticLists')->willReturn(['id' => 123]);
            $mockOrgStaticListRepository->method('getStaticListReferance')->willReturn($mockOrgStaticList);


            $mockOrgStaticListStudentsRepository = $this->getMock('OrgStaticListStudentsRepository', ['getStudentsByList']);
            $mockOrgStaticListStudentsRepository->method('getStudentsByList')->willReturn([]);

            //Mocking AcademicUpdate Object

            $mockAcademicUpdate = $this->getMock('\Synapse\AcademicUpdateBundle\Entity\AcademicUpdate', ['getId']);
            $mockAcademicUpdateRepository = $this->getMock('AcademicUpdateRepository', ['flush', 'findOneBy']);
            $mockAcademicUpdateRepository->method('findOneBy')->willReturn($mockAcademicUpdate);

            // Mocking EbiConfig Object
            $mockEbiConfig = $this->getMock('EbiConfig', ['getId', 'getValue']);
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockEbiConfigRepository->method('findOneBy')->willReturn($mockEbiConfig);

            //Mocking EmailTemplateLang Object
            $mockEmailTemplateLang = $this->getMock('EmailTemplateLang', ['getId', 'getBody', 'getEmailTemplate']);
            //Mocking EmailTemplate Object
            $mockEmailTemplate = $this->getMock('EmailTemplate', ['getFromEmailAddress', 'getBccRecipientList']);
            $mockEmailTemplateLang->method('getEmailTemplate')->willReturn($mockEmailTemplate);
            $mockEmailTemplate->method('getFromEmailAddress')->willReturn('faculty@mailinator.com');
            $mockEmailTemplateLangRepository = $this->getMock('EmailTemplateLangRepository', ['getEmailTemplateByKey']);
            $mockEmailTemplateLangRepository->method('getEmailTemplateByKey')->willReturn($mockEmailTemplateLang);

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [AcademicUpdateRequestRepository::REPOSITORY_KEY, $mockAcademicUpdateRequestRepository],
                    [OrgCourseStudentRepository::REPOSITORY_KEY, $mockOrgCourseStudentRepository],
                    [OrgCourseFacultyRepository::REPOSITORY_KEY, $mockOrgCourseFacultyRepository],
                    [OrgCoursesRepository::REPOSITORY_KEY, $mockOrgCoursesRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository],
                    [OrgGroupStudentsRepository::REPOSITORY_KEY, $mockOrgGroupStudentsRepository],
                    [OrgStaticListRepository::REPOSITORY_KEY, $mockOrgStaticListRepository],
                    [OrgStaticListStudentsRepository::REPOSITORY_KEY, $mockOrgStaticListStudentsRepository],
                    [AcademicUpdateRepository::REPOSITORY_KEY, $mockAcademicUpdateRepository],
                    [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository],
                    [EmailTemplateLangRepository::REPOSITORY_KEY, $mockEmailTemplateLangRepository],
                ]
            );

            $academicUpdateCreateDto = $this->getAcademicUpdateCreateDto($selectedAttributes);
            $academicUpdateRequestService = new AcademicUpdateRequestService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $academicUpdateCreateDtoDetails = $academicUpdateRequestService->createAcademicUpdateRequest($academicUpdateCreateDto, $mockPerson->getId());
            $this->assertInstanceOf("Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto", $academicUpdateCreateDtoDetails);
            $this->assertEquals($academicUpdateCreateDtoDetails->getOrganizationId(), $selectedAttributes['organization_id']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestDescription(), $selectedAttributes['request_description']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestDueDate()->format('m/d/Y'), $selectedAttributes['request_due_date']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestEmailSubject(), $selectedAttributes['request_email_subject']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestEmailOptionalMessage(), $selectedAttributes['request_email_optional_message']);

        }, [
            'examples' => [
                // Setting value for students, staff as true and groups, courses, profile_items, static_list as false
                [
                    95,
                    [
                        "organization_id" =>"2",
                        "request_name" => "Testing academic update for all staff",
                        "request_description" => "Testing academic update for all staff",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> true,
                            "selected_student_ids"=> ""
                        ],
                        "staff"=> [
                            "is_all"=> "1",
                            "selected_staff_ids"=> ""
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> ""
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> false,
                            "selected_static_ids"=> ""
                        ]
                    ]
                ],
                // Setting selected value for students, staff along with  groups, courses, profile_items, static_list as false
                [
                    96,
                    [
                        "organization_id" =>"3",
                        "request_name" => "Testing academic Update",
                        "request_description" => "Testing academic Update",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> false,
                            "selected_student_ids"=> "1,2,3"
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> ""
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> false,
                            "selected_static_ids"=> ""
                        ]
                    ]
                ],
                // Setting selected value for students, staff, static_list along with  groups, courses, profile_items as false
                [
                    97,
                    [
                        "organization_id" =>"3",
                        "request_name" => "Testing academic Update for static List",
                        "request_description" => "Testing academic for static list",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> false,
                            "selected_student_ids"=> "1,2,3"
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> ""
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> false,
                            "selected_static_ids"=> "901,902"
                        ]
                    ]
                ],
                // Setting selected value for students, staff, courses along with groups, profile_items as false and static_list as true
                // Invalid organization will throw SynapseValidationException
                [
                    98,
                    [
                        "organization_id" =>"-1",
                        "request_name" => "Testing academic for selected courses",
                        "request_description" => "Testing academic for selected courses",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> false,
                            "selected_student_ids"=> "1,2,3"
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123,456"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> "678,910"
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> "1",
                            "selected_static_ids"=> ""
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testGetAcademicUpdateCountForRequest()
    {
        $this->specify("Get an academic update count for the request", function ($loggedInPersonId, $selectedAttributes)
        {
            $this->requestDueDate = $selectedAttributes['request_due_date'];
            $this->requestDueDate = $this->requestDueDate->add(new DateInterval('P2D'));
            $selectedAttributes['request_due_date'] = $selectedAttributes['request_due_date']->format('m/d/Y');

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);

            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);

            // Mocking Services

            // Mocking Validator Service
            $mockValidatorService = $this->getMock('Validator', ['validate']);

            // Mocking Resque Object
            $mockResque = $this->getMock('resque', ['enqueue']);

            // Mocking ReportDrilldownService
            $mockReportDrilldownService = $this->getMock('reportDrilldownService', ['getIndividuallyAccessibleParticipants']);

            // Mocking AcademicYearService Object
            $mockAcademicYearService = $this->getMock('AcademicYearService', ['getCurrentOrgAcademicYearId']);
            $mockAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn(123);

            $mockContainer->method('get')
                ->willReturnMap([
                    [SynapseConstant::VALIDATOR, $mockValidatorService],
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque],
                    [ReportDrilldownService::SERVICE_KEY, $mockReportDrilldownService],
                    [AcademicYearService::SERVICE_KEY, $mockAcademicYearService],
                ]);

            // Mocking Repositories

            // Mocking Person Object
            $mockPerson = $this->getMock('\Synapse\CoreBundle\Entity\Person', ['getId', 'getOrganization']);
            // Mocking Organization Object
            $mockOrganization = $this->getMock('\Synapse\CoreBundle\Entity\Organization', ['getId']);
            $mockOrganization->method('getId')->willReturn($selectedAttributes['organization_id']);
            $mockPerson->method('getId')->willReturn($loggedInPersonId);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);

            // Mocking OrganizationRole Object
            $mockOrganizationRole = $this->getMock('OrganizationRole', ['getId']);
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['findOneBy']);
            $mockOrganizationRoleRepository->method('findOneBy')->willReturn($mockOrganizationRole);

            $mockOrgAcademicYearRepository = $this->getMock("OrgAcademicYearRepository", ['getCountCurrentAcademic']);
            $currentAcademicCount = ['oayCount' => 20];
            $mockOrgAcademicYearRepository->method('getCountCurrentAcademic')->willReturn([$currentAcademicCount]);

            $studentList = [['student_id' => 1], ['student_id' => 2], ['student_id' => 3], ['student_id' => 4]];
            $mockOrgCourseStudentRepository = $this->getMock('OrgCourseStudentRepository', ['getStudentsInAnyCurrentCourse', 'getCoursesForStudent', 'getStudentsByCourse']);
            $mockOrgCourseStudentRepository->method('getStudentsInAnyCurrentCourse')->willReturn($studentList);

            $courseForStudent = ['StudentID' => 123, 'courseId' => 543];
            $mockOrgCourseStudentRepository->method('getCoursesForStudent')->willReturn($courseForStudent);
            $mockOrgCourseStudentRepository->method('getStudentsByCourse')->willReturn($courseForStudent);

            $staffList = [['staff_id' => 123], ['staff_id' => 456]];
            $mockOrgPersonFacultyRepository = $this->getMock('OrgPersonFacultyRepository', ['getFacultiesByOrganizationCourse']);
            $mockOrgPersonFacultyRepository->method('getFacultiesByOrganizationCourse')->willReturn($staffList);

            // Mocking OrgCourseFaculty Object
            $coursesForStaffArray = [['facultyId' => 123, 'courseId' => 456]];
            $mockOrgCourseFacultyRepository = $this->getMock('OrgCourseFacultyRepository', ['getFacultiesForCourse', 'getCoursesForStaff']);
            $mockOrgCourseFacultyRepository->method('getCoursesForStaff')->willReturn($coursesForStaffArray);

            $mockOrgGroupStudentsRepository = $this->getMock('OrgGroupStudentsRepository', ['getNonArchivedStudentsByGroups']);
            $mockOrgGroupStudentsRepository->method('getNonArchivedStudentsByGroups')->willReturn([]);

            //Mocking OrgStaticList Object
            $mockOrgStaticList = $this->getMock('\Synapse\StaticListBundle\Entity\OrgStaticList', ['getId']);
            $mockOrgStaticListRepository = $this->getMock('OrgStaticListRepository', ['getAllStaticLists', 'getStaticListReferance', 'persist']);
            $mockOrgStaticListRepository->method('getAllStaticLists')->willReturn(['id' => 123]);
            $mockOrgStaticListRepository->method('getStaticListReferance')->willReturn($mockOrgStaticList);

            // Mocking AcademicUpdateRequest Object
            $mockAcademicUpdateRequest = $this->getMock('\Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest', ['getId', 'getPerson', 'getSelectStudent']);
            $mockAcademicUpdateRequest->method('getPerson')->willReturn($mockPerson);

            $mockOrgStaticListStudentsRepository = $this->getMock('OrgStaticListStudentsRepository', ['getStudentsByList']);
            $mockOrgStaticListStudentsRepository->method('getStudentsByList')->willReturn([]);

            // Mocking OrgCourses Object
            $mockOrgCourses = $this->getMock('\Synapse\AcademicBundle\Entity\OrgCourses', ['getId']);
            $mockOrgCoursesRepository = $this->getMock('OrgCoursesRepository', ['find', 'getAllCoursesEncapsulatingDatetime']);
            $mockOrgCoursesRepository->method('find')->willReturn($mockOrgCourses);
            $mockOrgCoursesRepository->method('getAllCoursesEncapsulatingDatetime')->willReturn([]);

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrganizationRoleRepository::REPOSITORY_KEY, $mockOrganizationRoleRepository],
                    [OrgAcademicYearRepository::REPOSITORY_KEY, $mockOrgAcademicYearRepository],
                    [OrgCourseStudentRepository::REPOSITORY_KEY, $mockOrgCourseStudentRepository],
                    [OrgPersonFacultyRepository::REPOSITORY_KEY, $mockOrgPersonFacultyRepository],
                    [OrgCourseFacultyRepository::REPOSITORY_KEY, $mockOrgCourseFacultyRepository],
                    [OrgGroupStudentsRepository::REPOSITORY_KEY, $mockOrgGroupStudentsRepository],
                    [OrgStaticListRepository::REPOSITORY_KEY, $mockOrgStaticListRepository],
                    [OrgStaticListStudentsRepository::REPOSITORY_KEY, $mockOrgStaticListStudentsRepository],
                    [OrgCoursesRepository::REPOSITORY_KEY,$mockOrgCoursesRepository]
                ]
            );
            $academicUpdateCreateDto = $this->getAcademicUpdateCreateDto($selectedAttributes);
            $academicUpdateRequestService = new AcademicUpdateRequestService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $academicUpdateCreateDtoDetails = $academicUpdateRequestService->getAcademicUpdateCountForRequest($academicUpdateCreateDto, $mockPerson);
            $this->assertInstanceOf("Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateCreateDto", $academicUpdateCreateDtoDetails);
            $this->assertEquals($academicUpdateCreateDtoDetails->getOrganizationId(), $selectedAttributes['organization_id']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestDescription(), $selectedAttributes['request_description']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestDueDate()->format('m/d/Y'), $selectedAttributes['request_due_date']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestEmailSubject(), $selectedAttributes['request_email_subject']);
            $this->assertEquals($academicUpdateCreateDtoDetails->getRequestEmailOptionalMessage(), $selectedAttributes['request_email_optional_message']);
            $this->assertGreaterThan(0, $academicUpdateCreateDtoDetails->getUpdateCount());
            $this->assertInternalType('object', $academicUpdateCreateDtoDetails);

        }, [
            'examples' => [
                // Setting students, staff, static_list as true along with groups, courses, profile_items as false
                [
                    95,
                    [
                        "organization_id" =>"2",
                        "request_name" => "Testing academic update",
                        "request_description" => "Testing academic update",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> true,
                            "selected_student_ids"=> ""
                        ],
                        "staff"=> [
                            "is_all"=> "1",
                            "selected_staff_ids"=> ""
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> ""
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> "1",
                            "selected_static_ids"=> ""
                        ]
                    ]
                ],
                // Setting selected staff, courses along with groups, profile_items, static_list as false and students as true
                [
                    95,
                    [
                        "organization_id" =>"2",
                        "request_name" => "Testing academic update",
                        "request_description" => "Testing academic update",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> true,
                            "selected_student_ids"=> ""
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> ""
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> "9001,9002"
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> false,
                            "selected_static_ids"=> ""
                        ]
                    ]
                ],
                // Setting selected staff, groups, courses, static_list along with profile_items as false and students as true
                [
                    95,
                    [
                        "organization_id" =>"2",
                        "request_name" => "Testing academic update",
                        "request_description" => "Testing academic update",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> true,
                            "selected_student_ids"=> ""
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> "9023"
                        ],
                        "courses"=> [
                            "is_all"=> false,
                            "selected_course_ids"=> "9001,9002"
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> false,
                            "selected_static_ids"=> "123,456"
                        ]
                    ]
                ],
                // Setting selected staff, groups along with students, courses, static_list as true and profile_items as false
                [
                    95,
                    [
                        "organization_id" =>"2",
                        "request_name" => "Testing academic update",
                        "request_description" => "Testing academic update",
                        "request_due_date" => new DateTime(),
                        "request_email_subject" => "Test",
                        "request_email_optional_message"=> "Optional message",
                        "students"=> [
                            "is_all"=> true,
                            "selected_student_ids"=> ""
                        ],
                        "staff"=> [
                            "is_all"=> false,
                            "selected_staff_ids"=> "123"
                        ],
                        "groups"=> [
                            "is_all"=> false,
                            "selected_group_ids"=> "9023"
                        ],
                        "courses"=> [
                            "is_all"=> true,
                            "selected_course_ids"=> ""
                        ],
                        "profile_items"=> [
                            "is_all"=> false,
                            "isps"=> [],
                            "ebi"=> []
                        ],
                        "static_list"=> [
                            "is_all"=> true,
                            "selected_static_ids"=> ""
                        ]
                    ]
                ]
            ]
        ]);
    }
}