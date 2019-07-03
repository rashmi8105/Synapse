<?php
use Codeception\Util\Stub;

class UploadFileLogServiceTest extends \Codeception\TestCase\Test
{

    /**
    * @var UnitTester
    */
    protected $tester;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Service\UploadFileLogService
     */
    private $uploadFileLogService;
    
    private $loggedInUser = 1;
    private $orgId = 1;
    
    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->uploadFileLogService = $this->container
            ->get('upload_file_log_service');
    }

    public function testCreateStudentUploadLog()
    {
        $uploadFileLog = $this->createTestLog(333, 555, 'ABC123');

        $this->assertEquals('S', $uploadFileLog->getUploadType());
        $this->assertEquals('333', $uploadFileLog->getOrganizationId());
        $this->assertEquals('This, Is, A, Test', $uploadFileLog->getUploadedColumns());
        $this->assertEquals('112233', $uploadFileLog->getUploadedRowCount());
        $this->assertEquals('Q', $uploadFileLog->getStatus());
        $this->assertEquals('ABC123', $uploadFileLog->getJobNumber());
        $this->assertEquals('555', $uploadFileLog->getPersonId());
    }

    public function testFindAllStudentUploadLogs()
    {
        $createdUploads = array(
            $this->createTestLog(),
            $this->createTestLog()
        );

        $uploadFileLogs = $this->uploadFileLogService->findAllStudentUploadLogs($createdUploads[0]->getOrganizationId());
        $this->assertContains($createdUploads[0], $uploadFileLogs);
        $uploadFileLogs = $this->uploadFileLogService->findAllStudentUploadLogs($createdUploads[1]->getOrganizationId());
        $this->assertContains($createdUploads[1], $uploadFileLogs);
    }

    public function testFindOneStudentUploadLog()
    {
        $createdUpload = $this->createTestLog();

        $uploadFileLog = $this->uploadFileLogService->findOneStudentUploadLog($createdUpload->getId());

        $this->assertEquals($createdUpload->getOrganizationId(), $uploadFileLog->getOrganizationId());
    }

    public function testUpdateJobStatus()
    {
        $createdUpload = $this->createTestLog();

        $updatedFileLog = $this->uploadFileLogService->updateJobStatus($createdUpload->getJobNumber(), 'S');

        $uploadFileLog = $this->uploadFileLogService->findOneStudentUploadLog($createdUpload->getId());

        $this->assertEquals('S', $uploadFileLog->getStatus());
    }

    protected function createTestLog($organizationId = false, $personId = false, $jobNumber = false)
    {
        $uploadFileLog = $this->uploadFileLogService
            ->createStudentUploadLog(
                $organizationId ? $organizationId : rand(1, 10000),
                'upload-test-1',
                array('This', 'Is', 'A', 'Test'),
                '112233',
                $jobNumber ? $jobNumber : uniqid(),
                $personId ? $personId : rand(1, 10000)
            );

        return $uploadFileLog;
    }

    public function testListUploeadHistory()
    {
        $uploadFileLog = $this->uploadFileLogService->listHistory(1, 1,'', '','','', false,false);
        $data = $uploadFileLog['data'][0]; 
        $this->assertInstanceOf('Synapse\UploadBundle\EntityDto\UploadFileHistoryDto', $data);
        $this->assertNotEmpty($data->getId());
        $this->assertNotEmpty($data->getFileName());
        $this->assertNotEmpty($data->getUploadedBy());       
    }
    
    public function testListUploeadHistoryCSV()
    {
        $createUploadFileLog = $this->createTestLog(333, 555, 'ABC123');
        $uploadFileLog = $this->uploadFileLogService->listHistory(1, 1,'', '','','', true,false);
      
        $this->assertEquals('You may continue to use Mapworks while your download completes. We will notify you when it is available.', $uploadFileLog[0]);
    }
    
    public function testListUploeadHistoryCSVJob()
    {
        $uploadFileLog = $this->uploadFileLogService->listHistory(1, 1,'', '','','', false,true);
        $this->assertNotEmpty($uploadFileLog[0]['id']);
        $this->assertNotEmpty($uploadFileLog[0]['file_name']);
        $this->assertNotEmpty($uploadFileLog[0]['uploaded_date']);
    }
    
    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
}