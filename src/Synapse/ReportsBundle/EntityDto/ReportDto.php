<?php
namespace Synapse\ReportsBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class ReportDto
{

    /**
     * reportId
     *
     * @var integer @JMS\Type("integer")
     */
    private $reportId;
	
	/**
     * reportInstanceId
     *
     * @var string @JMS\Type("string")
     */
    private $reportInstanceId;
	
	/**
     * generatedOn
     *
     * @var \DateTime @JMS\Type("DateTime<'Y-m-d H:i:s'>")
     */
    private $generatedOn;
	
    /**
     *
     * @var array @JMS\Type("array<Synapse\ReportsBundle\EntityDto\StudentReportDto>")
     *     
     */
    private $studentReport;
	
	/**
     *
     * @param int $reportId            
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     *
     * @return int
     */
    public function getReportId()
    {
        return $this->reportId;
    }
	
	/**
     *
     * @param int $reportInstanceId            
     */
    public function setReportInstanceId($reportInstanceId)
    {
        $this->reportInstanceId = $reportInstanceId;
    }

    /**
     *
     * @return int
     */
    public function getReportInstanceId()
    {
        return $this->reportInstanceId;
    }
	
	/**
     *
     * @param mixed $generatedOn            
     */
    public function setGeneratedOn($generatedOn)
    {
        $this->generatedOn = $generatedOn;
    }

    /**
     *
     * @return mixed
     */
    public function getGeneratedOn()
    {
        return $this->generatedOn;
    }

    /**
     *
     * @param array $studentReport
     */
    public function setStudentReport($studentReport)
    {
        $this->studentReport = $studentReport;
    }

    /**
     *
     * @return array
     */
    public function getStudentReport()
    {
        return $this->studentReport;
    }
}