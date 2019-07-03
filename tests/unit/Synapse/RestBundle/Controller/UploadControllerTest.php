<?php
namespace Synapse\RestBundle\Controller;

use Symfony\Component\Validator;

class UploadControllerTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function test_UploadController_ListHistoryAction()
    {


        /**
         * Syntax of "Specify" tests is a bit unique. Notice the second "function" parameter - this is an anonymous
         * function.  The parameters passed into the anonymous function need to be new names and scoped inside of the
         * function only, to avoid parameter name confusion and variable scope confusion
         */
        $this->markTestSkipped("Unit tests should not be on Api's");
        $this->specify("Verify the Upload Controller", function ($orgId, $userId) {

            // Scaffolding for paramFetcher is using mockBuilder to allow more direct correlation to the paramFetcher
            // utility that Symfony uses
            $paramFetcher = $this->getMockBuilder('FOS\RestBundle\Request\ParamFetcher')
                ->disableOriginalConstructor()
                ->setMethods(['get'])
                ->getMock();
            $paramFetcher->method('get')->willReturnMap(
                [
                    ['page_no', 1],
                    ['offset',25],
                    ['sortBy',''],
                    ['filter','{"type":""}'],
                    ['output-format', 'csv']
                ]
            );


            // Scaffolding setup for mock organization
            $mockOrg = $this->getMock('Organization', ['getId']);
            $mockOrg->expects($this->once())->method('getId')->willReturn($orgId);

            // Scaffolding setup for mock user
            $mockUser = $this->getMock('Person', ['getId', 'getOrganization']);
            $mockUser->expects($this->once())->method('getOrganization')->willReturn($mockOrg);

            // Scaffolding setup for mock token
            $mockToken = $this->getMock('AccessToken', ['getUser']);
            $mockToken->expects($this->any())->method('getUser')->willReturn($mockUser);


            // Scaffolding setup for mock security context
            $securityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
                ->disableOriginalConstructor()
                ->setMethods([
                    'getToken',
                    'isAuthenticated',
                    'getUser'
                ])
                ->getMock();
            $securityContext->expects($this->any())->method('getToken')->willReturn($mockToken);


            // Scaffolding setup for mock upload file log service. We don't care what listHistory does, only that it
            // is callable from within the controller when accessing the service.
            $uploadFileLogService = $this->getMock('UploadFileLogService', ['listHistory']);

            // Scaffolding setup for the symfony container used in the controller
            $container = $this->getMock('container', ['get']);
            // Allow for further injection of other container objects if necessary by specifying only what
            // security.context needs as a mock.
            $container->method('get')->willReturnMap(
                [
                    ['security.context', $securityContext]
                ]
            );

            // This is the class we're testing a method in
            $uploadController = new UploadController();

            // We use reflection to set properties because they're private and have no access methods specified.
            $reflection = new \ReflectionClass(get_class($uploadController));

            // The reason we're using reflection to set this property instead of the accessor is because
            // the accessor itself expects a certain object type for Container, which the mock won't represent
            // correctly
            $property = $reflection->getProperty('container');
            $property->setAccessible(true);
            $property->setValue($uploadController, $container);


            // Set the service to something we mocked away because it's work isn't relevant to this test
            $property = $reflection->getProperty('uploadFileLogService');
            $property->setAccessible(true);
            $property->setValue($uploadController, $uploadFileLogService);

            // Run the method that we're testing and verify its outputs with assertions
            // TODO: We need to figure out if it's possible to make paramFetcher a mock without causing warnings in the development tool.
            $responseFromListHistoryAction = $uploadController->listHistoryAction($orgId, $paramFetcher);

            // These assertions check for data, errors, and sideloaded to be part of the return object coming in
            $this->assertObjectHasAttribute('data', $responseFromListHistoryAction);
            $this->assertObjectHasAttribute('errors', $responseFromListHistoryAction);
            $this->assertObjectHasAttribute('sideLoaded', $responseFromListHistoryAction);

        }, [
            'examples' => [
                [
                    1, 1
                ]
            ]
        ]);
    }

}