<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EbiSearchCriteria
 *
 * @ORM\Table(name="ebi_search_criteria")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\SearchCriteriaRepository")
 * @ORM\Table(name="ebi_search_criteria", indexes={@ORM\Index(name="fk_ebi_search_criteria_ebi_search1_idx", columns={"ebi_search_id"})})
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EbiSearchCriteria extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     *     
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiSearch @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiSearch")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_search_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $ebiSearch;

    /**
     *
     * @var string @Assert\Length(max="200")
     *      @ORM\Column(name="table_name", type="string", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $tableName;

    /**
     *
     * @var string @Assert\Length(max="200")
     *      @ORM\Column(name="field_name", type="string", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $fieldName;

    /**
     *
     * @var string @Assert\Length(max="10")
     *      @ORM\Column(name="operator", type="string", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $operator;

    /**
     *
     * @var string @Assert\Length(max="5000")
     *      @ORM\Column(name="value", type="string", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $value;

    /**
     *
     * @var string @Assert\Length(max="3")
     *      @ORM\Column(name="join_condition", type="string", nullable=true)
     *     
     *      @JMS\Expose
     */
    private $joinCondition;

    /**
     * Set ebiSearch
     *
     * @param \Synapse\CoreBundle\Entity\EbiSearch $ebiSearch            
     * @return EbiSearchCriteria
     */
    public function setEbiSearch(\Synapse\CoreBundle\Entity\EbiSearch $ebiSearch = null)
    {
        $this->ebiSearch = $ebiSearch;
        
        return $this;
    }

    /**
     * Get ebiSearchLang
     *
     * @return \Synapse\CoreBundle\Entity\EbiSearch
     */
    public function getEbiSearch()
    {
        return $this->ebiSearch;
    }

    /**
     *
     * @param string $tableName            
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     *
     * @param string $fieldName            
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     *
     * @param string $operator            
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     *
     * @param string $value            
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     * @param string $joinCondition            
     */
    public function setJoinCondition($joinCondition)
    {
        $this->joinCondition = $joinCondition;
    }

    /**
     *
     * @return string
     */
    public function getJoinCondition()
    {
        return $this->joinCondition;
    }

    /**
     *
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
}