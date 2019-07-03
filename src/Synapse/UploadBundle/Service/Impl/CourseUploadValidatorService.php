<?php

namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadValidatorServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\AcademicBundle\Entity\OrgCourses;

/**
* Handle faculty upload validation
*
* @DI\Service("course_upload_validator_service")
*/
class CourseUploadValidatorService extends AbstractService implements UploadValidatorServiceInterface
{

    const SERVICE_KEY = 'course_upload_validator_service';

    private $courseService;
    private $errors;
    private $validator;
    private $isUpdate;
    /**
     * @param $repositoryResolver
     * @param $logger
     *
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "courseService" = @DI\Inject("course_service"),
     *      "validator" = @DI\Inject("validator")
     * })
     */
    public function __construct($repositoryResolver, $logger, $courseService, $validator)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->courseService = $courseService;
        $this->errors = [];
        $this->validator = $validator;
    }

    public function validate($name, $data, $orgId, $isUpdate = false)
    {

        $this->isUpdate = $isUpdate;

        $courseItems = [
            'YearId',
            'TermId',
            'UniqueCourseSectionId',
            'CollegeCode',
            'DeptCode',
            'SubjectCode',
            'CourseNumber',
            'CourseName',
            'SectionNumber',
            'Location',
            'CreditHours'
        ];


        $status = $this->validateCourseItem($name, $data);

        return $status;

    }

    private function validateCourseItem($name, $data)
    {
        $testCourse = new OrgCourses;
        call_user_func([$testCourse, 'set' . $name], $data);
        $validationErrors = $this->validator->validate($testCourse);
        foreach ($validationErrors as $error) {
            if (
                $error->getPropertyPath() == lcfirst($name) &&
                !$this->isUpdate &&
                $error->getMessage() != 'This value is already used.'
            ) {
                $this->errors[] = $error->getMessage();
            }
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    public function getErrors()
    {
        $errors = $this->errors;
        $this->errors = [];

        return $errors;
    }

    /*
     * @codeCoverageIgnore
    */
    private function recursiveInArray($array, $value, $key)
    {
        //loop through the array
        foreach ($array as $val) {
            //if $val is an array cal myInArray again with $val as array input
            if (is_array($val)) {
                if ($this->recursiveInArray($val, $value, $key)) {
                    return $val;
                }
            } else {
                if ($array[$key]==$value) {
                    return true;
                }
            }
        }
        return false;
    }
}