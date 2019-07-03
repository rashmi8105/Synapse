<?php


namespace Synapse\CoreBundle\Service;

interface EntityServiceInterface {

    public function findOneByName($name);
    
    public function getUserTypeById($userType);

}
