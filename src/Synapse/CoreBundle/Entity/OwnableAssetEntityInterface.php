<?php
/**
 * Created by PhpStorm.
 * User: tsmith
 * Date: 1/9/2015
 * Time: 10:01 AM
 */

namespace Synapse\CoreBundle\Entity;


interface OwnableAssetEntityInterface
{
    /**
     * @param \Synapse\CoreBundle\Entity\Person $personIdFaculty
     */
    public function setPersonIdFaculty(Person $personIdFaculty = null);

    /**
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdFaculty();

    /**
     * @param \Synapse\CoreBundle\Entity\Person $personIdStudent
     */
    public function setPersonIdStudent(Person $personIdStudent = null);

    /**
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdStudent();
}
