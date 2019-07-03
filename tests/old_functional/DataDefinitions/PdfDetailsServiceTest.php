<?php
use Codeception\Util\Stub;

class PdfDetailsServiceTest extends \Codeception\TestCase\Test
{
    /**
     *
     * @var UnitTester
     */
    protected $tester;
    
    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    /**
     *
     * @var \Synapse\PdfBundle\Service\Impl\PdfDetailsService
     */
    private $pdfDetailsService;
    
    private $organization = 1;
    
    public function _before() {
        $this->container = $this->getModule ( 'Symfony2' )->container;
        $this->pdfDetailsService = $this->container->get ( 'pdf_service' );
    }
    
    public function testGetFacultyUploadPdfDetails(){
        $details = $this->pdfDetailsService->getFacultyUploadPdfDetails();
        $uploadDetails = $details[0];
        $this->assertInternalType('array', $uploadDetails);
        foreach($uploadDetails as $uploadDetail){
            $this->assertArrayHasKey('column_name', $uploadDetail);
            $this->assertArrayHasKey('data_type', $uploadDetail);
            $this->assertArrayHasKey('length', $uploadDetail);
        }
        $this->assertInternalType('string', $details[1]);
    }
    
    public function testGetStudentUploadPdfDetails(){
        $details = $this->pdfDetailsService->getStudentUploadPdfDetails($this->organization);
        $this->assertInternalType('string', $details);
        $this->assertNotEmpty($details);
        $this->assertNotInternalType('array', $details);
    }
    
    public function testGetCourseUploadPdfDetails(){
        $details = $this->pdfDetailsService->getCourseUploadPdfDetails();
        $uploadDetails = $details[0];
        $this->assertInternalType('array', $uploadDetails);
        foreach($uploadDetails as $uploadDetail){
            $this->assertArrayHasKey('column_name', $uploadDetail);
            $this->assertArrayHasKey('data_type', $uploadDetail);
            $this->assertArrayHasKey('length', $uploadDetail);
        }
        $this->assertInternalType('string', $details[1]);
    }
    
    public function testGetCourseFacultyUploadPdfDetails(){
        $details = $this->pdfDetailsService->getCourseFacultyUploadPdfDetails();
        $this->assertInternalType('string', $details);
        $this->assertNotEmpty($details);
        $this->assertNotInternalType('array', $details);
    }
    
    public function testGetCourseStudentsUploadPdfDetails(){
        $details = $this->pdfDetailsService->getCourseStudentsUploadPdfDetails();
        $uploadDetails = $details[0];
        $this->assertInternalType('array', $uploadDetails);
        foreach($uploadDetails as $uploadDetail){
            $this->assertArrayHasKey('column_name', $uploadDetail);
            $this->assertArrayHasKey('data_type', $uploadDetail);
            $this->assertArrayHasKey('length', $uploadDetail);
        }
        $this->assertInternalType('string', $details[1]);
    }
    
    public function testGetAcademicUpdateUploadPdfDetails(){
        $details = $this->pdfDetailsService->getAcademicUpdateUploadPdfDetails();
        $this->assertInternalType('string', $details);
        $this->assertNotEmpty($details);
        $this->assertNotInternalType('array', $details);
    }
    
    public function testGetSubGroupsUploadPdfDetails(){
        $details = $this->pdfDetailsService->getSubGroupsUploadPdfDetails();
        $this->assertInternalType('string', $details);
        $this->assertNotEmpty($details);
        $this->assertNotInternalType('array', $details);
    }
    
    public function testGetGroupsFacultyUploadPdfDetails(){
        $details = $this->pdfDetailsService->getGroupsFacultyUploadPdfDetails($this->organization);
        $this->assertInternalType('string', $details);
        $this->assertNotEmpty($details);
        $this->assertNotInternalType('array', $details);
    }
    
    public function testGetGroupStudentsUploadPdfDetails(){
        $details = $this->pdfDetailsService->getGroupStudentsUploadPdfDetails();
        $this->assertInternalType('string', $details);
        $this->assertNotEmpty($details);
        $this->assertNotInternalType('array', $details);
    }
}