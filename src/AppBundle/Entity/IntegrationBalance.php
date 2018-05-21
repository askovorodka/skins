<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IntegrationBalanceRepository")
 * @ORM\Table(name="integration_balance")
 */
class IntegrationBalance
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Integration")
     */
    protected $integration;

    /**
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2)
     */
    protected $balance = 0.00;

    /**
     * @ORM\Column(name="currency", type="string")
     */
    protected $currency;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIntegration()
    {
        return $this->integration;
    }

    /**
     * @param mixed $integration
     *
     * @return IntegrationBalance
     */
    public function setIntegration($integration)
    {
        $this->integration = $integration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param mixed $balance
     *
     * @return $this
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }
}
