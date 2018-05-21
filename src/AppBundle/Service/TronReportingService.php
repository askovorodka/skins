<?php

namespace AppBundle\Service;

use AppBundle\Repository\DepositRepository;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

class TronReportingService
{
    const NAMESPACE = 's4r';
    const PROFIT_COUNTER = 'profit';

    /**
     * @var CollectorRegistry
     */
    private $registry;

    /**
     * @var RenderTextFormat
     */
    private $renderer;

    /**
     * @var DepositRepository
     */
    private $depositRepository;

    public function __construct(
        CollectorRegistry $registry,
        RenderTextFormat $renderer,
        DepositRepository $depositRepository
    ) {
        $this->registry = $registry;
        $this->renderer = $renderer;
        $this->depositRepository = $depositRepository;
    }

    public function registerProfitCounter(array $labels = ['profit'])
    {
        $this->registry->registerCounter(self::NAMESPACE, self::PROFIT_COUNTER, '', $labels);
    }

    public function snapProfitCounter(array $labels = ['profit'])
    {
        $counter = $this->registry->getCounter(self::NAMESPACE, self::PROFIT_COUNTER);
        $profit = $this->depositRepository->getProfitTotal();
        $counter->incBy($profit, $labels);
    }

    public function render(): string
    {
        $samples = $this->registry->getMetricFamilySamples();

        return $this->renderer->render($samples);
    }
}
