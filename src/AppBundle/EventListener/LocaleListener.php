<?php

namespace AppBundle\EventListener;

use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

        if ($locale = $request->get('lang')) {
            $path = parse_url($request->getUri(), PHP_URL_PATH);
            $query = $request->query->all();
            unset($query['lang']);
            $request->query->replace($query);
            if (!preg_match("%(/en/|/ru/)%", $path)) {
                $redirectUrl = '/' . $locale . $path . '?' . http_build_query($request->query->all());
            } else {
                $redirectUrl = preg_replace('%(/en/|/ru/)%', '/' . $locale . '/', $path) . '?' . http_build_query($request->query->all());
            }
            $event->setResponse(new RedirectResponse($redirectUrl));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }
}