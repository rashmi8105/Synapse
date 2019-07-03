<?php

namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
/**
 * reports_view
 *
 * @ORM\Table(name="reports_view")
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportsViewRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportsView extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="view_name", type="string", length=100, nullable=false)
     * @JMS\Expose
     */
    private $viewName;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getViewName()
    {
        return $this->viewName;
    }

    /**
     * @param string $viewName
     */
    public function setViewName($viewName)
    {
        $this->viewName = $viewName;
    }

}