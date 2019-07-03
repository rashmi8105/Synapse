<?php
namespace Synapse\AuthenticationBundle\Exception;

use Synapse\CoreBundle\Exception\SynapseException;

class MetadataException extends SynapseException
{
    function __construct(
        $orgId,
        $errors = []
    ) {
        parent::__construct(
            "Error validating SAML metadata for organization $orgId",
            "SAML metadata could not be generated for your organization",
            'saml_metadata',
            400
        );

        $this->addInfo('errors', $errors);
    }
}