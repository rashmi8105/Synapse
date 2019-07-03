<?php

namespace Synapse\CoreBundle\Exception;


use Synapse\RestBundle\Exception\RestException;

class SynapseValidationException extends RestException
{
    /**
     * SynapseValidationException constructor.
     *
     * @param string $userMessage - The message to pass to the user when this error occurs. Default: An error has occurred with Mapworks. Please contact client services.
     */
    function __construct($userMessage = "An error has occurred with Mapworks. Please contact client services.")
    {
        parent::__construct([$userMessage], $userMessage, "validation_error", 400, $userMessage);
    }
}