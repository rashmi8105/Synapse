<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\RestBundle\Exception\ValidationException;


class RiskVariableValidatorServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

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
    private $mockProfileService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOrgProfileService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockValidator;


    public function testCheckOverlap()
    {
        $this->specify("Checking that the check overlap function is checking risk variable buckets correctly", function ($throwsException, $minBucket1, $minBucket2, $maxBucket1, $maxBucket2, $expectedResult) {
            $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $this->mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $this->mockProfileService = $this->getMock('profileService', array('get'));
            $this->mockValidator = $this->getMock('validator', array());
            $this->mockOrgProfileService = $this->getMock('orgProfileService', array());
            $mockOrganizationRepository = $this->getMock('organizationRepository', array());
            $mockOrgGroupRepository = $this->getMock('orgGroupRepository', array());


            $this->mockRepositoryResolver->method('getRepository')->willReturnMap([['SynapseCoreBundle:Organization' => $mockOrganizationRepository],
                ['SynapseCoreBundle:OrgGroup' => $mockOrgGroupRepository]]);


            $riskVariableValidator = new RiskVariableValidatorService($this->mockRepositoryResolver, $this->mockLogger, $this->mockProfileService, $this->mockOrgProfileService, $this->mockValidator);
     

            try{
                $isRiskVariableValid = $riskVariableValidator->checkOverlap($minBucket1, $minBucket2, $maxBucket1, $maxBucket2);
                $this->assertEquals($isRiskVariableValid, $expectedResult);

            }
            catch(ValidationException $e){
                if (!$throwsException) {
                    $this->fail("Throws Unexpected Validation Exception");
                }
                return;
            }


        }, [
            'examples' => [
                [false, 1, 3, 2, 4, true],
                [true, 1, 2, 2.1, 4, null],
                [false, 1, 2, 2, 4, true],
                #[true, 1, 3, 5, 4, null], #Case where one bucket is inside another ESPRJ-11321
                [false, 3, 1, 4, 2, true],
                [true, 3, 1, 4, 5, null],
                [true, 1, 2, 3, 4, null],
                [true, 2, 1, 4, 5, null],
                [false, 2, 1, 4, 2, true]
            ]
        ]);
    }
}