<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TronReportingController.
 */
class TronReportingController extends Controller
{
    /**
     * @Route("/tron/profit")
     * @Method({"GET"})
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     * @throws \Prometheus\Exception\MetricNotFoundException
     */
    public function profitAction()
    {
        $tron = $this->get('app.tron_reporting');
        $tron->registerProfitCounter();
        $tron->snapProfitCounter();

        return new Response($tron->render(), 200, ['Content-Type' => 'text/plain']);
    }
}
