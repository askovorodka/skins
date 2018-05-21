<?php

namespace AppBundle\Utils;


use Symfony\Component\HttpFoundation\Request;

trait PaginationTrait
{
    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $offset;

    public function getLimitAndOffsetFromRequest(Request $request)
    {
        $this->limit = $request->query->get('limit', 30);
        $this->offset = $request->query->get('offset', 0);
    }
}