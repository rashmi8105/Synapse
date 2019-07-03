<?php
namespace Synapse\RiskBundle\Controller;

use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;
use Symfony\Component\Validator;

class RiskCalculationControllerTest extends \PHPUnit_Framework_TestCase
{
    //        \Codeception\Util\Debug::debug($inputRiskCalculationDto->getId());
    use \Codeception\Specify;


    public function test_RiskCalculationController_CreateRiskCalculationInputAction()
    {
        // Build a constraint violation list to send to the controller to ensure it sends back the right response code.
        $constraintViolationListToTestWithBuilder = new Validator\ConstraintViolationList();
        $constraintViolationToAddToList = new Validator\ConstraintViolation("It's broken", "", [], null, "", "");
        $constraintViolationListToTestWithBuilder->add($constraintViolationToAddToList);

        // Syntax of "Specify" tests is a bit unique. Notice the second "function" parameter, 
        // those parameter names need to be new names and scoped inside of the function only, 
        // to avoid parameter name confusion and variable scope confusion
        $this->specify("Verify that the risk calculation controller handles constraint violations correctly.", function ($inputRiskCalculationDto, $constraintViolationListToTestWith) {
            $this->assertEquals(1, $inputRiskCalculationDto->getId());

            // Create a mock risk calculation service
            $riskCalculationService = $this->getMock('RiskCalculationService', array('createRiskCalculationInput'));
            $riskCalculationController = new RiskCalculationController();

            $reflection = new \ReflectionClass(get_class($riskCalculationController));
            $property = $reflection->getProperty('riskCalculationService');
            $property->setAccessible(true);
            $property->setValue($riskCalculationController, $riskCalculationService);

            //Run the method that we're testing and verify its outputs with assertions
            $responseFromCreateRiskCalculationInputAction = $riskCalculationController->createRiskCalculationInputAction($inputRiskCalculationDto, $constraintViolationListToTestWith);

            $this->assertObjectHasAttribute('response', $responseFromCreateRiskCalculationInputAction);

            // Pull some data out of the response object to verify it more directly
            $responseFromResponseFromCreateRiskCalculationInputAction = $responseFromCreateRiskCalculationInputAction->getResponse();

            // Make sure we get the right response code
            $this->assertEquals(400, $responseFromResponseFromCreateRiskCalculationInputAction->getStatusCode());


        }, ['examples' => [[$this->inputRiskCalculationDto(), $constraintViolationListToTestWithBuilder]]]);

        // If the one above fails this will still run and decide if its successful also
        $this->specify("Verify that the risk calculation controller accurately directs output depending on what it takes in.", function ($inputRiskCalculationDto, $constraintViolationListToTestWith2) {

            // Build a mock object that has a placeholder for createRiskCalculationInput
            $riskCalculationService = $this->getMock('RiskCalculationService', array('createRiskCalculationInput'));

            // Make sure we call 'createRiskCalculationInput' exactly one time, and return the exact copy
            // of the parameter passed in
            $riskCalculationService
                ->expects($this->once())
                ->method('createRiskCalculationInput')
                ->with($this->equalTo($inputRiskCalculationDto))
                ->will($this->returnArgument(0));;


            $riskCalculationController = new RiskCalculationController();

            // Put the mock service onto the controller
            $reflection = new \ReflectionClass(get_class($riskCalculationController));
            $property = $reflection->getProperty('riskCalculationService');
            $property->setAccessible(true);
            $property->setValue($riskCalculationController, $riskCalculationService);

            // Run the method we're testing
            $responseFromCreateRiskCalculationInputAction = $riskCalculationController->createRiskCalculationInputAction($inputRiskCalculationDto, $constraintViolationListToTestWith2);

            // Inspect the response for the expected elements
            $this->assertObjectHasAttribute('data', $responseFromCreateRiskCalculationInputAction);
            $this->assertObjectHasAttribute('errors', $responseFromCreateRiskCalculationInputAction);
            $this->assertObjectHasAttribute('sideLoaded', $responseFromCreateRiskCalculationInputAction);

            $dataFromResponseFromCreateRiskCalculationInputAction = $responseFromCreateRiskCalculationInputAction->getData();

            $errorsFromResponseFromCreateRiskCalculationInputAction = $responseFromCreateRiskCalculationInputAction->getErrors();

            // Make sure errors is empty
            $this->assertEquals(0, count($errorsFromResponseFromCreateRiskCalculationInputAction));

            // Inspect the data object for verifying it contains the correct values.
            $this->assertObjectHasAttribute("id", $dataFromResponseFromCreateRiskCalculationInputAction);
            $this->assertEquals($inputRiskCalculationDto->getId(), $dataFromResponseFromCreateRiskCalculationInputAction->getId());
            $this->assertEquals($inputRiskCalculationDto->getIsRiskvalCalcRequired(), $dataFromResponseFromCreateRiskCalculationInputAction->getIsRiskvalCalcRequired());
            $this->assertEquals($inputRiskCalculationDto->getOrganizationId(), $dataFromResponseFromCreateRiskCalculationInputAction->getOrganizationId());
            $this->assertEquals($inputRiskCalculationDto->getPersonId(), $dataFromResponseFromCreateRiskCalculationInputAction->getPersonId());
        }, ['examples' => [[$this->inputRiskCalculationDto(), new Validator\ConstraintViolationList()]]]);


    }

    /**
     * @return RiskCalculationInputDto dummy object to pass some data into the controller method and verify
     * it works correctly.
     */
    private function inputRiskCalculationDto()
    {
        $riskCalculationInputDto = new RiskCalculationInputDto();
        $riskCalculationInputDto->setId("1");
        $riskCalculationInputDto->setIsRiskvalCalcRequired("y");
        $riskCalculationInputDto->setOrganizationId("1");
        $riskCalculationInputDto->setPersonId("1");
        return $riskCalculationInputDto;
    }

}