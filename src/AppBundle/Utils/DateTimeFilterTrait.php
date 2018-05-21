<?php

namespace AppBundle\Utils;


use Symfony\Component\HttpFoundation\Request;

trait DateTimeFilterTrait
{
    /**
     * @var \DateTime
     */
    public $dateFrom;

    /**
     * @var \DateTime
     */
    public $dateTo;

    public function getFilterFromRequest(Request $request)
    {
        $this->dateFrom = (new \DateTime($request->query->get('date_from', '-1 month')))->setTime(0, 0, 0);
        $this->dateTo = (new \DateTime($request->query->get('date_to', 'now')));
    }
}