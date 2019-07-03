<?php
namespace Synapse\SurveyBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\LoggerHelperService;

class FactorServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testListFactorOnPermission()
    {
        $this->specify("Test Listing of Factors", function ($surveyId, $orgId, $userId, $surveyBlockArr, $factorArr)
        {
            
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', [
                'getRepository'
            ]);
            
            $factorLangRepo = $this->getMock('FactorLangRepository', [
                'getFactorsBasedOnSurveyBlocks'
            ]);
            $organizationLangRepo = $this->getMock('OrganizationlangRepository', [
                'findOneBy'
            ]);
            $factorRepo = $this->getMock('FactorRepository', []);
            $factorQuestion = $this->getMock('FactorQuestionsRepository', []);
            
            $langMock = $this->getMock('\Synapse\CoreBundle\Entity\LanguageMaster', [
                'getId'
            ]);
            $langMock->method('getId')
                ->willReturn(1);
            
            $orglangMock = $this->getMock('Synapse\CoreBundle\Entity\OrganizationLang', [
                'getLang'
            ]);
            $orglangMock->method('getLang')
                ->willReturn($langMock);
            
            $organizationLangRepo->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($orglangMock));
            
            $mockLogger = $this->getMock('Logger', [
                'debug',
                'error',
                'info'
            ]);
            $mockContainer = $this->getMock('Container', [
                'get'
            ]);
            
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                [
                    'SynapseSurveyBundle:FactorLang',
                    $factorLangRepo
                ],
                [
                    'SynapseCoreBundle:OrganizationLang',
                    $organizationLangRepo
                ],
                [
                    'SynapseSurveyBundle:Factor',
                    $factorRepo
                ],
                [
                    'SynapseSurveyBundle:FactorQuestions',
                    $factorQuestion
                ]
            ]);
            
            $mockManager = $this->getMockBuilder('Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager')
                ->disableOriginalConstructor()
                ->setMethods([
                'getAccessMap'
            ])
                ->getMock();
            
            $mockManager->method('getAccessMap')
                ->willReturn($surveyBlockArr);
            
            $mockContainer->method('get')
                ->willReturnMap([
                [
                    'tinyrbac.manager',
                    $mockManager
                ]
            ]);
            
            $factorLangRepo->expects($this->any())
                ->method('getFactorsBasedOnSurveyBlocks')
                ->will($this->returnValue($factorArr));
            
            $loggerService = new LoggerHelperService($mockRepositoryResolver, $mockLogger, $mockContainer);
            
            $factorService = new FactorService($mockRepositoryResolver, $mockLogger, $mockContainer);
            
            $data = $factorService->listFactorOnPermission($orgId, $userId, $surveyId);
            
            $factorDataArr = $data->getFactors();
            $factorCount = count($factorDataArr);
            $this->assertEquals($factorCount, $data->getTotalCount());
            foreach ($factorDataArr as $key => $factor) {
                
                $this->assertInstanceOf("Synapse\SurveyBundle\EntityDto\FactorsArrayDto", $factor);
                $this->assertEquals($factorArr[$key]['factor_id'], $factor->getId());
                $this->assertEquals($factorArr[$key]['sequence'], $factor->getSequence());
            }
            
        }, [
            'examples' => [
                [
                    1,1,1,$this->surveyBlockArr(),$this->fatorListArr()
                ],
                [
                    1,1,1,[],[]
                ]
            ]
        ]);
    }

    private function surveyBlockArr()
    {
        return [
            'surveyBlocks' => [
                [
                    'id' => 7,
                    'value' => '*'
                ]
            ]
        ];
    }

    private function fatorListArr()
    {
        return [
            [
                'factor_id' => 1,
                'factorName' => "Factor Name1",
                'sequence' => 1
            ],
            [
                'factor_id' => 2,
                'factorName' => "Factor Name2",
                'sequence' => 2
            ]
        ];
    }
}