<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Validator;
use Synapse\SearchBundle\Service\Impl\SearchService;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class SearchControllerTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $organization = 1;

    public function test_SearchController_createCustomSearchAction()
    {
        $constraintViolationListToTestWithBuilder = new Validator\ConstraintViolationList();
        $constraintViolationToAddToList = new Validator\ConstraintViolation("It's broken", "", [], null, "", "");
        $constraintViolationListToTestWithBuilder->add($constraintViolationToAddToList);
        
        // Syntax of "Specify" tests is a bit unique. Notice the second "function" parameter,
        // those parameter names need to be new names and scoped inside of the function only,
        // to avoid parameter name confusion and variable scope confusion
        $this->specify("Verify the Search Controller", function ($createCustomSearchDto, $constraintViolationListToTestWithBuilder)
        {
            
            /*
             * ParamFetcher mock added
             */
            
            $paramFetcher = $this->getMockBuilder('FOS\RestBundle\Request\ParamFetcher')
                ->disableOriginalConstructor()
                ->getMock(array(
                'get'
            ));
            
            /*
             * This would return survey Id for the query parma survey Id
             */
            
            $paramFetcher->expects($this->any())
                ->method('get')
                ->will($this->returnCallback(function ($arg)
            {
                $argArr = array(
                    'output-format' => 'csv'
                );
                return $argArr[$arg];
            }));
            
            /*
             * Organization Mock Object
             */
            
            $mockOrg = $this->getMock('Organization', array(
                'getId'
            ));
            
            /*
             * Mock user
             */
            
            $mockUser = $this->getMock('Person', array(
                'getUser'
            ));
            /*
             * Mock Access Token
             */
            
            $mockToken = $this->getMock('AccessToken', array(
                'getUser'
            ));
            
            /*
             * Below lines mock the org user and token objects to return respective mocks.
             */
            
            $securityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
                ->disableOriginalConstructor()
                ->setMethods([
                'getToken',
                'isAuthenticated',
                'getUser'
            ])
                ->getMock();
            
            $securityContext->expects($this->any())
                ->method('getToken')
                ->will($this->returnValue($mockToken));
            
            /*
             * End for mocking the security context
             */
            
            
            
            /*
             * Mock of Search Service a that is injected  into the controller
             */
            
            $searchService = $this->getMock('SearchService', array(
                'createCustomSearch'
            ));
            
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));
            $searchController = new SearchController();
            
            $container = $this->getMock('container', array(
                'get'
            ));
            
            $container->expects($this->any())
                ->method('get')
                ->will($this->returnValue($securityContext));
            
            $reflection = new \ReflectionClass(get_class($searchController));
            
            /*
             * Container mock added
             */
            $property = $reflection->getProperty('container');
            $property->setAccessible(true);
            $property->setValue($searchController, $container);
            
            /*
             * Search Service mock added
             */
            
            $property = $reflection->getProperty('searchService');
            $property->setAccessible(true);
            $property->setValue($searchController, $searchService);
            
            // Run the method that we're testing and verify its outputs with assertions
            $responseFromCustomSearchAction = $searchController->createCustomSearchAction($this->createCustomSearchDto(), $constraintViolationListToTestWithBuilder, $paramFetcher);
            $this->assertObjectHasAttribute('data', $responseFromCustomSearchAction);
            $this->assertInternalType("object", $responseFromCustomSearchAction);
            $this->assertNotEmpty($responseFromCustomSearchAction);
        }, [
            'examples' => [
                [
                    $this->organization,
                    $constraintViolationListToTestWithBuilder
                ]
            ]
        ]);
    }

    private function createCustomSearchDto()
    {
        $searchAttribute = [];
        $customSearchDto = new SaveSearchDto();
        $customSearchDto->setOrganizationId($this->organization);
        $searchAttribute["risk_indicator_ids"] = "1,2,3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "'o','c'";
        $searchAttribute["contact_types"] = "1,2";
        $customSearchDto->setSearchAttributes($searchAttribute);
        return $customSearchDto;
    }
}