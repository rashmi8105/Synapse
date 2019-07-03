<?php

namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\RiskBundle\Entity\OrgRiskvalCalcInputs;
use Synapse\RiskBundle\Entity\RiskGroup;
use Synapse\RiskBundle\Repository\OrgRiskvalCalcInputsRepository;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;

class OrgCalcFlagsRiskServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;


    use\Codeception\Specify;


    public function _before()
    {
        //Core Mocks
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);

    }


    public function testAddStudentsToRiskFlagCalculation()
    {
        $this->specify("Testing OrgCalcFlagsRiskService Function for setting flags", function ($riskGroupPersonIdArray, $doesflagExist, $expectedResults) {
            //Mocks
            //Core Mocks
            $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $this->mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockRiskGroupPersonHistoryRepository = $this->getMock('riskGroupPersonHistoryRepository', ['getRiskGroupByOrg']);
            $mockOrgRiskvalCalcInputsRepository = $this->getMock('orgRiskvalCalcInputsRepository', ['findOneBy', 'persist','flush', 'clear']);
            $mockPersonRepository = $this->getMock('personRepository', ['find', 'flush', 'clear']);
            $irrelevantRiskGroup = 1;
            $irrelevantOrganizationId = 1;
            $mockOrganization = new Organization();
            $mockPerson = new Person();
            $mockPerson->setOrganization($mockOrganization);

            if ($doesflagExist) {
                $orgRiskvalCalcInputs = new OrgRiskvalCalcInputs();
            } else {
                $orgRiskvalCalcInputs = null;
            }

            $mockRiskGroupPersonHistoryRepository->method('getRiskGroupByOrg')->willReturn($riskGroupPersonIdArray);
            $mockOrgRiskvalCalcInputsRepository->method('findOneBy')->willReturn($orgRiskvalCalcInputs);
            $mockPersonRepository->method('find')->wilLReturn($mockPerson);


            $this->mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [RiskGroupPersonHistoryRepository::REPOSITORY_KEY, $mockRiskGroupPersonHistoryRepository],
                    [OrgRiskvalCalcInputsRepository::REPOSITORY_KEY, $mockOrgRiskvalCalcInputsRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]);

            $orgCalcFlagsRiskService = new OrgCalcFlagsRiskService($this->mockRepositoryResolver, $this->mockLogger);
            $recordsCreatedCount = $orgCalcFlagsRiskService->addStudentsToRiskFlagCalculation($irrelevantRiskGroup, $irrelevantOrganizationId);

            $this->assertEquals($expectedResults, $recordsCreatedCount);


        }, [
            'examples' => [
                //No Records Passed
                [
                    [],
                    1,
                    0

                ],
                    // 1 Record Passed
                [
                    [
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3]
                    ],
                    1,
                    1
                ],
                // 7 Records Passed
                [
                    [
                        ["riskGroupId" => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3]
                    ],
                    1,
                    7
                ],
                // 7 Records Passed and flag does not exist
                 [
                    [
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3],
                        ['riskGroupId' => 1, 'externalId' => "Jeff", "id" => 3]
                    ],
                    0,
                    7
                ]
        ]]);

    }

    public function testUpdateStudentRiskFlags()
    {
        $this->specify("Testing UpdateStudentRiskFlags", function ($studentToUpdateArray, $doesflagExist, $isStudentInRiskGroup, $expectedResults) {
            //Mocks
            //Core Mocks
            $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $this->mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockOrgRiskvalCalcInputsRepository = $this->getMock('orgRiskvalCalcInputsRepository', ['findOneBy', 'persist','flush', 'clear']);
            $mockPersonRepository = $this->getMock('personRepository', ['findOneBy', 'flush', 'clear']);
            $mockOrgPersonStudentRepository = $this->getMock('orgPersonStudentRepostory', ['findOneBy']);
            $mockRiskGroupPersonHistoryRepository = $this->getMock('riskGroupPersonHistoryRepository', ['isStudentInValidRiskGroup']);

            $irrelevantOrganizationId = 1;
            $mockOrganization = new Organization();
            $mockPerson = new Person();
            $mockPerson->setOrganization($mockOrganization);
            $mockOrgPersonStudent = new OrgPersonStudent();

            if ($doesflagExist) {
                $orgRiskvalCalcInputs = new OrgRiskvalCalcInputs();
            } else {
                $orgRiskvalCalcInputs = null;
            }

            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            $mockRiskGroupPersonHistoryRepository->method('isStudentInValidRiskGroup')->willReturn($isStudentInRiskGroup);
            $mockOrgRiskvalCalcInputsRepository->method('findOneBy')->willReturn($orgRiskvalCalcInputs);

            $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);


            $this->mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [RiskGroupPersonHistoryRepository::REPOSITORY_KEY, $mockRiskGroupPersonHistoryRepository],
                    [OrgRiskvalCalcInputsRepository::REPOSITORY_KEY, $mockOrgRiskvalCalcInputsRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrgPersonStudentRepository::REPOSITORY_KEY, $mockOrgPersonStudentRepository]
                ]);

            $orgCalcFlagsRiskService = new OrgCalcFlagsRiskService($this->mockRepositoryResolver, $this->mockLogger);
            $recordsCreatedCount = $orgCalcFlagsRiskService->updateStudentRiskFlags($studentToUpdateArray, $irrelevantOrganizationId);

            $this->assertEquals($expectedResults, $recordsCreatedCount);


        }, [
            'examples' => [
                //No Records Passed
                [
                    [],//Student List
                    1,  //Existing Flag
                    1, //Existing Risk Group
                    0 //Expected Flush Count

                ],
                // 1 Record Passed
                [
                    ['1'],//Student List
                    1, //Existing Flag
                    1, //Existing Risk Group
                    1 //Expected Flush Count
                ],
                // 7 Records Passed
                [
                    ['1', '2', '3', '4', '5', '6', '7'], //Student List
                    1,  //Existing Flag
                    1, //Existing Risk Group
                    7 //Expected Flush Count
                ],
                // 7 Records Passed and flag does not exist
                [
                    ['1', '2', '3', '4', '5', '6', '7'], //Student List
                    0,  //Existing Flag
                    1, //Existing Risk Group
                    7 //Expected Flush Count
                ],
                //An Empty String Entry as this is user input
                [
                    [' '], //Student List
                    1, //Existing Flag
                    1, //Existing Risk Group
                    0 //Expected Flush Count
                ]
            ]]);

    }

    public function testUpdateStudentRiskFlagsWithInternalIds()
    {
        $this->specify("Testing UpdateStudentRiskFlagsWithInternalIds", function ($studentToUpdateArray, $doesflagExist, $isStudentInRiskGroup, $expectedResults) {
            //Mocks
            //Core Mocks
            $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $this->mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockOrgRiskvalCalcInputsRepository = $this->getMock('orgRiskvalCalcInputsRepository', ['findOneBy', 'persist','flush', 'clear']);
            $mockPersonRepository = $this->getMock('personRepository', ['find', 'flush', 'clear']);
            $mockOrgPersonStudentRepository = $this->getMock('orgPersonStudentRepostory', ['findOneBy']);
            $mockRiskGroupPersonHistoryRepository = $this->getMock('riskGroupPersonHistoryRepository', ['isStudentInValidRiskGroup']);

            $irrelevantOrganizationId = 1;
            $mockOrganization = new Organization();
            $mockPerson = new Person();
            $mockPerson->setOrganization($mockOrganization);
            $mockOrgPersonStudent = new OrgPersonStudent();

            if ($doesflagExist) {
                $orgRiskvalCalcInputs = new OrgRiskvalCalcInputs();
            } else {
                $orgRiskvalCalcInputs = null;
            }

            $mockOrgPersonStudentRepository->method('findOneBy')->willReturn($mockOrgPersonStudent);
            $mockRiskGroupPersonHistoryRepository->method('isStudentInValidRiskGroup')->willReturn($isStudentInRiskGroup);
            $mockOrgRiskvalCalcInputsRepository->method('findOneBy')->willReturn($orgRiskvalCalcInputs);


            $mockPersonRepository->method('find')->willReturn($mockPerson);


            $this->mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [RiskGroupPersonHistoryRepository::REPOSITORY_KEY, $mockRiskGroupPersonHistoryRepository],
                    [OrgRiskvalCalcInputsRepository::REPOSITORY_KEY, $mockOrgRiskvalCalcInputsRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrgPersonStudentRepository::REPOSITORY_KEY, $mockOrgPersonStudentRepository]
                ]);

            $orgCalcFlagsRiskService = new OrgCalcFlagsRiskService($this->mockRepositoryResolver, $this->mockLogger);
            $recordsCreatedCount = $orgCalcFlagsRiskService->updateStudentRiskFlagsWithInternalIds($studentToUpdateArray, $irrelevantOrganizationId);

            $this->assertEquals($expectedResults, $recordsCreatedCount);


        }, [
            'examples' => [
                //No Records Passed
                [
                    [],//Student List
                    1,  //Existing Flag
                    1, //Existing Risk Group
                    0 //Expected Flush Count

                ],
                // 1 Record Passed
                [
                    [1],//Student List
                    1, //Existing Flag
                    1, //Existing Risk Group
                    1 //Expected Flush Count
                ],
                // 7 Records Passed
                [
                    [1, 2, 3, 4, 5, 6, 7], //Student List
                    1,  //Existing Flag
                    1, //Existing Risk Group
                    7 //Expected Flush Count
                ],
                // 7 Records Passed and flag does not exist
                [
                    [1, 2, 3, 4, 5, 6, 7], //Student List
                    0,  //Existing Flag
                    1, //Existing Risk Group
                    7 //Expected Flush Count
                ]
            ]]);

    }
}
