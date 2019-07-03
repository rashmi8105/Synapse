<?php
namespace Synapse\ReportsBundle\Service\Impl;


use JMS\DiExtraBundle\Annotation as DI;
use Synapse\ReportsBundle\EntityDto\ReportRunningStatusDto;

class DtoVerificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;


    /*
     * CREATE MOCK OBJECT ReportRunningDto
     * Mock Repository Resolver with getRepository
     * Mock container with get
     *
     */

    // tests
    public function testVerifierWithValidOrg()
    {

            $reportOrg = 3;
            $authorizedOrg = 3;

            $dtoVerifier = new ReportsDtoVerificationService();
            $reportDto = $this->createReportRunningDto(1, 12, 'GPA', 1, $reportOrg, array());



            $dtoVerifier->verifyOrganizationInDto($authorizedOrg, $reportDto);


    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     * @expectedExceptionMessage Bad Request: Requested Organization id does not match authorized organization id
     */
    public function testVerifierWithInvalidOrg()
    {
        $reportOrg = 5;
        $authorizedOrg = 6;


        $dtoVerifier = new ReportsDtoVerificationService();
        $reportDto = $this->createReportRunningDto(1, 12, 'GPA', 1, $reportOrg, array());

        $dtoVerifier->verifyOrganizationInDto($authorizedOrg, $reportDto);

    }

    public function testVerifierWithValidPerson()
    {
        $reportPerson = 1;
        $authorizedPerson = 1;



        $dtoVerifier = new ReportsDtoVerificationService();
        $reportDto = $this->createReportRunningDto(1, 12, 'GPA', $reportPerson, 1, array());

        $dtoVerifier->verifyPersonInDto($authorizedPerson, $reportDto);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     * @expectedExceptionMessage Bad Request: Requested Person id does not match authorized person id
     */
    public function testVerifierWithInvalidPerson()
    {
        $reportPerson = 1;
        $authorizedPerson = 2;



        $dtoVerifier = new ReportsDtoVerificationService();
        $reportDto = $this->createReportRunningDto(1, 12, 'GPA', $reportPerson, 1, array());

        $dtoVerifier->verifyPersonInDto($authorizedPerson, $reportDto);

    }

    private function createReportRunningDto($id, $reportId, $shortCode, $personId, $organizationId, $searchAttributes){
        $reportRunningStatusDto = new ReportRunningStatusDto();
        $reportRunningStatusDto->setId($id);
        $reportRunningStatusDto->setReportId($reportId);
        $reportRunningStatusDto->setShortCode($shortCode);
        $reportRunningStatusDto->setStatus('IP');
        $reportRunningStatusDto->setPersonId($personId);
        $reportRunningStatusDto->setOrganizationId($organizationId);

        $reportRunningStatusDto->setSearchAttributes($searchAttributes);//Need Filter Criteria
        return $reportRunningStatusDto;

    }



}