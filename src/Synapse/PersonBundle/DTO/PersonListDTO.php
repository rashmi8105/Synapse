<?php
namespace Synapse\PersonBundle\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Group of Persons that are being submitted to create, update.
 */
class PersonListDTO
{
    /**
     * Array of persons
     *
     * @var PersonDTO[]
     * @JMS\Type("array<Synapse\PersonBundle\DTO\PersonDTO>")
     *
     */
    private $personList;

    /**
     * @return PersonDTO[]
     */
    public function getPersonList()
    {
        return $this->personList;
    }

    /**
     * @param PersonDTO[] $personList
     */
    public function setPersonList($personList)
    {
        $this->personList = $personList;
    }
}
