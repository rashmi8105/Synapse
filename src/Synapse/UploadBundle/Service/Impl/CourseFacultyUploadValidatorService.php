<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Handle course upload validation
 *
 * @DI\Service("course_faculty_upload_validator_service")
 */
class CourseFacultyUploadValidatorService extends AbstractService
{

    const SERVICE_KEY = 'course_faculty_upload_validator_service';

    /**
     * @var array
     */
    private $errors;

    /**
     * @var array
     */
    private $requiredItems = [
        'uniquecoursesectionid',
        'facultyid'
    ];

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
    }

    /**
     * Validates the data and column headers of CSV
     * @param string $name
     * @return bool
     */
    public function validate($name)
    {
        if(!in_array(strtolower($name), $this->requiredItems)){
            $this->errors[] = 'is not a valid column';
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
}
