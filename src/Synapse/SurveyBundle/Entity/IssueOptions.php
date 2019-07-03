<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * IssueOptions
 *
 * @ORM\Table(name="issue_options", indexes={@ORM\Index(name="fk_issue_options_issue1_idx", columns={"issue_id"}), @ORM\Index(name="fk_issue_options_ebi_question_options1_idx", columns={"ebi_question_options_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\IssueOptionsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class IssueOptions extends BaseEntity
{
    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     *
     * @var \Synapse\SurveyBundle\Entity\Issue @ORM\ManyToOne(targetEntity="Synapse\SurveyBundle\Entity\Issue")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="issue_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $issue;
    
    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiQuestionOptions @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiQuestionOptions")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_question_options_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $ebiQuestionOptions;
    
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
     * Get Issue
     *
     * @return \Synapse\SurveyBundle\Entity\Issue
     */
    public function getIssue()
    {
    	return $this->issue;
    }
    
    /**
     * Set Issue
     * @param \Synapse\SurveyBundle\Entity\Issue $issue  
     * @return integer
     */
    public function setIssue(\Synapse\SurveyBundle\Entity\Issue $issue = null)
    {
    	$this->issue =  $issue;
    	return $this;
    }
    
    /**
     * Set EbiQuestionOptions
     *
     * @param \Synapse\CoreBundle\Entity\EbiQuestionOptions $ebiQuestionOptions
     * @return EbiQuestionOptions
     */
    public function setEbiQuestionOptions(\Synapse\CoreBundle\Entity\EbiQuestionOptions $ebiQuestionOptions = null)
    {
    	$this->ebiQuestionOptions = $ebiQuestionOptions;
    
    	return $this;
    }
    
    /**
     * Get EbiQuestionOptions
     *
     * @return \Synapse\CoreBundle\Entity\EbiQuestionOptions
     */
    public function getEbiQuestionOptions()
    {
    	return $this->ebiQuestionOptions;
    }
}
