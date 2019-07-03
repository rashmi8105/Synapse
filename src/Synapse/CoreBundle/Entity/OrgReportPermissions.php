<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgReportPermissions
 *
 * @ORM\Table(name="org_report_permissions", indexes={@ORM\Index(name="fk_org_report_permission_organization_id", columns={"organization_id"}), @ORM\Index(name="fk_org_report_permission_report_id", columns={"report_id"}), @ORM\Index(name="fk_org_report_permission_permissionset_id", columns={"org_permissionset_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgReportPermissionsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgReportPermissions extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;    

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $organization;
    
    /**
     * @var \Synapse\CoreBundle\Entity\OrgPermissionset
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgPermissionset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_permissionset_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgPermissionset;
    
    /**
     * @var \Synapse\ReportsBundle\Entity\Reports
     *
     * @ORM\ManyToOne(targetEntity="\Synapse\ReportsBundle\Entity\Reports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $report;    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="timeframe_all", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $timeframeAll;

    /**
     * @var boolean
     *
     * @ORM\Column(name="current_calendar", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $currentCalendar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="previous_calendar", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $previousCalendar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="next_period", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $nextPeriod;

    /**
     * @param boolean $currentCalendar
     */
    public function setCurrentCalendar($currentCalendar)
    {
        $this->currentCalendar = $currentCalendar;
    }

    /**
     * @return boolean
     */
    public function getCurrentCalendar()
    {
        return $this->currentCalendar;
    }

    /**
     * @param boolean $nextPeriod
     */
    public function setNextPeriod($nextPeriod)
    {
        $this->nextPeriod = $nextPeriod;
    }

    /**
     * @return boolean
     */
    public function getNextPeriod()
    {
        return $this->nextPeriod;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset
     */
    public function setOrgPermissionset($orgPermissionset)
    {
        $this->orgPermissionset = $orgPermissionset;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\OrgPermissionset
     */
    public function getOrgPermissionset()
    {
        return $this->orgPermissionset;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param boolean $previousCalendar
     */
    public function setPreviousCalendar($previousCalendar)
    {
        $this->previousCalendar = $previousCalendar;
    }

    /**
     * @return boolean
     */
    public function getPreviousCalendar()
    {
        return $this->previousCalendar;
    }

    /**
     * @param \Synapse\ReportsBundle\Entity\Reports $report
     */
    public function setReport($report)
    {
        $this->report = $report;
    }

    /**
     * @return \Synapse\ReportsBundle\Entity\Reports
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param boolean $timeframeAll
     */
    public function setTimeframeAll($timeframeAll)
    {
        $this->timeframeAll = $timeframeAll;
    }

    /**
     * @return boolean
     */
    public function getTimeframeAll()
    {
        return $this->timeframeAll;
    }
    

}
