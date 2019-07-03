<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class OurStudentsReportDto
{
   
    /**
     * top5issue
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\OurStudentsTop5issueDto>")
     *
     */
    private $top5issue;
    
    /**
     * demographics
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\OurStudentsDemographicItemsDto>")
     */
    private $demographics;
    
    /**
     * sections
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\OurStudentsReportSectionsDto>")
     */
    private $sections;
    
    /**
     *
     * @param array $top5issue
     */
    public function setTop5issue($top5issue)
    {
    	$this->top5issue = $top5issue;
    }
    
    /**
     *
     * @return array
     */
    public function getTop5issue()
    {
    	return $this->top5issue;
    }
    
    /**
     *
     * @param array $demographics
     */
    public function setDemographics($demographics)
    {
    	$this->demographics = $demographics;
    }
    
    /**
     *
     * @return array
     */
    public function getDemographics()
    {
    	return $this->demographics;
    }
    
    /**
     *
     * @param array $sections
     */
    public function setSections($sections)
    {
    	$this->sections = $sections;
    }
    
    /**
     *
     * @return array
     */
    public function getSections()
    {
    	return $this->sections;
    }
}