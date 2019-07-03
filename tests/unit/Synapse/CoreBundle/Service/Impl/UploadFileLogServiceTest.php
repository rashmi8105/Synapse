<?php
namespace Synapse\CoreBundle\Service\Impl;

use Symfony\Bridge\Monolog\Logger;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;

class UploadFileLogServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;
    
    private $orgId = 1;
    
    private $startPoint = '';
     
    private $endPoint = '';
     
    private $sortByField = '';
     
    private $order = '';
     
    private $isCsv = false;
     
    private $uploadType = '';
     
    private $isJob = false;
   // TODO: Test case need to be modify
   /* public function testListHistory()
    {
        
        $this->specify("Test fetching upload history data for CSV", function ($orgId = 1, $startPoint, $endPoint, $sortByField, $order, $isCsv= false, $uploadType, $isJob) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug','error'));
            $mockRiskCalculationRepository = $this->getMock('MetadataListValuesRepository');
            $mockRiskCalculationRepository = $this->getMock('PersonRepository', array('find'));
            $mockRiskCalculationRepository = $this->getMock('UploadFileLogRepository', array('listHistory'));
            
            $mockContainer = $this->getMock('Container', array('get'));
            
            $mockResque = $this->getMock('resque', array('enqueue'));
            $mockCache = $this->getMock('cache');
            $orgService = $this->getMock('OrganizationService', array('find'));
            $personService = $this->getMock('PersonService', array('find'));
            
            $ebiConfigService =  $this->getMock('EbiConfigService', array('find'));
            
            //$mockRepositoryResolver->expects($this->once())->method('getRepository')->with($this->equalTo(RiskCalculationService::ORG_RISK_CAL_INPUTS))->willReturn($mockRiskCalculationRepository);
            $mockContainer->expects($this->at(0))->method('get')->with($this->equalTo('org_service'))->willReturn($orgService);
            $mockContainer->expects($this->at(1))->method('get')->with($this->equalTo('person_service'))->willReturn($personService);            
            
            $mockContainer->expects($this->at(2))->method('get')->with($this->equalTo('ebi_config_service'))->willReturn($ebiConfigService);
            $mockContainer->expects($this->at(3))->method('get')->with($this->equalTo('synapse_redis_cache'))->willReturn($mockCache);
            $mockContainer->expects($this->at(4))->method('get')->with($this->equalTo('bcc_resque.resque'))->willReturn($mockResque);
         
            $uploadFileLogService = new UploadFileLogService($mockRepositoryResolver, $mockLogger, $mockCache, $mockContainer, $ebiConfigService, $mockResque);
            
            
            // Fetching data for csv 
            $uploadFileLogServiceData = $uploadFileLogService->listHistory($orgId, $startPoint, $endPoint, $sortByField, $order, false, $uploadType, $isJob);
            
            // Asserting values
            $this->assertEquals(1, $uploadFileLogServiceData[0]['id']);

        },['examples'=>[[y,y,y,y,y,y,y,y]]]);
    }*/

    
}