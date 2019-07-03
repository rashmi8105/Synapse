<?php
namespace Synapse\AuthenticationBundle\Exception;

use Synapse\CoreBundle\Exception\SynapseException;

class UnconfiguredOrganizationException extends SynapseException
{
    function __construct(
        $orgId
    ) {
        parent::__construct(
            "Organization $orgId missing SAML config",
            "SAML is not configured for your organization",
            'saml_unconfig',
            400
        );
    }
}