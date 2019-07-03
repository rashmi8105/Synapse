<?php
namespace Synapse\RestBundle\EventListener;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @DI\Service("synapse_locale_listener")
 * @DI\Tag("kernel.event_listener", attributes = {"event" = "kernel.request"})
 */
class LocaleListener implements EventSubscriberInterface
{

    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->headers->get('accept_language')) {
            $request->setLocale(str_replace('-', '_', $request->headers->get('accept_language')));
        } else {
            $request->setLocale($this->defaultLocale);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}