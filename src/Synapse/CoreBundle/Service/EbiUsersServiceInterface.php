<?php
namespace Synapse\CoreBundle\Service;

interface EbiUsersServiceInterface
{
    public function hasEbiUsersAccess($loggedInUser);
}