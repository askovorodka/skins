<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 01.12.16
 * Time: 16:41
 */

namespace AppBundle\EventListener;

use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ReferrerListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        try {
            $request = $event->getRequest();
            $session = $request->getSession();
            $referrerUrl = $request->headers->get('referer');
            $host = parse_url($referrerUrl, PHP_URL_HOST);
            $refPort = parse_url($referrerUrl, PHP_URL_PORT);
            $skinsHost = $request->getHost();

            $this->logger->info("referrer listener", [$skinsHost, $host]);
            if (!empty($referrerUrl))
            {
                $sessionHost = null;
                if ($session->get('ref_url')) {
                    $sessionUrl = $session->get('ref_url');
                    $sessionHost = parse_url($sessionUrl, PHP_URL_HOST);
                }

                if (!$session->get('ref_url') || ($sessionHost !== $host)) {
                    $session->set('ref_url', $referrerUrl);
                    $this->logger->info('set up ref_url', [$referrerUrl]);
                }

            }
        }
        catch(\Exception $exception) {
            $this->logger->crit('Exception: ', [$exception->getMessage()]);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 16)),
        );
    }
}