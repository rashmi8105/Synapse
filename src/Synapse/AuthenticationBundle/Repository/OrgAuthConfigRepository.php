<?php
namespace Synapse\AuthenticationBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\AuthenticationBundle\Entity\OrgAuthConfig;

class OrgAuthConfigRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseAuthenticationBundle:OrgAuthConfig';
}