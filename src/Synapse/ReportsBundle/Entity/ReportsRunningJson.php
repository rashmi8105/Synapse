<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * reports_running_json
 *
 * @ORM\Table(name="reports_running_json")
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportsRunningJsonRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportsRunningJson extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="request_json", type="text", nullable=true)
     * @JMS\Expose
     */
    private $requestJson;

    /**
     *
     * @var string @ORM\Column(name="report_running_status_json", type="text", nullable=true)
     * @JMS\Expose
     */
    private $reportRunningStatusJson;
	
    /**
     *
     * @var string @ORM\Column(name="factor_json", type="text", nullable=true)
     * @JMS\Expose
     */
    private $factorJson;
	
    /**
     *
     * @var string @ORM\Column(name="gpa_json", type="text", nullable=true)
     * @JMS\Expose
     */
    private $gpaJson;

    /**
     *
     * @var string @ORM\Column(name="retention_completion_json", type="text", nullable=true)
     * @JMS\Expose
     */
    private $retentionCompletionJson;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get RequestJson
     *
     * @return string
     */
    public function getRequestJson()
    {
        return $this->requestJson;
    }

    /**
     * Set requestJson
     *
     * @param string $requestJSON
     * @return requestJson
     */
    public function setRequestJson($requestJSON)
    {
        $this->requestJson = $requestJSON;
        
        return $this;
    }

    /**
     * Get getReportRunningStatusJson
     *
     * @return string
     */
    public function getReportRunningStatusJson()
    {
        return $this->reportRunningStatusJson;
    }

    /**
     * Set reportRunnigStatusJson
     *
     * @param string $reportRunningStatusJSON
     * @return reportRunningStatusJson
     */
    public function setReportRunningStatusJson($reportRunningStatusJSON)
    {
        $this->reportRunningStatusJson = $reportRunningStatusJSON;

        return $this;
    }

    /**
     * Get factorJson
     *
     * @return string
     */
    public function getFactorJson()
    {
        return $this->factorJson;
    }

    /**
     * Set factorJson
     *
     * @param string $factorJSON
     * @return factorJson
     */
    public function setFactorJson($factorJSON)
    {
        $this->factorJson = $factorJSON;

        return $this;
    }

    /**
     * Get gpaJson
     *
     * @return string
     */
    public function getGpaJson()
    {
        return $this->gpaJson;
    }

    /**
     * Set gpaJson
     *
     * @param string $gpaJSON
     * @return gpaJson
     */
    public function setGpaJson($gpaJSON)
    {
        $this->gpaJson = $gpaJSON;

        return $this;
    }

    /**
     * Get retentionCompletionJson
     *
     * @return string
     */
    public function getRetentionCompletionJson()
    {
        return $this->retentionCompletionJson;
    }

    /**
     * Set retentionCompletionJson
     *
     * @param string $retentionCompletionJson
     * @return retentionCompletionJson
     */
    public function setRetentionCompletionJson($retentionCompletionJson)
    {
        $this->retentionCompletionJson = $retentionCompletionJson;
        return $this;
    }


}