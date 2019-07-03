<?php

namespace Synapse\RestBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RestBundle\Entity\Response as SynapseResponse;
use Synapse\RestBundle\Entity\Error as SynapseError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Synapse\CoreBundle\Exception\AccessDeniedException;

/**
 * @DI\Service("synapse_rest_exception_listener")
 * @DI\Tag("kernel.event_listener", attributes = {"event" = "kernel.exception"})
 */
class RestExceptionListener
{

    /**
     * @var jms_serializer
     */
    protected $serializer;

    /**
     * @var Logger logger
     */
    protected $logger;

    /**
     * @var Translator translator
     */
    protected $translator;

    /**
     * @param mixed $serializer
     *
     * @DI\InjectParams({
     *     "serializer" = @DI\Inject("jms_serializer")
     * })
     *
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }


    /**
     * @param Logger $logger
     *
     * @DI\InjectParams({
     *     "logger" = @DI\Inject("logger")
     * })
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Translator $translator
     *
     * @DI\InjectParams({
     *     "translator" = @DI\Inject("translator")
     * })
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        $exception = $event->getException();

        $this->logger->error(
            $exception->getMessage(),
            [
                'trace' => $exception->getTrace(),
                'code' => $exception->getCode(),
                // these are temporary. Need to figure out a better way to handle this.
                'eventId' => method_exists($exception,'getEventId') ? $exception->getEventId() : null,
                'info' => method_exists($exception,'getInfo') ? $exception->getInfo(): null,
            ]
        );

        $httpStatusCode = $this->getResponseStatusCode($exception);

        $synapseResponse = new SynapseResponse([]);
        if ($exception instanceof ValidationException || $exception instanceof SynapseValidationException){
            $synapseResponse->setErrors($exception->getErrors());
        }
        else if ($exception instanceof SynapseException){
            $error = new SynapseError(
                $exception->getCode(),
                $this->translator->trans($exception->getUserMessage()),
                $exception->getEventId(),
                $exception->getInfo()
            );
            $synapseResponse->addError($error);
        }
        else{
            $error = new SynapseError($exception->getCode(), $this->translator->trans($exception->getMessage()), null);
            $synapseResponse->addError($error);
        }

        $response = new Response();
        $content = $this->serializer->serialize($synapseResponse, "json");
        $response->setContent($content);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode($httpStatusCode);
        }

        // Send the modified response object to the event
        $event->setResponse($response);
    }

    /**
     * Returns the corresponding status code for the exception
     * @param $exception
     * @return int status code
     */
    protected function getResponseStatusCode($exception)
    {

        if (method_exists($exception, 'getHttpCode')) {
            $statusCode = $exception->getHttpCode();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        if ($exception instanceof EntityNotFoundException){
            $statusCode = Response::HTTP_NOT_FOUND;
        }
        else if ($exception instanceof InvalidArgumentException || $exception instanceof ValidationException){
            $statusCode = Response::HTTP_BAD_REQUEST;
        }
        else if ($exception instanceof UnauthorizedException){
            $statusCode = Response::HTTP_UNAUTHORIZED;
        }
        else if ($exception instanceof AccessDeniedException){
            $statusCode = Response::HTTP_FORBIDDEN;
        }

        return $statusCode;
    }
}