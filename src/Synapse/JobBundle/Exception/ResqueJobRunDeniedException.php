<?php
namespace Synapse\JobBundle\Exception;

use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\SynapseConstant;

class ResqueJobRunDeniedException extends SynapseException
{

    /**
     * ResqueJobRunDeniedException constructor.
     *
     * @param string $userMessage
     * @param string $message
     * @param string $code
     * @param int $httpCode
     */
    function __construct(
        $userMessage = "Error has found while processing scheduled job.Please try again later",
        $message = "Resque Job Error",
        $code = "resque_job_error",
        $httpCode = SynapseConstant::ACCESS_DENIED_ERROR_CODE
    )
    {
        parent::__construct(
            $message,
            $userMessage,
            $code,
            $httpCode
        );
    }
}