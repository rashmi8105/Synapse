<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * IssueLang
 *
 * @ORM\Table(name="issue_lang", indexes={@ORM\Index(name="fk_issue_lang_issue1_idx", columns={"issue_id"}), @ORM\Index(name="fk_issue_lang_language_master1_idx", columns={"lang_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\IssueLangRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class IssueLang extends BaseEntity
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
     *      @ORM\JoinColumn(name="issue_id", referencedColumnName="id", nullable=false)
     *      })
     */
    private $issue;
    
    /**
     * 
     * @var \Synapse\CoreBundle\Entity\LanguageMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lang_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $lang;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
     */
    
    private $name;
    
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
     * @return IssueLang
     */
    public function setIssue(\Synapse\SurveyBundle\Entity\Issue $issue = null)
    {
    	$this->issue = $issue;
    }
    
    /**
     * Set lang
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $lang
     * @return IssueLang
     */
    public function setLang(\Synapse\CoreBundle\Entity\LanguageMaster $lang)
    {
    	$this->lang = $lang;
    
    	return $this;
    }
    
    /**
     * Get lang
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLang()
    {
    	return $this->lang;
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return IssueLang
     */
    public function setName($name)
    {
    	$this->name = $name;
    
    	return $this;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
    	return $this->name;
    }
}