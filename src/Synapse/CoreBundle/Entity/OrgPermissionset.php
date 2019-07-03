<?php

namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * OrgPermissionset
 *
 * @ORM\Table(name="org_permissionset", indexes={@ORM\Index(name="permissionset_organizationid", columns={"organization_id"}),@ORM\Index(name="permissionset_name_idx", columns={"permissionset_name"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPermissionsetRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"permissionsetName", "organization"},message="Permission Template Name already exists.")
 */
class OrgPermissionset extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="permissionset_name", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=100,maxMessage = "Permission Template Name cannot be longer than {{ limit }} characters");
     *
     *
     */
    private $permissionsetName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_archived", type="boolean", length=1, nullable=true)
     *
     */
    private $isArchived;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accesslevel_ind_agg", type="boolean", length=1, nullable=true)
     *
     */
    private $accesslevelIndAgg;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accesslevel_agg", type="boolean", length=1, nullable=true)
     *
     */
    private $accesslevelAgg;

    /**
     * @var boolean
     *
     * @ORM\Column(name="risk_indicator", type="boolean", length=1, nullable=true)
     *
     */
    private $riskIndicator;

    /**
     * @var boolean
     *
     * @ORM\Column(name="intent_to_leave", type="boolean", length=1, nullable=true)
     *
     */
    private $intentToLeave;

    /**
     * @var boolean
     *
     * @ORM\Column(name="view_courses", type="boolean", length=1, nullable=true)
     *
     */
    private $viewCourses;

    /**
     * @var boolean
     *
     * @ORM\Column(name="create_view_academic_update", type="boolean", length=1, nullable=true)
     *
     */
    private $createViewAcademicUpdate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="view_all_academic_update_courses", type="boolean", length=1, nullable=true)
     *
     */
    private $viewAllAcademicUpdateCourses;

    /**
     * @var boolean
     *
     * @ORM\Column(name="view_all_final_grades", type="boolean", length=1, nullable=true)
     *
     */
    private $viewAllFinalGrades;

    /**
     * @var boolean
     *
     * @ORM\Column(name="current_future_isq", type="boolean", length=1, nullable=true)
     *
     */
    private $currentFutureIsq;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $organization;

    /**
     * @var boolean
     *
     * @ORM\Column(name="retention_completion", type="boolean", length=1, nullable=true)
     *
     */
    private $retentionCompletion;

    /**
     * @param boolean $intentToLeave
     */
    public function setIntentToLeave($intentToLeave)
    {
        $this->intentToLeave = $intentToLeave;
    }

    /**
     * @return boolean
     */
    public function getIntentToLeave()
    {
        return $this->intentToLeave;
    }

    /**
     * @param boolean $riskIndicator
     */
    public function setRiskIndicator($riskIndicator)
    {
        $this->riskIndicator = $riskIndicator;
    }

    /**
     * @return boolean
     */
    public function getRiskIndicator()
    {
        return $this->riskIndicator;
    }

    /**
     * @param boolean $accesslevelAgg
     */
    public function setAccesslevelAgg($accesslevelAgg)
    {
        $this->accesslevelAgg = $accesslevelAgg;
    }

    /**
     * @return boolean
     */
    public function getAccesslevelAgg()
    {
        return $this->accesslevelAgg;
    }

    /**
     * @param boolean $accesslevelIndAgg
     */
    public function setAccesslevelIndAgg($accesslevelIndAgg)
    {
        $this->accesslevelIndAgg = $accesslevelIndAgg;
    }

    /**
     * @return boolean
     */
    public function getAccesslevelIndAgg()
    {
        return $this->accesslevelIndAgg;
    }



    /**
     * Set permissionsetName
     *
     * @param string $permissionsetName
     * @return OrgPermissionset
     */
    public function setPermissionsetName($permissionsetName)
    {
        $this->permissionsetName = $permissionsetName;

        return $this;
    }

    /**
     * Get permissionsetName
     *
     * @return string
     */
    public function getPermissionsetName()
    {
        return $this->permissionsetName;
    }

    /**
     * Set isArchived
     *
     * @param string $isArchived
     * @return OrgPermissionset
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return string
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgPermissionset
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param boolean $createViewAcademicUpdate
     */
    public function setCreateViewAcademicUpdate($createViewAcademicUpdate)
    {
        $this->createViewAcademicUpdate = $createViewAcademicUpdate;
    }

    /**
     * @return boolean
     */
    public function getCreateViewAcademicUpdate()
    {
        return $this->createViewAcademicUpdate;
    }

    /**
     * @param boolean $viewAllAcademicUpdateCourses
     */
    public function setViewAllAcademicUpdateCourses($viewAllAcademicUpdateCourses)
    {
        $this->viewAllAcademicUpdateCourses = $viewAllAcademicUpdateCourses;
    }

    /**
     * @return boolean
     */
    public function getViewAllAcademicUpdateCourses()
    {
        return $this->viewAllAcademicUpdateCourses;
    }

    /**
     * @param boolean $viewAllFinalGrades
     */
    public function setViewAllFinalGrades($viewAllFinalGrades)
    {
        $this->viewAllFinalGrades = $viewAllFinalGrades;
    }

    /**
     * @return boolean
     */
    public function getViewAllFinalGrades()
    {
        return $this->viewAllFinalGrades;
    }

    /**
     * @param boolean $viewCourses
     */
    public function setViewCourses($viewCourses)
    {
        $this->viewCourses = $viewCourses;
    }

    /**
     * @return boolean
     */
    public function getViewCourses()
    {
        return $this->viewCourses;
    }

    /**
     * @param boolean $currentFutureIsq
     */
    public function setCurrentFutureIsq($currentFutureIsq)
    {
        $this->currentFutureIsq = $currentFutureIsq;
    }

    /**
     * @return boolean
     */
    public function getCurrentFutureIsq()
    {
        return $this->currentFutureIsq;
    }

    /**
     * @return boolean
     */
    public function getRetentionCompletion()
    {

        return $this->retentionCompletion;
    }

    /**
     * @param boolean $retentionCompletion
     */
    public function setRetentionCompletion($retentionCompletion)
    {

        $this->retentionCompletion = $retentionCompletion;
    }

}

