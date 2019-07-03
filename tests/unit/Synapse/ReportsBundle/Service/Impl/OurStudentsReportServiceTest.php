<?php

namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;
use JMS\DiExtraBundle\Annotation as DI;


class OurStudentsReportServiceTest extends Unit
{
    use Specify;

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


    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockContainer = $this->getMock('Container', ['get']);
    }


    public function testGetSurveyBasedSections()
    {
        $this->specify("Verify the functionality of the method getSurveyBasedSections", function ($elementsFromFactors, $elementsFromSurveys, $expectedResult) {

            // Declaring mock services, DAOs, and repositories
            $mockOurStudentsReportDAO = $this->getMock('OurStudentsReportDAO', ['getFactorResponseCounts', 'getSurveyResponseCounts']);

            // Mock data -- the values of these variables don't matter, as this is only a test of the sorting and formatting that this function is doing.
            $loggedInUserId = 256049;
            $organizationId = 191;
            $surveyId = 11;
            $studentIds = [4556012, 4556013, 4556014, 4556015, 4556016, 4556017];

            // Mocking method calls
            $this->mockContainer->method('get')->willReturn($mockOurStudentsReportDAO);

            $mockOurStudentsReportDAO->method('getFactorResponseCounts')->willReturn($elementsFromFactors);
            $mockOurStudentsReportDAO->method('getSurveyResponseCounts')->willReturn($elementsFromSurveys);

            // Call the function to be tested and verify results.
            $ourStudentsReportService = new OurStudentsReportService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $output = $ourStudentsReportService->getSurveyBasedSections($loggedInUserId, $organizationId, $surveyId, $studentIds);

            verify($output)->equals($expectedResult);

        }, ['examples' =>
            [
                // Example -- I think I have managed to include enough variation into this example that I don't know what else I would put into additional examples.
                [
                    // Elements from factors
                    [
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 21,
                            'element_name' => 'High overall satisfaction with institution',
                            'numerator_count' => 4,
                            'denominator_count' => 6
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 39,
                            'element_name' => 'Has high test anxiety',
                            'numerator_count' => 1,
                            'denominator_count' => 6
                        ],
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 80,
                            'element_name' => 'Rates their communications skills high',
                            'numerator_count' => 5,
                            'denominator_count' => 6
                        ]
                    ],
                    // Elements from questions
                    [
                        [
                            'section_id' => 6,
                            'section_name' => 'Academics',
                            'element_id' => 164,
                            'element_name' => 'Struggling in one course',
                            'numerator_count' => 3,
                            'denominator_count' => 5
                        ],
                        [
                            'section_id' => 4,
                            'section_name' => 'Perceptions of Campus',
                            'element_id' => 172,
                            'element_name' => 'This institution was first choice',
                            'numerator_count' => 3,
                            'denominator_count' => 4
                        ]
                    ],
                    // Expected sorted and formatted data
                    [
                        [
                            'section_id' => 4,
                            'title' => 'Perceptions of Campus',
                            'elements' => [
                                [
                                    'element_id' => 172,
                                    'name' => 'This institution was first choice',
                                    'count' => 4,
                                    'percentage' => 75
                                ],
                                [
                                    'element_id' => 21,
                                    'name' => 'High overall satisfaction with institution',
                                    'count' => 6,
                                    'percentage' => 66.7
                                ]
                            ]
                        ],
                        [
                            'section_id' => 6,
                            'title' => 'Academics',
                            'elements' => [
                                [
                                    'element_id' => 80,
                                    'name' => 'Rates their communications skills high',
                                    'count' => 6,
                                    'percentage' => 83.3
                                ],
                                [
                                    'element_id' => 164,
                                    'name' => 'Struggling in one course',
                                    'count' => 5,
                                    'percentage' => 60
                                ],
                                [
                                    'element_id' => 39,
                                    'name' => 'Has high test anxiety',
                                    'count' => 6,
                                    'percentage' => 16.7
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

}