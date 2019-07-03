<?php
namespace Synapse\RiskBundle\Validator\Constraints;

use Synapse\RestBundle\Exception\ValidationException;

class RiskIndicatorValidatorTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    public function testCheckOverlap()
    {

        $this->specify("Checking that the check overlap function is checking risk variable buckets correctly", function ($throwsException, $minBucket1, $minBucket2, $maxBucket1, $maxBucket2, $expectedResult) {

            $RiskIndicatorValidator = new RiskIndicatorValidator();

            try{
                $isRiskVariableValid = $RiskIndicatorValidator->checkOverlap($minBucket1, $minBucket2, $maxBucket1, $maxBucket2);
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