<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class FacultyPermissionSetDto
{

    /**
     * templateId
     *
     * @var integer @JMS\Type("integer")
     */
    private $templateId;

    /**
     * templateName
     *
     * @var string @JMS\Type("string")
     */
    private $templateName;

    /**
     *
     * @param int $templateId            
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     *
     * @return int
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     *
     * @return int
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     *
     * @param string $subjectCourse            
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;
    }
}