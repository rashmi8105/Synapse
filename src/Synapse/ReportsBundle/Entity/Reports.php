<?php

namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
/**
 * reports
 *
 * @ORM\Table(name="reports")
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Reports extends BaseEntity
{

    /**
     *
     * @var int @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="name", type="string", length=100, nullable=false)
     * @JMS\Expose
     */
    private $name;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=255, nullable=false)
     * @JMS\Expose
     */
    private $description;

    /**
     *
     * @var string @ORM\Column(name="is_batch_job", type="string", columnDefinition="enum('y','n')")
     * @JMS\Expose
     */
    private $isBatchJob;

    /**
     *
     * @var boolean @ORM\Column(name="is_coordinator_report", type="string", columnDefinition="enum('y','n')")
     *
     */
    private $isCoordinatorReport;


    /**
     *
     * @var boolean @ORM\Column(name="short_code", type="string", length=10, nullable=false)
     *
     */
    private $shortCode;

    /**
     *
     * @var boolean @ORM\Column(name="is_active", type="string", columnDefinition="enum('y','n')")
     *
     */
    private $isActive;

    /**
     *
     * @var int @ORM\Column(name="report_view_id" ,type="integer")
     */
    private $reportViewId;

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return reports
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return reports
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isBatchJob
     *
     * @param string $isBatchJob
     * @return reports
     */
    public function setIsBatchJob($isBatchJob)
    {
        $this->isBatchJob = $isBatchJob;
    }

    /**
     *
     * @return string
     */
    public function getIsBatchJob()
    {
        return $this->isBatchJob;
    }

    /**
     * Set isCoordinatorReport
     *
     * @param boolean $isCoordinatorReport
     * @return reports
     */
    public function setIsCoordinatorReport($isCoordinatorReport)
    {
        $this->isCoordinatorReport = $isCoordinatorReport;
    }

    /**
     * Get isCoordinatorReport
     *
     * @return boolean
     */
    public function getIsCoordinatorReport()
    {
        return $this->isCoordinatorReport;
    }

    /**
     * @param boolean $shortCode
     */
    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;
    }

    /**
     * @return boolean
     */
    public function getShortCode()
    {
        return $this->shortCode;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return reports
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return int
     */
    public function getReportViewId()
    {
        return $this->reportViewId;
    }

    /**
     * @param int $reportViewId
     */
    public function setReportViewId($reportViewId)
    {
        $this->reportViewId = $reportViewId;
    }

}