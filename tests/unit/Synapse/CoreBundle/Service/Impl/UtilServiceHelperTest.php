<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Util\UtilServiceHelper;

class UtilServiceHelperTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testGetDateByTimezone()
    {
        $this->markTestSkipped("This is duplicated code and needs to be replaced with the correct utility methods later.");
        $this->specify("Test getDateByTimezone function which returns output as Ymd_HisT or specified date format", function ($orgTimezone, $currDateTime, $isValidTimeZone, $timeZone) {

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));

            $mockMetadataListValuesRepository = $this->getMock('MetadataListValuesRepository', array(
                'findByListName'
            ));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseCoreBundle:MetadataListValues',
                        $mockMetadataListValuesRepository
                    ]
                ]);

            /**
             * Mock MetadataListValues
             */
            $mockMetaDataListObj = $this->getMock('MetadataListValues', array(
                'getListValue'
            ));

            if (!empty($timeZone)) {
                $mockMetaDataListObj->expects($this->any())
                    ->method('getListValue')
                    ->will($this->returnValue($timeZone));

                $mockMetadataListValuesRepository->method('findByListName')
                    ->will($this->returnValue(array(
                        $mockMetaDataListObj
                    )));
            } else {
                $mockMetadataListValuesRepository->method('findByListName')
                    ->will($this->returnValue(false));
            }

            $utilService = new UtilServiceHelper($mockRepositoryResolver, $mockLogger);
            $response = $utilService->getDateByTimezone($orgTimezone);
            if ($isValidTimeZone) {
                $this->assertInternalType("string", $response);
                $this->assertNotNull($response);
                $this->assertStringStartsWith($currDateTime, $response);
                $this->assertEquals($currDateTime, $response);
            } else {
                $this->assertInternalType("string", $response);
                $this->assertNotNull($response);
            }
        }, [
            'examples' => [
                [
                    'PST',
                    'currentDateTime' => $this->getCurrentDateTimeByTimezone('America/Dawson_Creek'),
                    true,
                    'timeZone' => 'America/Dawson_Creek'
                ],
                [
                    'CST',
                    'currentDateTime' => $this->getCurrentDateTimeByTimezone('Canada/Saskatchewan'),
                    true,
                    'timeZone' => 'Canada/Saskatchewan'
                ],
                [
                    '',
                    'currentDateTime' => $this->getCurrentDateTimeByTimezone(''),
                    false,
                    'timeZone' => ''
                ]
            ]
        ]);
    }

    private function getCurrentDateTimeByTimezone($timeZone)
    {
        $dateTime = new \DateTime('now');
        if (!empty($timeZone)) {
            $dateTime = new \DateTime('now', new \DateTimeZone($timeZone));
        }
        $currentDateTime = $dateTime->format('Ymd_HisT');
        return $currentDateTime;
    }

    public function testGetCohotCodesForStudent()
    {
        $this->specify("Test getCohotCodesForStudent function", function ($studentId, $cohortValues) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));

            $mockOrgPersonStudentSurveyLinkRepository = $this->getMock('OrgPersonStudentSurveyLinkRepository', array(
                'getStudentCohort'
            ));

            $mockOrgPersonStudentSurveyLinkRepository->method('getStudentCohort')
                ->will($this->returnValue($cohortValues));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseSurveyBundle:OrgPersonStudentSurveyLink',
                        $mockOrgPersonStudentSurveyLinkRepository
                    ]
                ]);

            $utilService = new UtilServiceHelper($mockRepositoryResolver, $mockLogger);
            $response = $utilService->getCohotCodesForStudent($studentId);

            if (count($cohortValues) > 0) {
                $this->assertEquals(implode(",", $cohortValues), $response);
            } else {
                $this->assertEquals("-1", $response);
            }
        }, [
            'examples' => [
                [
                    1,
                    []
                ],
                [
                    1,
                    [
                        1,
                        2
                    ]
                ]
            ]

        ]);
    }

    public function testGetSortByField()
    {
        $this->specify("Test getSortByField function to return order by string", function ($sortBy, $riskLevelGrayFlag, $expectedSortByString) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error'
            ));

            $utilService = new UtilServiceHelper($mockRepositoryResolver, $mockLogger);
            $response = $utilService->getSortByField($sortBy, $this->getSortableFieldDetails(), '', $riskLevelGrayFlag);
            $this->assertInternalType('string', $response);
            $this->assertEquals($expectedSortByString, trim($response));


        }, [
            'examples' => [
                [
                    '-student_last_name',
                    false,
                    'ORDER BY    p.lastname  DESC, p.firstname  DESC'
                ],
                [
                    'student_risk_status',
                    true,
                    'ORDER BY  RL.id=(SELECT id FROM risk_level WHERE risk_text=\'gray\' AND deleted_at IS NULL) ASC,   p.risk_level  DESC  , p.lastname, p.firstname'
                ],
                [
                    '-student_risk_status',
                    true,
                    'ORDER BY  RL.id=(SELECT id FROM risk_level WHERE risk_text=\'gray\' AND deleted_at IS NULL) ASC,   p.risk_level  ASC  , p.lastname, p.firstname'
                ],
                [
                    '-student_intent_to_leave',
                    false,
                    'ORDER BY    p.intent_to_leave  DESC'
                ]

            ]

        ]);
    }

    private function getSortableFieldDetails()
    {
        return array(
            'student_last_name' => ' p.lastname [SORT_ORDER], p.firstname [SORT_ORDER]',
            'student_risk_status' => ' p.risk_level [SORT_ORDER] , p.lastname, p.firstname ',
            'student_intent_to_leave' => ' p.intent_to_leave [SORT_ORDER]',
            'student_classlevel' => ' class_level [SORT_ORDER]',
            'student_logins' => ' logged_activities [SORT_ORDER]',
            'last_activity' => ' last_activity [SORT_ORDER]',
        );
    }
}