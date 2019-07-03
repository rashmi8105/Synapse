<?php
namespace Synapse\AcademicUpdateBundle\Exception;

use Synapse\CoreBundle\Exception\SynapseException;
class AcademicUpdateCreateException extends  SynapseException
{
    /**
     *
     * @param string $message
     * @param int|string $code
     */
    function __construct(
        $message = "Academic Update Not Created",
        $userMessage = "There was an error creating this academic update",
        $code = "academic_update_not_created",
        $httpCode = 404
    ) {
        parent::__construct($message, $userMessage, $code, $httpCode);
    }
}