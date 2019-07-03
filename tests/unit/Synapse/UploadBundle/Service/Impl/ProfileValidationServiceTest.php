<?php
namespace Synapse\UploadBundle\Service\Impl;

class ProfileValidationServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testListAcademicTerm()
    {
        $this->specify("List Students Surveys Data for Student", function ($profileItem, $profileValue)
        {
            $profileValidationService = new ProfileValidationService();
            $vaLidationMessage = $profileValidationService->profileItemCustomValidations($profileItem, $profileValue);
            if (strlen($profileValue) > 255) {
                $this->assertEquals($vaLidationMessage, "should not be more than  255 characters");
            } else {
                $this->assertEquals($vaLidationMessage, "");
            }
        }, [
            'examples' => [
                [
                    "campusstate",
                    "Text with more than 255  Text with more than 255  Text with more than 255  Text with more than 255  Text with more than 255 Text with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 chars"
                ],
                [
                    "campusstate",
                    "Text less  than 255 characters"
                ],
                [
                    "campuszip",
                    "Text with more than 255  Text with more than 255  Text with more than 255  Text with more than 255  Text with more than 255 Text with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 chars"
                ],
                [
                    "campuszip",
                    "Text less  than 255 characters"
                ],
                [
                    "campuscountry",
                    "Text with more than 255  Text with more than 255  Text with more than 255  Text with more than 255  Text with more than 255 Text with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 chars"
                ],
                [
                    "campuscountry",
                    "Text less  than 255 characters"
                ],
                [
                    "campuscity",
                    "Text with more than 255  Text with more than 255  Text with more than 255  Text with more than 255  Text with more than 255 Text with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 charsText with more than 255 chars"
                ],
                [
                    "campuscity",
                    "Text less  than 255 characters"
                ]
            ]
        ]);
    }
}