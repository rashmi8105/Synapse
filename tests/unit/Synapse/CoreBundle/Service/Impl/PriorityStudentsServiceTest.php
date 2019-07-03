<?php
namespace Synapse\CoreBundle\Service\Impl;

use Codeception\Specify;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\RestBundle\Entity\PersonDTO;
use Synapse\RestBundle\Entity\RiskLevelsDto;
use Synapse\RiskBundle\Entity\RiskLevels;
use Synapse\RiskBundle\Repository\RiskLevelsRepository;

class PriorityStudentsServiceTest extends \PHPUnit_Framework_TestCase
{
    use Specify;

    public function testGetMyStudentsDashboard()
    {
        $this->specify("Test Get My Students Dashboard", function ($personId, $errorType, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            // Mock Repositories
            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'getMyHighPriorityStudentsCount', 'getStudentCountByRiskLevel']);
            $mockPerson = $this->getMock('Person',['getId','getOrganization']);
            $mockOrganization = $this->getMock('Organization',['getId']);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);
            if ($errorType == 'person_not_found') {
                $mockPersonRepository->method('find')->willThrowException(new SynapseValidationException($expectedResult));
            } else {
                $mockPersonRepository->method('find')->willReturn($mockPerson);
            }
            $mockPersonRepository->method('getMyHighPriorityStudentsCount')->willReturn(100);
            $mockPersonRepository->method('getStudentCountByRiskLevel')->willReturn($this->getStudentCountByRiskLevel());
            $mockRiskLevelsRepository = $this->getMock('RiskLevelsRepository', ['findAll']);
            $mockRiskLevelsRepository->method('findAll')->willReturn($this->getRiskLevelsDetails());

