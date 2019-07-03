<?php
namespace tests\unit\Synapse\CoreBundle\Service\Util;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use PHPUnit_Framework_MockObject_MockObject;
use Synapse\CoreBundle\SynapseConstant;


class DateUtilityServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    use \Codeception\Specify;


    // tests
    public function testConvertToUtcDatetime(){
        $this->specify("Test if UTC time is retrieved in different scenerios", function ($throwException, $timeZoneReturn, $userDate, $isEndDate, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug','error'));

            $mockOrganizationRepo = $this->getMock('orgRepository', array('find'));
            $mockMetaList = $this->getMock('metalistFinder', array('findOneBy'));
            $mockOrg = $this->getMock('reportOrganization', array('getTimeZone'));
            $timeZoneName = "Eastern";
            $mockTimeZone = $this->getMock('timeZoneKey', array('getListValue'));

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [['SynapseCoreBundle:Organization', $mockOrganizationRepo],
                ['SynapseCoreBundle:MetadataListValues', $mockMetaList]]
            );

            $mockOrganizationRepo->expects($this->at(0))->method('find')->willReturn($mockOrg);
            $mockOrg->expects($this->at(0))->method('getTimeZone')->willReturn($timeZoneName);

            if($timeZoneReturn) {
                if ($throwException) {
                    $mockMetaList->expects($this->at(0))->method('findOneBy')->willThrowException(new SynapseValidationException('Timezone does not exist'));
                } else {
                    $mockMetaList->expects($this->at(0))->method('findOneBy')->willReturn($mockTimeZone);
                    $mockTimeZone->expects($this->at(0))->method('getListValue')->willReturn($timeZoneReturn);
                }
            }else{
                $mockMetaList->expects($this->at(0))->method('findByListName')->willReturn($timeZoneReturn);
            }

            $dateUtility = new DateUtilityService($mockRepositoryResolver, $mockLogger);

            //org portion is mocked away
            try {
                $functionResults = $dateUtility->convertToUtcDatetime($orgId = 1, $userDate, $isEndDate);
            } catch (\Exception $e) {
                $functionResults = $e->getMessage();
            }

            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples'=>[
            //Converting Start Date Canada Time
            [false, "Canada/Eastern", "2015-09-09", false, "2015-09-09 04:00:00"],
            //Converting Start Date Canada Time
            [false, "Canada/Eastern", "2015-12-09", false, "2015-12-09 05:00:00"],
            //Null time zone results in no timezone change Start Date
            [false, null, "2015-09-09", false, "2015-09-09 00:00:00"],
            //Empty Array time zone results in no timezone change Start Date
            [false, array(), "2015-09-09", false, "2015-09-09 00:00:00"],
            //Converting Eastern US Start Date
            [false, "US/Eastern", "2015-09-09", false, "2015-09-09 04:00:00"],
            //Converting Eastern US Start Date
            [false, "US/Eastern", "2015-12-09", false, "2015-12-09 05:00:00"],
            //Converting Eastern US End Date
            [false, "US/Eastern", "2015-12-09", true, "2015-12-10 04:59:59"],
            //Null time zone results in no timezone change End Date
            [false, null, "2015-12-09", true, "2015-12-09 23:59:59"],
            //Empty Array time zone results in no timezone change End Date
            [false, array(), "2015-12-09", true, "2015-12-09 23:59:59"],
            //Time Zone Doesn't Exist Exception
            [true, "Rainbow", "2015-09-09", false, "Timezone does not exist"]
        ]]);
    }

    public function testAdjustDateTimeToOrganizationTimezone(){
        $this->specify("adjust DateTime To Organization Timezone", function ($throwError, $timeZoneKey, $dateTimeString, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug','error'));

            $mockOrganizationRepo = $this->getMock('orgRepository', array('find'));
            $mockMetaList = $this->getMock('MetadataListValuesRepository', array('findOneBy'));
            $timeZoneName = "Eastern";

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [['SynapseCoreBundle:Organization', $mockOrganizationRepo],
                    ['SynapseCoreBundle:MetadataListValues', $mockMetaList]]
            );

            $mockOrganization = $this->getMock('Organization', ['getTimeZone']);
            $mockOrganizationRepo->expects($this->once())->method('find')->willReturn($mockOrganization);

            $mockOrganization->expects($this->once())->method('getTimeZone')->willReturn($timeZoneName);

            $mockMetadataListValues = $this->getMock('MetadataListValues',['getListValue']);

            if ($throwError) {
                $mockMetaList->expects($this->once())->method('findOneBy')->willThrowException(new SynapseValidationException('Timezone does not exist'));

            } else {
                $mockMetaList->expects($this->once())->method('findOneBy')->willReturn($mockMetadataListValues);
                $mockMetadataListValues->expects($this->once())->method('getListValue')->willReturn($timeZoneKey);
            }


            $dateUtility = new DateUtilityService($mockRepositoryResolver, $mockLogger);

            try {
                $orgDateTime = $dateUtility->adjustDateTimeToOrganizationTimezone($orgId = 1, new \DateTime($dateTimeString));
                $functionResults = $orgDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            } catch (\Exception $e) {
                $functionResults = $e->getMessage();
            }

            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples'=>[
            //Canada Eastern, 4 hours back
            [false, "Canada/Eastern", "2015-09-09 22:30:00", "2015-09-09 18:30:00"],
            //US Eastern, 4 hours back
            [false, "US/Eastern", "2015-09-09 22:30:00", "2015-09-09 18:30:00"],
            //Asia, Previous Day
            [false, "Asia/Kolkata", "2015-09-09 22:30:00", "2015-09-10 04:00:00"],
            //Throwing Exception
            [true,"TwilightTime","2015-09-09 22:30:00", "Timezone does not exist" ]
        ]]);
    }

    public function testBuildDateTimeRangeByTimePeriodAndDateTimeObject() {
        $this->specify("Validate BuildDateTimeRangeByTimePeriod", function ($timePeriod, $dateTimeString,$expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug','error'));

            $dateTimeObject = new \DateTime($dateTimeString);

            $dateUtility = new DateUtilityService($mockRepositoryResolver, $mockLogger);
            $dateArray = $dateUtility->buildDateTimeRangeByTimePeriodAndDateTimeObject($timePeriod, $dateTimeObject);
            $this->assertEquals($expectedResult, $dateArray);

        }, ['examples'=>[
            //Today
            ['today', '2016-01-03', ['from_date' => '2016-01-03 00:00:00', 'to_date' => '2016-01-03 23:59:59']],
            //Week (Sunday)
            ['week', '2016-01-10', ['from_date' => '2016-01-04 00:00:00', 'to_date' => '2016-01-10 23:59:59']],
            //Week (Monday)
            ['week', '2016-01-11', ['from_date' => '2016-01-11 00:00:00', 'to_date' => '2016-01-17 23:59:59']],
            //Week (not sunday or monday)
            ['week', '2016-01-06', ['from_date' => '2016-01-04 00:00:00', 'to_date' => '2016-01-10 23:59:59']],
            //Month
            ['month', '2016-01-05', ['from_date' => '2016-01-01 00:00:00', 'to_date' => '2016-01-31 23:59:59']],
            //Default
            ['goofy', '2016-01-05', ['from_date' => '2016-01-04 00:00:00', 'to_date' => '2016-01-10 23:59:59']]
        ]]);
        }

    public function testAdjustOrganizationDateTimeStringToUtcDateTimeObject() {
        $this->specify("Validate BuildDateTimeRangeByTimePeriod", function ($throwException, $dateTimeString, $organizationId, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug','error'));


            $mockOrganizationRepository = $this->getMock('orgRepository', array('find'));
            $mockMetaList = $this->getMock('MetadataListValuesRepository', array('findOneBy'));
            $timeZoneName = "Central";
            $timeZoneKey = "US/Central";

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [['SynapseCoreBundle:Organization', $mockOrganizationRepository],
                    ['SynapseCoreBundle:MetadataListValues', $mockMetaList]]
            );

            $mockOrganization = $this->getMock('Organization', ['getTimeZone']);
            $mockOrganizationRepository->expects($this->once())->method('find')->willReturn($mockOrganization);

            $mockOrganization->expects($this->once())->method('getTimeZone')->willReturn($timeZoneName);

            $mockMetadataListValues = $this->getMock('MetadataListValues',['getListValue']);

            if ($throwException) {
                $mockMetaList->expects($this->once())->method('findOneBy')->willThrowException(new SynapseValidationException('Timezone does not exist'));
            } else {
                $mockMetaList->expects($this->once())->method('findOneBy')->willReturn($mockMetadataListValues);
                $mockMetadataListValues->expects($this->once())->method('getListValue')->willReturn($timeZoneKey);
            }


            $dateUtility = new DateUtilityService($mockRepositoryResolver, $mockLogger);

            try {
                $dateTimeObject = $dateUtility->adjustOrganizationDateTimeStringToUtcDateTimeObject($dateTimeString, $organizationId);
                $functionResults = $dateTimeObject->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            } catch (\Exception $e) {
                $functionResults = $e->getMessage();
            }
            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples'=>[
            //6 hours during daylight savings
            [false, '2016-01-01 23:00:00', 1, '2016-01-02 05:00:00'],
            //5 hours during no daylight savings
            [false, '2016-06-01 23:00:00', 1, '2016-06-02 04:00:00'],
            //DAY OF DAYLIGHT SAVINGS BEFORE
            [false, '2016-11-06 01:00:00', 1, '2016-11-06 06:00:00'],
            //DAY OF DAYLIGHT SAVINGS AFTER
            [false, '2016-11-06 03:00:00', 1, '2016-11-06 09:00:00'],
            //BELOW SCENERIO COULD RESULT IN 25 HOUR DAY
            //INCOMING DAY ON DAYLIGHT SAVINGS 5 HOURS DIFFERENCE
            [false, '2016-11-06 00:00:00', 1, '2016-11-06 05:00:00'],
            //OUTGOING END DAY ON DAYLIGHT SAVINGS 6 HOURS DIFFERENCE
            [false, '2016-11-06 23:59:59', 1, '2016-11-07 05:59:59'],
            //Time Zone Doesn't Exist Exception
            [true, '2016-11-06 23:59:59', 1, 'Timezone does not exist']
        ]]);
    }

    public function testGetCurrentUtcDateStringFromOrganizationDateTimeString() {
        $this->specify("Validate BuildDateTimeRangeByTimePeriod", function ($throwError, $format, $dateTimeString, $organizationId, $expectedResult) {
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug','error'));


            $mockOrganizationRepository = $this->getMock('orgRepository', array('find'));
            $mockMetaList = $this->getMock('MetadataListValuesRepository', array('findOneBy'));
            $timeZoneName = "Central";
            $timeZoneKey = "US/Central";

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [['SynapseCoreBundle:Organization', $mockOrganizationRepository],
                    ['SynapseCoreBundle:MetadataListValues', $mockMetaList]]
            );

            $mockOrganization = $this->getMock('Organization', ['getTimeZone']);
            $mockOrganizationRepository->expects($this->once())->method('find')->willReturn($mockOrganization);

            $mockOrganization->expects($this->once())->method('getTimeZone')->willReturn($timeZoneName);
            $mockMetadataListValues = $this->getMock('MetadataListValues',['getListValue']);

            if ($throwError) {
                $mockMetaList->expects($this->once())->method('findOneBy')->willThrowException(new SynapseValidationException('Timezone does not exist'));

            } else {
                $mockMetaList->expects($this->once())->method('findOneBy')->willReturn($mockMetadataListValues);
                $mockMetadataListValues->expects($this->once())->method('getListValue')->willReturn($timeZoneKey);
            }

            $dateUtility = new DateUtilityService($mockRepositoryResolver, $mockLogger);
            try {
                $functionResults = $dateUtility->getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString($dateTimeString, $organizationId, $format);
            } catch (\Exception $e) {
                $functionResults = $e->getMessage();
            }

            $this->assertEquals($expectedResult, $functionResults);

        }, ['examples'=>[
            //6 hours during daylight savings
            [false, 'Y-m-d', '2016-01-01 23:00:00', 1, '2016-01-02'],
            //5 hours during no daylight savings
            [false,'Y-m-d', '2016-06-01 23:00:00', 1, '2016-06-02'],
            //DAY OF DAYLIGHT SAVINGS BEFORE
            [false,'Y-m-d', '2016-11-06 01:00:00', 1, '2016-11-06'],
            //DAY OF DAYLIGHT SAVINGS AFTER
            [false,'Y-m-d', '2016-11-06 03:00:00', 1, '2016-11-06'],
            //BELOW SCENERIO COULD RESULT IN 25 HOUR DAY
            //INCOMING DAY ON DAYLIGHT SAVINGS 5 HOURS DIFFERENCE
            [false,'Y-m-d', '2016-11-06 00:00:00', 1, '2016-11-06'],
            //OUTGOING END DAY ON DAYLIGHT SAVINGS 6 HOURS DIFFERENCE
            [false,'Y-m-d', '2016-11-06 23:59:59', 1, '2016-11-07'],
            //6 hours during daylight savings (Different Format 'd/m/Y')
            [false,'d/m/Y', '2016-01-01 23:00:00', 1, '02/01/2016'],
            //6 hours during daylight savings (Different Format 'd/m/Y H:i:s')
            [false,'d/m/Y H:i:s', '2016-01-01 23:00:00', 1, '02/01/2016 05:00:00'],
            //Throwing An Error if Timezone doesn't exist
            [true,'d/m/Y H:i:s', '2016-01-01 23:00:00', 1, 'Timezone does not exist']
        ]]);
    }

    public function testGetOrganizationISOTimeZone() {
        $this->specify('test getReadableTimeZoneForOrganization', function($organizationRepositoryMock, $metadataListValuesRepositoryMock, $expectedResults){

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug','error'));

            $mockRepositoryResolver->method('getRepository')->willReturnMap(
                [
                    [OrganizationRepository::REPOSITORY_KEY, $organizationRepositoryMock],
                    [MetadataListValuesRepository::REPOSITORY_KEY, $metadataListValuesRepositoryMock]
                ]
            );

            $organizationId = "Mocked Away";
            try {
                $dateUtility = new DateUtilityService($mockRepositoryResolver, $mockLogger);
                $functionResults = $dateUtility->getOrganizationISOTimeZone($organizationId);
            } catch (\Exception $e) {
                $functionResults = $e->getMessage();
            }

            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [
            // covers all lines of code
            [
                $this->createMockObject('OrganizationRepository', ['find' => $this->createMockObject('organization', ['getTimeZone' => 'mocked away'])]),
                $this->createMockObject('MetadataListValuesRepository', ['findOneBy' => $this->createMockObject('metadataListValues', ['getListValue' => 'mocked away'])]),
                'mocked away'
            ],
            // will throw error on line $organizationObject = $this->organizationRepository->find($organizationId, new SynapseValidationException('Organization does not exist'));
            [
                $this->createMockErrorObject('OrganizationRepository', ['find' => new SynapseValidationException('mocked away')]),
                $this->createMockObject('MetadataListValuesRepository', ['findOneBy' => $this->createMockObject('metadataListValues', ['getListValue' => 'mocked away'])]),
                'mocked away'
            ],
            // will throw error on line $organizationTimeZone = $organizationObject->getTimeZone();
            [
                $this->createMockObject('OrganizationRepository', ['find' => $this->createMockErrorObject('organization', ['getTimeZone' => new SynapseValidationException('mocked away')])]),
                $this->createMockObject('MetadataListValuesRepository', ['findOneBy' => $this->createMockObject('metadataListValues', ['getListValue' => 'mocked away'])]),
                'mocked away'
            ],
            // will throw error on line $readableOrganizationTimeZone = $this->metadataListValuesRepository->findOneBy(['listName' => $organizationTimeZone], new SynapseValidationException('TimeZone does not exist'));
            [
                $this->createMockObject('OrganizationRepository', ['find' => $this->createMockObject('organization', ['getTimeZone' => 'mocked away'])]),
                $this->createMockErrorObject('MetadataListValuesRepository', ['findOneBy' => new SynapseValidationException('mocked away')]),
                'mocked away'
            ],
            // will throw error on line $readableOrganizationTimeZone->getListValue();
            [
                $this->createMockObject('OrganizationRepository', ['find' => $this->createMockObject('organization', ['getTimeZone' => 'mocked away'])]),
                $this->createMockObject('MetadataListValuesRepository', ['findOneBy' => $this->createMockErrorObject('metadataListValues', ['getListValue' => new SynapseValidationException('mocked away')])]),
                'mocked away'
            ]
        ]]);
    }

    /**
     * creates mock objects that will return different things for each function
     *
     * @param $objectName
     * @param $arrayOfFunctions
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockObject($objectName, $arrayOfFunctions){
        $functionNames = array_keys($arrayOfFunctions);
        $mockObject = $this->getMock($objectName, $functionNames);
        foreach ($arrayOfFunctions as $functionName => $functionReturnValue) {
            $mockObject->method($functionName)->willReturn($functionReturnValue);
        }
        return $mockObject;
    }


    /**
     * creates mock objects that will return different things for each function
     *
     * @param $objectName
     * @param $arrayOfFunctions
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockErrorObject($objectName, $arrayOfFunctions){
        $functionNames = array_keys($arrayOfFunctions);
        $mockObject = $this->getMock($objectName, $functionNames);
        foreach ($arrayOfFunctions as $functionName => $functionReturnValue) {
            $mockObject->method($functionName)->willThrowException($functionReturnValue);
        }
        return $mockObject;
    }

}