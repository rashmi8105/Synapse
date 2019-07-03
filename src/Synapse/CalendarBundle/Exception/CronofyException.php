<?php
namespace Synapse\CalendarBundle\Exception;

use Synapse\CoreBundle\Exception\SynapseException;

class CronofyException extends SynapseException
{
    /**
     * CronofyException constructor.
     *
     * @param string $message
     * @param int $code
     * @param null|string $errorDetails
     * @param null|string $url
     * @param null|string $userMessage
     */
    public function __construct($message, $code, $errorDetails = null, $url = null, $userMessage = "Mapworks encountered a problem communicating with external calendars.  Please contact technical support.")
    {
        $developerMessage = json_encode(['error_url' => $url, 'error_message' => $code . '-' . $message, 'trace_string' => $this->getTraceAsString()]);
        parent::__construct($developerMessage, $userMessage, "cronofy_error", 400);
    }
}