            // Mock Services
            $mockAcademicYearService = $this->getMock('AcademicYearService',['findCurrentAcademicYearForOrganization']);
            $currentAcademicYearArray = ['org_academic_year_id' => 127, 'year_id' => 201516];
            $mockAcademicYearService->method('findCurrentAcademicYearForOrganization')->willReturn($currentAcademicYearArray);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        RiskLevelsRepository::REPOSITORY_KEY,
                        $mockRiskLevelsRepository
                    ]
                ]);

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        AcademicYearService::SERVICE_KEY,
                        $mockAcademicYearService
                    ],
                ]);
            try {
                $priorityStudentsService = new PriorityStudentsService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $result = $priorityStudentsService->getMyStudentsDashboard($personId);
                $this->assertEquals($result, $expectedResult);
            } catch (SynapseException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
                'examples' => [
                    // case 1 : Person not found throws exception
                    [
                        1234,
                        'person_not_found',
                        'Person not found'
                    ],
                    // case 2 : Valid person id, returns my students dashboard details
                    [
                        1234,
                        '',
                        $this->setPersonDtoResponse(1234)
                    ],
                    // case 3 : Different person id, returns my students dashboard details
                    [
                        1235,
                        '',
                        $this->setPersonDtoResponse(1235)
                    ],

                ]
            ]
        );
    }

    /**
     * Get student count by individual risk level
     *
     * @return array
     */
    private function getStudentCountByRiskLevel()
    {
        $studentCountByRiskLevelArray = [
            0 => [
                'risk_level' => 1,
                'count' => 601,
                'risk_text' => 'red2',
                'image_name' => 'risk-level-icon-r2.png',
                'color_hex' => '#c70009'
            ],
            1 => [
                'risk_level' => 2,
                'count' => 774,
                'risk_text' => 'red',
                'image_name' => 'risk-level-icon-r1.png',
                'color_hex' => '#f72d35'
            ],
            2 => [
                'risk_level' => 3,
                'count' => 617,
                'risk_text' => 'yellow',
                'image_name' => 'risk-level-icon-y.png',
                'color_hex' => '#fec82a'
            ],
            3 => [
                'risk_level' => 4,
                'count' => 5717,
                'risk_text' => 'green',
                'image_name' => 'risk-level-icon-g.png',
                'color_hex' => '#95cd3c'
            ],
            4 => [
                'risk_level' => 6,
                'count' => 59,
                'risk_text' => 'gray',
                'image_name' => 'risk-level-icon-gray.png',
                'color_hex' => '#cccccc'
            ]
        ];
        return $studentCountByRiskLevelArray;
    }

    private function getRiskLevelsDetails()
    {

        $riskLevelsArray = [];
        $riskLevelsObject = new RiskLevels();

        $riskLevelsObject->setId(1);
        $riskLevelsObject->setRiskText('red2');
        $riskLevelsObject->setImageName('risk-level-icon-r2.png');
        $riskLevelsObject->setColorHex('#c70009');
        $riskLevelsArray[] = $riskLevelsObject;

        $riskLevelsObject = new RiskLevels();
        $riskLevelsObject->setId(2);
        $riskLevelsObject->setRiskText('red');
        $riskLevelsObject->setImageName('risk-level-icon-r1.png');
        $riskLevelsObject->setColorHex('#f72d35');
        $riskLevelsArray[] = $riskLevelsObject;

        $riskLevelsObject = new RiskLevels();
        $riskLevelsObject->setId(3);
        $riskLevelsObject->setRiskText('yellow');
        $riskLevelsObject->setImageName('risk-level-icon-y.png');
        $riskLevelsObject->setColorHex('#fec82a');
        $riskLevelsArray[] = $riskLevelsObject;

        $riskLevelsObject = new RiskLevels();
        $riskLevelsObject->setId(4);
        $riskLevelsObject->setRiskText('green');
        $riskLevelsObject->setImageName('risk-level-icon-g.png');
        $riskLevelsObject->setColorHex('#95cd3c');
        $riskLevelsArray[] = $riskLevelsObject;

        $riskLevelsObject = new RiskLevels();
        $riskLevelsObject->setId(6);
        $riskLevelsObject->setRiskText('gray');
        $riskLevelsObject->setImageName('risk-level-icon-gray.png');
        $riskLevelsObject->setColorHex('#cccccc');
        $riskLevelsArray[] = $riskLevelsObject;

        return $riskLevelsArray;
    }

    private function setPersonDtoResponse($personId)
    {
        $riskLevelsArray = [];
        $personDtoObject = new PersonDTO();
        $personDtoObject->setPersonId($personId);
        $personDtoObject->setTotalStudents(7768);
        $personDtoObject->setTotalHighPriorityStudents(100);

        $riskLevelsDtoObject = new RiskLevelsDto();
        $riskLevelsDtoObject->setRiskLevel('red2');
        $riskLevelsDtoObject->setTotalStudents(601);
        $riskLevelsDtoObject->setRiskPercentage(8);
        $riskLevelsDtoObject->setColorValue('#c70009');
        $riskLevelsArray[] = $riskLevelsDtoObject;

        $riskLevelsDtoObject = new RiskLevelsDto();
        $riskLevelsDtoObject->setRiskLevel('red');
        $riskLevelsDtoObject->setTotalStudents(774);
        $riskLevelsDtoObject->setRiskPercentage(10);
        $riskLevelsDtoObject->setColorValue('#f72d35');
        $riskLevelsArray[] = $riskLevelsDtoObject;

        $riskLevelsDtoObject = new RiskLevelsDto();
        $riskLevelsDtoObject->setRiskLevel('yellow');
        $riskLevelsDtoObject->setTotalStudents(617);
        $riskLevelsDtoObject->setRiskPercentage(8);
        $riskLevelsDtoObject->setColorValue('#fec82a');
        $riskLevelsArray[] = $riskLevelsDtoObject;

        $riskLevelsDtoObject = new RiskLevelsDto();
        $riskLevelsDtoObject->setRiskLevel('green');
        $riskLevelsDtoObject->setTotalStudents(5717);
        $riskLevelsDtoObject->setRiskPercentage(74);
        $riskLevelsDtoObject->setColorValue('#95cd3c');
        $riskLevelsArray[] = $riskLevelsDtoObject;

        $riskLevelsDtoObject = new RiskLevelsDto();
        $riskLevelsDtoObject->setRiskLevel('gray');
        $riskLevelsDtoObject->setTotalStudents(59);
        $riskLevelsDtoObject->setRiskPercentage(1);
        $riskLevelsDtoObject->setColorValue('#cccccc');
        $riskLevelsArray[] = $riskLevelsDtoObject;

        $personDtoObject->setRiskLevels($riskLevelsArray);
        return $personDtoObject;
    }
}