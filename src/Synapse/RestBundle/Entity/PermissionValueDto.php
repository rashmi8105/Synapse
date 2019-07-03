<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class PermissionValueDto implements DtoInterface
{

    /**
     * Block Selection
     *
     * @JMS\Type("boolean")
     */
    private $view;

    /**
     * Block Selection
     *
     * @JMS\Type("boolean")
     */
    private $create;

    /**
     *
     * @param mixed $create            
     */
    public function setCreate($create)
    {
        $this->create = $create;
    }

    /**
     *
     * @return mixed
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     *
     * @param mixed $view            
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     *
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {
        $this->view = (isset($attributes['view'])) ? (bool)$attributes['view'] : null;
        $this->create = (isset($attributes['create'])) ? (bool)$attributes['create'] : null;
    }
}
