<?php
namespace Synapse\SearchBundle\Service\Impl;

use Symfony\Component\Validator\Constraints\DateTime;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Utility\SearchUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\SearchBundle\Entity\OrgSearch;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;


class SavedSearchServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $personId = 1;

    private $organization = 1;


    public function testGetSavedSearch()
    {

        $this->specify("Get saved search filters", function ($searchId, $orgId, $userId, $dateTime) {

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'info'
            ));

            $mockOrganizationEntity = $this->getMock('Organization', [
                'getId',
                'getTimezone',
                'find'
            ]);

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', array(
                'findOneBy'
            ));

            $orgSearch = $this->getMock('OrgSearch', [
                'getOrganization',
                'getId',
                'getName',
                'getCreatedAt',
                'getJson',
                'getPerson',

            ]);

            $mockPersonEntity = $this->getMock('Person', [
                'getId',
                'getOrganization'
            ]);
            $mockPersonEntity->method('getId')
                ->willReturn(1);

            $mockOrgSearchRepository->method('findOneBy')
                ->willReturn($orgSearch);

            $orgSearch->method('getOrganization')
                ->willReturn($mockOrganizationEntity);

            $mockOrganizationEntity->method('getId')
                ->willReturn(1);

            $orgSearch->method('getId')
                ->willReturn(1);

            $orgSearch->method('getCreatedAt')
                ->willReturn(new \DateTime());

            $orgSearch->method('getPerson')
                ->willReturn($mockPersonEntity);

            $mockOrganizationEntity->method('getTimezone')
                ->willReturn('Asia/Kolkotta');

            $mockPersonEntity->method('getOrganization')
                ->willReturn($mockOrganizationEntity);


            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockOrgService = $this->getMock('OrganizationService', array(
                'find'
            ));
            $mockOrgService->method('find')
                ->willReturn($mockOrganizationEntity);

            $mockPersonService = $this->getMock('PersonService', array(
                'findPerson'
            ));
            $mockPersonService->method('findPerson')
                ->willReturn($mockPersonEntity);


            $mockMetaDataListValues = $this->getMock('MetadataListValues', [
                'findByListName'
            ]);
            $mockMetaDataListValues->method('findByListName')
                ->willReturn(null);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseSearchBundle:OrgSearch',
                        $mockOrgSearchRepository
                    ],
                    [
                        'SynapseCoreBundle:MetadataListValues',
                        $mockMetaDataListValues
                    ],

                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'org_service',
                        $mockOrgService
                    ],
                    [
                        'person_service',
                        $mockPersonService
                    ]
                ]);

            $expectedSaveSearch = $this->createCustomSearchDto($dateTime);

            $savedSearchService = new SavedSearchService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $savedSearch = $savedSearchService->getSavedSearch($searchId, $orgId, $userId);
            $savedSearch->setDateCreated($dateTime);

            $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\SaveSearchDto", $savedSearch);
            $this->assertEquals($this->personId, $savedSearch->getPersonId());
            $this->assertEquals($this->organization, $savedSearch->getOrganizationId());
            $this->assertEquals($savedSearch, $expectedSaveSearch);

        }, [
            'examples' => [
                [
                    1,
                    $this->personId,
                    $this->organization,
                    new \DateTime()
                ],
                [
                    2,
                    $this->personId,
                    $this->organization,
                    new \DateTime()
                ]
            ]
        ]);
    }

    public function testCreateSavedSearches()
    {
        $this->specify("Test create saved searches", function ($savedSearchDto, $personId, $organizationId, $expectedResult, $errorType = '') {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['error', 'debug', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockPersonObject = $this->getMock('\Synapse\CoreBundle\Entity\Person', ['getId']);
            $mockPersonObject->method('getId')->willReturn($personId);

            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);

            $mockOrgSearchRepository = $this->getMock('OrgSearchRepository', ['findOneBy', 'createOrgSearch', 'flush']);

            $mockSearchUtilityService = $this->getMock('SearchUtilityService', ['makeSqlQuery']);

            $mockValidator = $this->getMock('validator', ['validate']);


            if ($organizationId != -1) {
                $mockOrganization = $this->getMock('Synapse\CoreBundle\Entity\Organization', ['getId']);
                $mockOrganizationRepository->method('find')->willReturn($mockOrganization);
                $searchAttributes = $savedSearchDto->getSearchAttributes();

                $iterator = 0;
                if (isset($searchAttributes['risk_indicator_ids']) && !empty($searchAttributes['risk_indicator_ids'])) {
                    $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn("and p.risk_level in ('1','2','3')");
                    $iterator++;
                }

                if (isset($searchAttributes['intent_to_leave_ids']) && !empty($searchAttributes['intent_to_leave_ids'])) {

                    $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn("intent_to_leave_ids in ('1','2','3')");
                    $iterator++;
                }
                $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn("and ogs.org_group_id in ('1')");

            } else {
                $mockOrganizationRepository->method('find')->will($this->throwException(new SynapseValidationException('Organization Not Found.')));
            }

            $mockPersonRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockPersonObject));

            $mockOrgSearchRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue(true));

            $mockOrgSearchRepository->expects($this->any())
                ->method('createOrgSearch')
                ->will($this->returnValue(true));


            $this->getOrganizationSearchEntity($savedSearchDto);

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository],
                    [OrgSearchRepository::REPOSITORY_KEY, $mockOrgSearchRepository]
                ]
            );

            $mockContainer->method('get')->willReturnMap([
                [SynapseConstant::VALIDATOR, $mockValidator],
                [SearchUtilityService::SERVICE_KEY, $mockSearchUtilityService],
            ]);

            if ($errorType == 'org_search_duplicate_error') {
                $organizationSearchValidationErrors = ['error' => $expectedResult];
                $errors = $this->arrayOfErrorObjects($organizationSearchValidationErrors);
                $mockValidator->method('validate')->willReturn($errors);
            } else {
                $mockValidator->method('validate')->willReturn([]);
            }

            try {
                $savedSearchService = new SavedSearchService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $savedSearchService->createSavedSearches($savedSearchDto, $personId);
                $this->assertNotEmpty($result->getSavedSearchName());
                $this->assertNotEmpty($result->getSearchAttributes());
                $this->assertEquals($result->getSavedSearchName(), $expectedResult);

            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        }, [
                'examples' => [
                    // Example 1: Invalid organization should throw SynapseValidationException
                    [
                        $this->createSavedSearchDto(201718, 201, 'savedSearch1'),
                        201718,
                        -1,
                        'Organization Not Found.'
                    ],
                    // Example 2: Duplicate organization search should throw SynapseValidationException
                    [
                        $this->createSavedSearchDto(201718, 201, 'savedSearch1'),
                        201718,
                        201,
                        'Duplicate organization search.',
                        'org_search_duplicate_error'
                    ],
                    // Example 3: Creates saved searches
                    [
                        $this->createSavedSearchDto(201718, 201, 'savedSearch2'),
                        201718,
                        201,
                        'savedSearch2'
                    ]
                ]
            ]
        );
    }

    public function testCoursesSearch()
    {
        $this->specify("Test add course to search query", function ($courses, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['error', 'debug', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockSearchUtilityService = $this->getMock('SearchUtilityService', ['makeSqlQuery']);

            $iterator = 0;
            if (isset($courses['department_id']) && !empty($courses['department_id'])) {
                $departmentId = $courses['department_id'];
                $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn("dept_code = '$departmentId'");
                $iterator++;
            }
            if (isset($courses['subject_id']) && !empty($courses['subject_id'])) {
                $subjectId = $courses['subject_id'];
                $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn(" OR subject_code = '$subjectId'");
                $iterator++;
            }
            if (isset($courses['section_ids']) && !empty($courses['section_ids'])) {
                $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn(" section_ids in ('section1','section2','section3','section4')");
                $iterator++;
            }

            $mockContainer->method('get')->willReturnMap([
                [SearchUtilityService::SERVICE_KEY, $mockSearchUtilityService]
            ]);

            try {
                $savedSearchService = new SavedSearchService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $savedSearchService->coursesSearch($courses);
                //echo $result;die;
                $this->assertEquals($result, $expectedResult);

            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        }, [
                'examples' => [
                    // Example 1: Will return department_id appended query string
                    [
                        ['department_id' => 1],
                        "AND p.id in(select distinct person_id from org_course_student where deleted_at is null and org_courses_id in
                            (select distinct id from org_courses oc where deleted_at is null and id in
                            (select distinct org_courses_id from org_course_faculty where deleted_at is null and org_permissionset_id in
                            (select id from org_permissionset where deleted_at is null and view_courses=1)) AND (dept_code = '1')))"
                    ],
                    // Example 2: Will return dept_code and subject_id appended query string
                    [
                        ['department_id' => 1,
                            'subject_id' => 12
                        ],
                        "AND p.id in(select distinct person_id from org_course_student where deleted_at is null and org_courses_id in
                            (select distinct id from org_courses oc where deleted_at is null and id in
                            (select distinct org_courses_id from org_course_faculty where deleted_at is null and org_permissionset_id in
                            (select id from org_permissionset where deleted_at is null and view_courses=1)) AND (dept_code = '1' OR subject_code = '12')))"
                    ],
                    // Example 3: Will return department_id,subject_id and section_ids appended query string
                    [
                        ['department_id' => 1,
                            'subject_id' => 12,
                            'section_ids' => 'section1,section2,section3,section4'
                        ],
                        "AND p.id in(select distinct person_id from org_course_student where deleted_at is null and org_courses_id in
                            (select distinct id from org_courses oc where deleted_at is null and id in
                            (select distinct org_courses_id from org_course_faculty where deleted_at is null and org_permissionset_id in
                            (select id from org_permissionset where deleted_at is null and view_courses=1)) AND (dept_code = '1' OR subject_code = '12' section_ids in ('section1','section2','section3','section4'))))"
                    ],
                    // Example 4: Test with empty data
                    [
                        [],
                        ""
                    ]
                ]
            ]
        );
    }

    public function testGetBaseQuery()
    {
        $this->specify("Test get basequery", function ($savedSearchDto, $personId, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['error', 'debug', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockSearchUtilityService = $this->getMock('SearchUtilityService', ['makeSqlQuery']);

            $searchAttributes = $savedSearchDto->getSearchAttributes();
            $iterator = 0;

            if (isset($searchAttributes['risk_indicator_ids']) && !empty($searchAttributes['risk_indicator_ids'])) {
                $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn($this->makeSqlQuery($searchAttributes['risk_indicator_ids'], ' and p.risk_level'));
                $iterator++;
            }
            if (isset($searchAttributes['intent_to_leave_ids']) && !empty($searchAttributes['intent_to_leave_ids'])) {
                $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn($this->makeSqlQuery($searchAttributes['intent_to_leave_ids'], ' and p.intent_to_leave'));
                $iterator++;
            }
            if (isset($searchAttributes['referral_status']) && !empty($searchAttributes['referral_status'])) {
                $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn($this->makeSqlQuery($searchAttributes['referral_status'], 'referralstatus'));
                $iterator++;
            }

            $mockSearchUtilityService->expects($this->at($iterator))->method("makeSqlQuery")->willReturn($this->makeSqlQuery($searchAttributes['group_ids'], ' and ogs.org_group_id'));

            $mockContainer->method('get')->willReturnMap([
                [SearchUtilityService::SERVICE_KEY, $mockSearchUtilityService]
            ]);

            try {
                $savedSearchService = new SavedSearchService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $savedSearchService->getBaseQuery($searchAttributes, $personId);
                $this->assertEquals(trim($result), trim($expectedResult));

            } catch (SynapseException $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        }, [
                'examples' => [
                    // Example 1: Returns basequery
                    [
                        $this->createSavedSearchDto(201718, 201, 'savedSearch1'),
                        201718,
                        "left join person_ebi_metadata pem on 
                (pem.person_id = p.id and pem.ebi_metadata_id= [EBI_METADATA_CLASSLEVEL_ID] 
                    /* and pem.org_academic_year_id = [CURRENT_ACADEMIC_YEAR] */)
                left join ebi_metadata_list_values emlv on (pem.metadata_value = emlv.list_value
            		and emlv.ebi_metadata_id = [EBI_METADATA_CLASSLEVEL_ID])
          left join intent_to_leave il on (p.intent_to_leave = il.id)
          left join referrals r on (p.id = r.person_id_student and  r.deleted_at is null)
          left join org_person_student ops on (p.id = ops.person_id)
          left outer join activity_log lc on (lc.person_id_student = p.id and lc.deleted_at is null)
          left join risk_level rl on (p.risk_level = rl.id)
          left join risk_model_levels rml on (rml.risk_level = rl.id)
          
          
          
         201718 and deleted_at is null and org_permissionset_id in
                               (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null))
                                and ogs.deleted_at is null referralstatus in (''o'',''c'') and p.deleted_at is null  and p.risk_level in ('1','2','3') and ogs.org_group_id = '1' and gf.person_id=201718 group by (p.id)
                        "
                    ],
                    // Example 2: Returns basequery with courses search
                    [
                        $this->createSavedSearchDto(201718, 201, 'savedSearch1', null, null, true),
                        201718,
                        "left join person_ebi_metadata pem on 
                (pem.person_id = p.id and pem.ebi_metadata_id= [EBI_METADATA_CLASSLEVEL_ID] 
                    /* and pem.org_academic_year_id = [CURRENT_ACADEMIC_YEAR] */)
                left join ebi_metadata_list_values emlv on (pem.metadata_value = emlv.list_value
            		and emlv.ebi_metadata_id = [EBI_METADATA_CLASSLEVEL_ID])
          left join intent_to_leave il on (p.intent_to_leave = il.id)
          left join referrals r on (p.id = r.person_id_student and  r.deleted_at is null)
          left join org_person_student ops on (p.id = ops.person_id)
          left outer join activity_log lc on (lc.person_id_student = p.id and lc.deleted_at is null)
          left join risk_level rl on (p.risk_level = rl.id)
          left join risk_model_levels rml on (rml.risk_level = rl.id)
          
          
          
         201718 and deleted_at is null and org_permissionset_id in
                               (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null))
                                and ogs.deleted_at is null referralstatus in (''o'',''c'') and p.deleted_at is null  and p.risk_level in ('1','2','3') and ogs.org_group_id = '1' and gf.person_id=201718 group by (p.id)
                        "
                    ],
                    // Example 3: Returns basequery with isps search
                    [
                        $this->createSavedSearchDto(201718, 201, 'savedSearch1', null, "isps"),
                        201718,
                        "left join person_ebi_metadata pem on 
                (pem.person_id = p.id and pem.ebi_metadata_id= [EBI_METADATA_CLASSLEVEL_ID] 
                    /* and pem.org_academic_year_id = [CURRENT_ACADEMIC_YEAR] */)
                left join ebi_metadata_list_values emlv on (pem.metadata_value = emlv.list_value
            		and emlv.ebi_metadata_id = [EBI_METADATA_CLASSLEVEL_ID])
          left join intent_to_leave il on (p.intent_to_leave = il.id)
          left join referrals r on (p.id = r.person_id_student and  r.deleted_at is null)
          left join org_person_student ops on (p.id = ops.person_id)
          left outer join activity_log lc on (lc.person_id_student = p.id and lc.deleted_at is null)
          left join risk_level rl on (p.risk_level = rl.id)
          left join risk_model_levels rml on (rml.risk_level = rl.id)
          
          
          
         201718 and deleted_at is null and org_permissionset_id in
                               (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null))
                                and ogs.deleted_at is null referralstatus in (''o'',''c'') and p.deleted_at is null  and p.risk_level in ('1','2','3') and ogs.org_group_id = '1' and gf.person_id=201718 group by (p.id)
                        "
                    ],
                    // Example 4: Returns basequery with EBI search
                    [
                        $this->createSavedSearchDto(201718, 201, 'savedSearch1', $this->getDataBlockArray(), null),
                        201718,
                        "left join person_ebi_metadata pem on 
                (pem.person_id = p.id and pem.ebi_metadata_id= [EBI_METADATA_CLASSLEVEL_ID] 
                    /* and pem.org_academic_year_id = [CURRENT_ACADEMIC_YEAR] */)
                left join ebi_metadata_list_values emlv on (pem.metadata_value = emlv.list_value
            		and emlv.ebi_metadata_id = [EBI_METADATA_CLASSLEVEL_ID])
          left join intent_to_leave il on (p.intent_to_leave = il.id)
          left join referrals r on (p.id = r.person_id_student and  r.deleted_at is null)
          left join org_person_student ops on (p.id = ops.person_id)
          left outer join activity_log lc on (lc.person_id_student = p.id and lc.deleted_at is null)
          left join risk_level rl on (p.risk_level = rl.id)
          left join risk_model_levels rml on (rml.risk_level = rl.id)
          
          
          
         201718 and deleted_at is null and org_permissionset_id in
                               (select id from org_permissionset where accesslevel_ind_agg = 1 and deleted_at is null))
                                and ogs.deleted_at is null referralstatus in (''o'',''c'') and p.deleted_at is null  and p.risk_level in ('1','2','3')AND ebi_metadata_id=1) ) and ogs.org_group_id = '1' and gf.person_id=201718 group by (p.id)"
                    ]
                ]
            ]
        );
    }

    private function getDataBlockArray()
    {
        $dataBlockArray = [
            "profile_block_id" => 12,
            "name" => "Profile Block",
            "profile_items" => [
                [
                    "id" => 1,
                    "item_data_type" => "S",
                    "calendar_assignment" => "Y",
                    "category_type" => [
                        [
                            "answer" => "some ans",
                            "value" => "0"
                        ]
                    ],
                    "name" => "PROFILE NAME",
                    "years" => [
                        [
                            "year_id" => 1,
                            "year_name" => "some Year"
                        ]
                    ]
                ]
            ]
        ];
        return $dataBlockArray;
    }

    private function makeSqlQuery($value, $append)
    {
        $valueCount = substr_count($value, ',');
        if ($value == null || strlen($value) < 1) {
            $sqlAppend = "";
        } elseif ($valueCount == 0 && strlen($value) > 0) {
            $sqlAppend = $append . " = '" . $value . "'";
        } elseif ($valueCount > 0) {
            $valueArray = explode(',', $value);
            foreach ($valueArray as $arrayData) {
                if (trim($arrayData) != "") {
                    $resultArray[] = $arrayData;
                }
                $value = "'" . implode("','", $resultArray) . "'";
            }
            $sqlAppend = $append . " in ($value)";
        } else {
            $sqlAppend = "";
        }
        return $sqlAppend;
    }

    private function createSavedSearchDto($personId, $organizationId, $savedSearchName, $dataBlockArr = null, $ispArr = null, $includeCourses = false)
    {
        $searchAttribute = [];
        $savedSearchDto = new SaveSearchDto();
        $savedSearchDto->setSavedSearchName($savedSearchName);
        $savedSearchDto->setOrganizationId($organizationId);
        $savedSearchDto->setPersonId($personId);
        $savedSearchDto->setSavedSearchId(1);
        $savedSearchDto->setDateCreated(new \DateTime());
        $searchAttribute["risk_indicator_ids"] = "1,2,3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "'o','c'";
        $searchAttribute["contact_types"] = "1,2";
        if ($includeCourses) {
            $course1 = array();
            $course1['department_id'] = "IT";
            $course1['subject_id'] = "SEC001";
            $course1['course_ids'] = "0087";
            $course1['section_ids'] = "SEC 1";
            $searchAttribute["courses"] = $course1;
        }
        if (!is_null($ispArr)) {
            $searchAttribute["isps"] = $this->createIspArrForTerm();
        }
        if (!is_null($dataBlockArr)) {
            $searchAttribute["datablocks"][] = $dataBlockArr;
        }
        $savedSearchDto->setSearchAttributes($searchAttribute);
        return $savedSearchDto;
    }

    private function getOrganizationSearchEntity($savedSearchDto)
    {
        $orgSearch = new  OrgSearch();
        $searchAttributes = $savedSearchDto->getSearchAttributes();
        $searchAttributesJson = json_encode($searchAttributes);
        $mockOrganization = $this->getOrganizationInstance($savedSearchDto->getOrganizationId());
        $orgSearch->setOrganization($mockOrganization);
        $orgSearch->setName($savedSearchDto->getSavedSearchName());
        $mockPerson = $this->getPersonInstance($savedSearchDto->getPersonId());
        $orgSearch->setPerson($mockPerson);
        $orgSearch->setQuery('select');
        $orgSearch->setJson($searchAttributesJson);
        $orgSearch->setEditedByMe(true);
        return $orgSearch;
    }

    private function getOrganizationInstance($organizationId)
    {
        $organization = $this->getMock("Synapse\CoreBundle\Entity\Organization", ['getId']);
        $organization->setCampusId($organizationId);
        $organization->method('getId')->willReturn($organizationId);
        return $organization;

    }

    private function getPersonInstance($personId)
    {
        $person = $this->getMock("Synapse\CoreBundle\Entity\Person", ['getId']);
        $person->setId($personId);
        $person->method('getId')->willReturn($personId);
        return $person;
    }

    private function arrayOfErrorObjects($errorArray)
    {
        $returnArray = [];
        foreach ($errorArray as $errorKey => $error) {
            $mockErrorObject = $this->getMock('ErrorObject', ['getPropertyPath', 'getMessage']);
            $mockErrorObject->method('getPropertyPath')->willReturn($errorKey);
            $mockErrorObject->method('getMessage')->willReturn($error);
            $returnArray[] = $mockErrorObject;
        }
        return $returnArray;
    }

    private function createCustomSearchDto($dateTime, $dataBlockArr = null, $ispArr = null)
    {
        $searchAttribute = [];
        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($this->organization);
        $saveSearchDto->setPersonId($this->personId);
        $saveSearchDto->setSavedSearchId(1);
        $saveSearchDto->setDateCreated($dateTime);
        $searchAttribute["risk_indicator_ids"] = "1,2,3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "'o','c'";
        $searchAttribute["contact_types"] = "1,2";
        if (!is_null($ispArr)) {
            $searchAttribute["isps"] = $this->createIspArrForTerm();
        }
        if (!is_null($dataBlockArr)) {
            $searchAttribute["datablocks"][] = $dataBlockArr;
        }
        return $saveSearchDto;
    }

    private function createIspArrForTerm()
    {
        $ispArr = [
            [
                "id" => 2,
                "item_data_type" => "N",
                "calendar_assignment" => "T",
                "is_single" => true,
                "single_value" => "5",
                "name" => "ISP",
                "terms" => [
                    [
                        "term_id" => 1,
                        "term_name" => "Term1"
                    ]
                ]
            ]
        ];
        return $ispArr;
    }
}