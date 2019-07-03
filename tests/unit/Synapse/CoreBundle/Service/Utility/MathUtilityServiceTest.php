<?php
namespace tests\unit\Synapse\CoreBundle\Service\Utility;

use Codeception\Specify;
use Codeception\Test\Unit;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\MathUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\TotalStudentsListDto;


class MathUtilityServiceTest extends Unit
{
    use Specify;

    public function testGetDivisionResult()
    {
        $this->specify("test division", function ($expectedResult, $numerator = null, $denominator = null) {
            $mathService = new MathUtilityService();
            $result = $mathService->divide($numerator, $denominator);
            verify($result)->equals($expectedResult);

        }, [
            'examples' => [
                // Successful division, whole number
                [
                    1,
                    2,
                    2
                ],
                // Successful division, decimal
                [
                    4.66666666666666696272613990004174411296844482421875,
                    14,
                    3
                ],
                // Division by 0
                [
                    0,
                    0,
                    0
                ],
                // Null numerator and denominator
                [
                    0
                ],
                //Invalid denominator, null numerator
                [
                    0,
                    null,
                    'IM A STRING'
                ],
                //Invalid numerator, null denominator
                [
                    0,
                    'IM A GIRAFFE'
                ],
                // Numbers as strings
                [
                    1,
                    "1",
                    "1"
                ],
                // Division by 0 as strings
                [
                    0,
                    "0",
                    "0"
                ],
                // Extreme float edge case
                [
                    1,
                    0.000000000000001,
                    0.000000000000001
                ],
                // Large Integer test case
                [
                    0.9999999999999997779553950749686919152736663818359375,
                    20000000000000002,
                    20000000000000004
                ]
            ]
        ]);
    }

    public function testGetRoundedDivisionResult()
    {
        $this->specify("test get rounded division result", function ($expectedResult, $numerator = null, $denominator = null, $precision = null, $rounding = null) {
            $mathService = new MathUtilityService();
            $result = $mathService->divideAndRound($numerator, $denominator, $precision, $rounding);
            verify($result)->equals($expectedResult);
        }, [
            'examples' => [
                // Successful division, whole number, no rounding, no precision
                [
                    1,
                    2,
                    2
                ],
                // Successful division, decimal, no rounding, no precision
                [
                    5,
                    14,
                    3
                ],
                // Null numerator and denominator, no rounding, no precision
                [
                    0
                ],
                //Invalid denominator, null numerator, no rounding, no precision
                [
                    0,
                    null,
                    'IM A STRING'
                ],
                //Invalid numerator, null denominator, no rounding, no precision
                [
                    0,
                    'IM A GIRAFFE'
                ],
                // Successful division, decimal, invalid rounding, invalid precision
                [
                    4.66666666666666696272613990004174411296844482421875,
                    14,
                    3,
                    'IM A GIRAFFE',
                    'IM A NON A NUMBER'
                ],
                // Successful division, decimal, valid rounding, invalid precision
                [
                    4.66666666666666696272613990004174411296844482421875,
                    14,
                    3,
                    7,
                    'IM A NON A NUMBER'
                ],
                // Successful division, decimal, no rounding, valid precision
                [
                    4.66666670000000038953658076934516429901123046875,
                    14,
                    3,
                    7,
                    PHP_ROUND_HALF_UP
                ],
                // Successful division, whole number, rounding, precision
                [
                    1,
                    2,
                    2,
                    7,
                    PHP_ROUND_HALF_UP
                ],
                // Division by 0
                [
                    0,
                    0,
                    0
                ],
                // Successful division, 3 precision
                [
                    .073864,
                    13,
                    176,
                    6,
                    PHP_ROUND_HALF_UP
                ]
            ]
        ]);
    }


    public function testGetDivisionResultAsPercentage()
    {
        $this->specify("", function($expectedResult, $numerator = null, $denominator = null, $precision = null, $rounding = null){
            $mathService = new MathUtilityService();
            $result = $mathService->getPercentage($numerator, $denominator, $precision, $rounding);
            verify($result)->equals($expectedResult);
        }, [
            'examples' => [
                // Successful division, whole number, no rounding, no precision
                [
                    100,
                    2,
                    2
                ],
                // Successful division, decimal, no rounding, no precision
                [
                    500,
                    14,
                    3
                ],
                // Null numerator and denominator, no rounding, no precision
                [
                    0
                ],
                //Invalid denominator, null numerator, no rounding, no precision
                [
                    0,
                    null,
                    'IM A STRING'
                ],
                //Invalid numerator, null denominator, no rounding, no precision
                [
                    0,
                    'IM A GIRAFFE'
                ],
                // Successful division, decimal, invalid rounding, invalid precision
                [
                    466.666666666666696272613990004174411296844482421875,
                    14,
                    3,
                    'IM A GIRAFFE',
                    'IM A NON A NUMBER'
                ],
                // Successful division, decimal, valid rounding, invalid precision
                [
                    466.666666666666696272613990004174411296844482421875,
                    14,
                    3,
                    7,
                    'IM A NON A NUMBER'
                ],
                // Successful division, decimal, no rounding, valid precision
                [
                    466.666670000000038953658076934516429901123046875,
                    14,
                    3,
                    7,
                    PHP_ROUND_HALF_UP
                ],
                // Successful division, whole number, rounding, precision
                [
                    100,
                    2,
                    2,
                    7,
                    PHP_ROUND_HALF_UP
                ],
                // Division by 0
                [
                    0,
                    0,
                    0
                ],
                // Successful division, 3 precision
                [
                    7.3864,
                    13,
                    176,
                    6,
                    PHP_ROUND_HALF_UP
                ]
            ]
        ]);
    }

    public function testDivideCeiling ()
    {
        $this->specify("", function ($expectedResult, $numerator = null, $denominator = null){
            $mathService = new MathUtilityService();
            $result = $mathService->divideAndCeiling($numerator, $denominator);
            verify($result)->equals($expectedResult);
        }, [
            "examples" => [
                // Successful division, whole number, no rounding, no precision
                [
                    1,
                    2,
                    2
                ],
                // Successful division, decimal, no rounding, no precision
                [
                    5,
                    14,
                    3
                ],
                // Null numerator and denominator, no rounding, no precision
                [
                    0
                ],
                //Invalid denominator, null numerator, no rounding, no precision
                [
                    0,
                    null,
                    'IM A STRING'
                ],
                //Invalid numerator, null denominator, no rounding, no precision
                [
                    0,
                    'IM A GIRAFFE'
                ],
                // Division by 0
                [
                    0,
                    0,
                    0
                ],
                // Successful division, 3 precision
                [
                    1,
                    13,
                    176
                ]
            ]
        ]);
    }

}