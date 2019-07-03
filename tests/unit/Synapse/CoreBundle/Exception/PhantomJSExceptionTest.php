<?php

use Codeception\TestCase\Test;
use Synapse\CoreBundle\Exception\PhantomJsException;

class PhantomJSExceptionTest extends Test
{
    use \Codeception\Specify;

    /**
     * @var string
     */
    private $defaultErrorMessage = "PhantomJS encountered an unknown error code.";

    /**
     * @var string
     */
    private $defaultUserErrorMessage = "Mapworks encountered an error while attempting to generate the pdf. Please contact Mapworks Client Services";

    protected function _before()
    {

    }

    public function testPhantomJsException()
    {
        $this->specify("Test PhantomJsException class ", function ($exceptedExceptionMessage, $code, $customDeveloperMessage = null, $customUserMessage = null) {
            $phantomJsException = new PhantomJsException($code, $customDeveloperMessage, $customUserMessage);
            $errorMessage = $phantomJsException->getErrorMessage($code);
            $userMessage = $phantomJsException->getUserMessage();
            if ($customDeveloperMessage == null && $customUserMessage == null) {
                // both developer and user custom message not provided
                $this->assertEquals($exceptedExceptionMessage, $errorMessage);
                $this->assertEquals($this->defaultUserErrorMessage, $userMessage);
            } else if ($customDeveloperMessage == null && $customUserMessage != null) {
                // custom user message provided
                $this->assertEquals($exceptedExceptionMessage, $userMessage);
            } else if ($customDeveloperMessage != null && $customUserMessage == null) {
                // custom developer message provided not using getErrorMessage as it provides code specific message
                $this->assertEquals($exceptedExceptionMessage, $phantomJsException->getMessage());
            }
        }, [
            'examples' => [
                // this example tests when a custom developer error message provided
                [
                    "Server is unable to process your request.",
                    1, // some invalid error code,
                    "Server is unable to process your request.",
                    null
                ],
                // this example tests when a custom user error message provided with valid error code
                [
                    "Unable to process your request.", // expected error message
                    1, // error code
                    null, // custom developer message
                    "Unable to process your request." // custom user message
                ],
                // this example tests when there is non existing error code passed
                [
                    $this->defaultErrorMessage,
                    1001, // some invalid error code,
                    null,
                    null
                ],
                // all examples below verifies exception message with given exception code
                [
                    "the remote server refused the connection (the server is not accepting requests).",
                    1,
                    null,
                    null
                ],
                [
                    "the remote server closed the connection prematurely, before the entire reply was received and processed.",
                    2,
                    null,
                    null
                ],
                [
                    "the remote host name was not found (invalid hostname).",
                    3,
                    null,
                    null
                ],
                [
                    "the connection to the remote server timed out.",
                    4,
                    null,
                    null
                ],
                [
                    "the operation was canceled via calls to abort() or close() before it was finished.",
                    5,
                    null,
                    null
                ],
                [
                    "the SSL/TLS handshake failed and the encrypted channel could not be established. The sslErrors() signal should have been emitted.",
                    6,
                    null,
                    null
                ],
                [
                    "the connection was broken due to disconnection from the network, however the system has initiated roaming to another access point. The request should be resubmitted and will be processed as soon as the connection is re-established.",
                    7,
                    null,
                    null
                ],
                [
                    "the connection was broken due to disconnection from the network or failure to start the network.",
                    8,
                    null,
                    null
                ],
                [
                    "the background request is not currently allowed due to platform policy.",
                    9,
                    null,
                    null
                ],
                [
                    "while following redirects, the maximum limit was reached. The limit is by default set to 50 or as set by QNetworkRequest::setMaxRedirectsAllowed().",
                    10,
                    null,
                    null
                ],
                [
                    "while following redirects, the network access API detected a redirect from a encrypted protocol (https) to an unencrypted one (http).",
                    11,
                    null,
                    null
                ],
                [
                    "an unknown network-related error was detected",
                    99,
                    null,
                    null
                ],
                [
                    "the connection to the proxy server was refused (the proxy server is not accepting requests).",
                    101,
                    null,
                    null
                ],
                [
                    "the proxy server closed the connection prematurely, before the entire reply was received and processed.",
                    102,
                    null,
                    null
                ],
                [
                    "the proxy host name was not found (invalid proxy hostname).",
                    103,
                    null,
                    null
                ],
                [
                    "the connection to the proxy timed out or the proxy did not reply in time to the request sent.",
                    104,
                    null,
                    null
                ],
                [
                    "the proxy requires authentication in order to honour the request but did not accept any credentials offered (if any).",
                    105,
                    null,
                    null
                ],
                [
                    "an unknown proxy-related error was detected",
                    199,
                    null,
                    null
                ],
                [
                    "the access to the remote content was denied.",
                    201,
                    null,
                    null
                ],
                [
                    "the operation requested on the remote content is not permitted.",
                    202,
                    null,
                    null
                ],
                [
                    "the remote content was not found at the server.",
                    203,
                    null,
                    null
                ],
                [
                    "the remote server requires authentication to serve the content but the credentials provided were not accepted.",
                    204,
                    null,
                    null
                ],
                [
                    "the request needed to be sent again, but this failed for example because the upload data could not be read a second time.",
                    205,
                    null,
                    null
                ],
                [
                    "the request could not be completed due to a conflict with the current state of the resource.",
                    206,
                    null,
                    null
                ],
                [
                    "the requested resource is no longer available at the server.",
                    207,
                    null,
                    null
                ],
                [
                    "an unknown error related to the remote content was detected",
                    299,
                    null,
                    null
                ],
                [
                    "the Network Access API cannot honor the request because the protocol is not known.",
                    301,
                    null,
                    null
                ],
                [
                    "the requested operation is invalid for this protocol.",
                    302,
                    null,
                    null
                ],
                [
                    "a breakdown in protocol was detected (parsing error, invalid or unexpected responses, etc.)",
                    399,
                    null,
                    null
                ],
                [
                    "the server encountered an unexpected condition which prevented it from fulfilling the request.",
                    401,
                    null,
                    null
                ],
                [
                    "the server does not support the functionality required to fulfill the request.",
                    402,
                    null,
                    null
                ],
                [
                    "the server is unable to handle the request at this time.",
                    403,
                    null,
                    null
                ],
                [
                    "an unknown error related to the server response was detected",
                    499,
                    null,
                    null
                ],
            ]
        ]);
    }

}