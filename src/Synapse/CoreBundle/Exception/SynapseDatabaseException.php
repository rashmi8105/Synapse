<?php

namespace Synapse\CoreBundle\Exception;


class SynapseDatabaseException extends SynapseException
{

    /**
     * SynapseDatabaseException constructor.
     *
     * @param string $message - Message sent to the logs for developers about specific information happening when something bad happened at the DB level
     */
    function __construct($message = "A database error has occurred. Please review system logs for more information. You can override this message when throwing SynapseDatabaseException to get more useful details if needed.")
    {
        echo $message;exit;
        parent::__construct(
            $message,
            "An error has occurred with Mapworks. Please contact client services.",
            "database_error",
            500
        );
    }
}