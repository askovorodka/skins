<?php
namespace AppBundle\Event;

use AppBundle\Entity\Deposit;
use Symfony\Component\EventDispatcher\Event;

class DepositEvent extends Event
{
    const DEPOSIT_UPDATE    =   'deposit_update_event';
    private $deposit;
    public function __construct(Deposit $deposit)
    {
        $this->deposit = $deposit;
    }

    public function getDeposit() {
        return $this->deposit;
    }

}