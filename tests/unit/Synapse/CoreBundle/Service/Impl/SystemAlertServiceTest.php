<?php
use Synapse\CoreBundle\Entity\SystemAlerts;
use Synapse\CoreBundle\Repository\SystemAlertRepository;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Service\Impl\SystemAlertService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\SystemAlertDto;


class SystemAlertServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testCreateSystemAlert()
    {
        $this->specify("Test to createSystemAlert", function ($systemAlertArray, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            // Mock LoggerHelperService
            $mockLoggerHelperService = $this->getMock('LoggerHelperService',['getLog']);
            $mockLoggerHelperService->method('getLog')->willReturn('Testing logger');

            // Mock SystemAlertRepository
            $mockSystemAlertRepository = $this->getMock('SystemAlertRepository', ['createSystemAlert', 'flush']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                ]);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [SystemAlertRepository::REPOSITORY_KEY, $mockSystemAlertRepository],
                ]);

                $systemAlertService = new SystemAlertService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $systemAlertService->createSystemAlert($this->getSystemAlertDto($systemAlertArray));
                $this->assertEquals($results, $expectedResult);
        }, [
            'examples' => [

                // Test 01 - passing all Valid data will create system alert
                [
                    [
                      'message' => 'Test',
                      'is_enabled' => true,
                      'start_date_time' => '2017-05-06 16:34:52',
                      'end_date_time' => '2017-07-06 16:34:52',
                    ],
                    $this->getSystemAlertsResponse('Test', 1, '2017-05-06 16:34:52', '2017-07-06 16:34:52')
                ],
                // Test 02 - If message field is empty|null will create system alert with an empty message
                [
                    [
                        'message' => '',
                        'is_enabled' => true,
                        'start_date_time' => '2017-05-06 16:34:52',
                        'end_date_time' => '2017-07-06 16:34:52',
                    ],
                    $this->getSystemAlertsResponse(null, 1, '2017-05-06 16:34:52', '2017-07-06 16:34:52')
                ],
                // Test 03 - If is_enabled field is empty|null will create system alert with 0 is_enabled value
                [
                    [
                        'message' => 'Test',
                        'is_enabled' => null,
                        'start_date_time' => '2017-05-06 16:34:52',
                        'end_date_time' => '2017-07-06 16:34:52',
                    ],
                    $this->getSystemAlertsResponse('Test', 0, '2017-05-06 16:34:52', '2017-07-06 16:34:52')
                ]
            ]
        ]);
    }

    private function getSystemAlertDto($systemAlertArray)
    {
        $systemAlertDto = new SystemAlertDto();
        $systemAlertDto->setMessage($systemAlertArray['message']);
        $systemAlertDto->setIsEnabled($systemAlertArray['is_enabled']);
        if (isset($systemAlertArray['start_date_time'])) {
            $systemAlertDto->setStartDateTime(new \DateTime($systemAlertArray['start_date_time']));
        }
        if (isset($systemAlertArray['end_date_time'])) {
            $systemAlertDto->setEndDateTime(new \DateTime($systemAlertArray['end_date_time']));
        }
        return $systemAlertDto;
    }

    private function getSystemAlertsResponse($message, $isEnabled, $startDateTime, $endDateTime)
    {
        $systemAlerts = new SystemAlerts();
        $systemAlerts->setDescription($message);
        $systemAlerts->setIsEnabled($isEnabled);
        if (is_null($startDateTime) || is_null($endDateTime)) {
            $startDateTime = new \DateTime();
            $endDateTime = new \DateTime();
            $endDateTime->add(new \DateInterval(SynapseConstant::DATE_INTERVAL));
        } else {
            $startDateTime = new \DateTime($startDateTime);
            $endDateTime = new \DateTime($endDateTime);
        }
        $systemAlerts->setStartDate($startDateTime);
        $systemAlerts->setEndDate($endDateTime);
        return $systemAlerts;
    }
}