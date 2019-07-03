<?php

namespace Synapse\UploadBundle\Service\Impl;

use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\UploadBundle\Repository\UploadColumnHeaderDownloadMapRepository;
use Synapse\UploadBundle\Repository\UploadRepository;


class UploadServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    /**
     * @var UploadService
     */
    private $uploadService;

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


    public function testCheckDatafilePermissionReturnsErrorString()
    {
        $this->specify("List Students Surveys Data for Student", function ($type, $orgStaticListRepositoryReturnValues,
                                                                           $academicYearServiceReturnValues, $expectedResult) {
            $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
            $this->mockContainer = $this->getMock('Container', ['get']);

            $mockOrgStaticListRepository = $this->getMock('orgStaticListRepository', ['FindOneBy']);
            $mockOrgStaticListRepository->method('FindOneBy')->willReturn($orgStaticListRepositoryReturnValues);

            $mockOrgAcademicYearService = $this->getMock('orgAcademicYearService', array('getCurrentOrgAcademicYearId'));
            $mockOrgAcademicYearService->method('getCurrentOrgAcademicYearId')->willReturn($academicYearServiceReturnValues);
            $this->mockContainer->method('get')->willReturnMap([
                [AcademicYearService::SERVICE_KEY, $mockOrgAcademicYearService]
            ]);

            $this->mockRepositoryResolver->method('getRepository')->willReturnMap(
                [[OrgStaticListRepository::REPOSITORY_KEY, $mockOrgStaticListRepository]]);

            $this->uploadService = new UploadService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);

            // person, UploadTypeId, and organization id variables are only being used against mocked away functions
            $person = new Person();
            $organizationId = 2;
            $uploadTypeId = 867;
            $validationMessage = $this->uploadService->checkDatafilePermissionReturnsErrorString($person, $type, $organizationId, $uploadTypeId);
            $this->assertEquals($validationMessage, $expectedResult);
        }, ['examples' =>
            [
                [ // Testing to see if the static list type will display an error when they don't have access to the static list
                    // and they don't have an active academic year
                    'staticlist', //type
                    null, // What orgStaticListRepository->FindOneBy returns
                    [], // What academicYearService->getCurrentOrgAcademicYearId returns
                    'There is no currently active academic year; You do not have access to this Static List; ' // what checkDatafilePermissionReturnsErrorString return
                ],
                [ // Testing to see if error when they have access to the static list and they have a current academic year
                    'staticlist', //type
                    [1], // What orgStaticListRepository->FindOneBy returns
                    1234, // What academicYearService->getCurrentOrgAcademicYearId returns
                    null // what checkDatafilePermissionReturnsErrorString return
                ],
                [ // testing to see what occurs when they have a current academic year but not access to the static list
                    'staticlist', //type
                    null, // What orgStaticListRepository->FindOneBy returns
                    1234, // What academicYearService->getCurrentOrgAcademicYearId returns
                    'You do not have access to this Static List; ' // what checkDatafilePermissionReturnsErrorString return
                ],
                [ // Testing to see what occurs when they have access to the static list but don't have a current academic year
                    'staticlist', //type
                    [1], // What orgStaticListRepository->FindOneBy returns
                    null, // What academicYearService->getCurrentOrgAcademicYearId returns
                    'There is no currently active academic year; ' // what checkDatafilePermissionReturnsErrorString return

                ],
                [ // Testing to see what occurs when they try to run a student data dump
                    'student', //type
                    [1],  // What orgStaticListRepository->FindOneBy returns
                    1234, // What academicYearService->getCurrentOrgAcademicYearId returns
                    null  // what checkDatafilePermissionReturnsErrorString return

                ]
            ]
        ]);

    }


    public function testDownloadFacultyUploadTemplate()
    {

        $this->specify("List Students Surveys Data for Student", function ($template, $downloadFileName, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockCSVUtilityService = $this->getMock('CSVUtilityService', ['createCSVFileInTempFolder', 'writeToFile']);
            $mockUploadColumnHeaderMapRepository = $this->getMock('UploadColumnHeaderDownloadMapRepository', ['getUploadHeaders']);
            $mockUploadColumnHeaderMapRepository->method('getUploadHeaders')->willReturn(['ExternalId', 'FirstName']);

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [
                    UploadColumnHeaderDownloadMapRepository::REPOSITORY_KEY,
                    $mockUploadColumnHeaderMapRepository
                ]
            ]);
            $mockContainer->method('get')->willReturnMap([
                [
                    CSVUtilityService::SERVICE_KEY,
                    $mockCSVUtilityService
                ]
            ]);
            $uploadService = new UploadService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $downloadFile = $uploadService->downloadUploadTemplate($template, $downloadFileName);
                $this->assertEquals($downloadFile, $expectedResult);
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->assertEquals($errorMessage, $expectedResult);
            }
        }, ['examples' =>
            [
                // All valid Data
                [
                    'faculty', 'faculty_staff-upload-template.csv', '/tmp/faculty_staff-upload-template.csv'
                ],
                // When template name is empty, exception is thrown
                [
                    null, 'faculty_staff-upload-template.csv', 'Invalid template name.'
                ],
                // When download  file name is empty, exception is thrown
                [
                    'faculty', null, 'Invalid download file name'
                ]
            ]
        ]);
    }

}
