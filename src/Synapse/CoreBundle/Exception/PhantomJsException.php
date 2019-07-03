<?php

namespace Synapse\CoreBundle\Exception;

class PhantomJsException extends SynapseException
{
    /**
     * @var array
     */
    private $errorCodeArray = [
        1 => "the remote server refused the connection (the server is not accepting requests).",
        2 => "the remote server closed the connection prematurely, before the entire reply was received and processed.",
        3 => "the remote host name was not found (invalid hostname).",
        4 => "the connection to the remote server timed out.",
        5 => "the operation was canceled via calls to abort() or close() before it was finished.",
        6 => "the SSL/TLS handshake failed and the encrypted channel could not be established. The sslErrors() signal should have been emitted.",
        7 => "the connection was broken due to disconnection from the network, however the system has initiated roaming to another access point. The request should be resubmitted and will be processed as soon as the connection is re-established.",
        8 => "the connection was broken due to disconnection from the network or failure to start the network.",
        9 => "the background request is not currently allowed due to platform policy.",
        10 => "while following redirects, the maximum limit was reached. The limit is by default set to 50 or as set by QNetworkRequest::setMaxRedirectsAllowed().",
        11 => "while following redirects, the network access API detected a redirect from a encrypted protocol (https) to an unencrypted one (http).",
        99 => "an unknown network-related error was detected",
        101 => "the connection to the proxy server was refused (the proxy server is not accepting requests).",
        102 => "the proxy server closed the connection prematurely, before the entire reply was received and processed.",
        103 => "the proxy host name was not found (invalid proxy hostname).",
        104 => "the connection to the proxy timed out or the proxy did not reply in time to the request sent.",
        105 => "the proxy requires authentication in order to honour the request but did not accept any credentials offered (if any).",
        199 => "an unknown proxy-related error was detected",
        201 => "the access to the remote content was denied.",
        202 => "the operation requested on the remote content is not permitted.",
        203 => "the remote content was not found at the server.",
        204 => "the remote server requires authentication to serve the content but the credentials provided were not accepted.",
        205 => "the request needed to be sent again, but this failed for example because the upload data could not be read a second time.",
        206 => "the request could not be completed due to a conflict with the current state of the resource.",
        207 => "the requested resource is no longer available at the server.",
        299 => "an unknown error related to the remote content was detected",
        301 => "the Network Access API cannot honor the request because the protocol is not known.",
        302 => "the requested operation is invalid for this protocol.",
        399 => "a breakdown in protocol was detected (parsing error, invalid or unexpected responses, etc.)",
        401 => "the server encountered an unexpected condition which prevented it from fulfilling the request.",
        402 => "the server does not support the functionality required to fulfill the request.",
        403 => "the server is unable to handle the request at this time.",
        499 => "an unknown error related to the server response was detected"
    ];

    /**
     * @var string
     */
    private $defaultErrorMessage = "PhantomJS encountered an unknown error code.";

    /**
     * @var string
     */
    private $defaultUserErrorMessage = "Mapworks encountered an error while attempting to generate the pdf. Please contact Mapworks Client Services";

    /**
     * PhantomJsException constructor.
     *
     * @param int $errorCode
     * @param string|null $message
     * @param string|null $userMessage
     */
    public function __construct($errorCode, $developerMessage = null, $userMessage = null)
    {
        if ($developerMessage == null) {
            $message = $this->getErrorMessage($errorCode);
        } else {
            $message = $developerMessage;
        }
        if ($userMessage == null) {
            $userMessage = $this->defaultUserErrorMessage;
        }
        parent::__construct($message, $userMessage, $errorCode);
    }

    /** Get error message for given error code
     *
     * @param $errorCode
     * @return string
     */
    function getErrorMessage($errorCode)
    {
        if (isset($this->errorCodeArray[$errorCode])) {
            return $this->errorCodeArray[$errorCode];
        }
        return $this->defaultErrorMessage;
    }

}