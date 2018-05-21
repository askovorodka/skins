<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IntegrationDebit.
 *
 * @ORM\Table(
 *    name="integration_debit",
 *    indexes={
 *      @ORM\Index(name="created", columns={"created"}),
 *      @ORM\Index(name="integration_id", columns={"integration_id"}),
 *    }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IntegrationDebitRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class IntegrationDebit implements \JsonSerializable
{
    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const MONEY_TYPE_YANDEX = 'yandex';
    const MONEY_TYPE_WEBMONEY = 'webmoney';
    const MONEY_TYPE_QIWI   = 'qiwi';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Integration")
     */
    protected $integration;

    /**
     * @ORM\Column(name="currency", type="string")
     */
    protected $currency;

    /**
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     */
    protected $amount;

    /**
     * @var string
     * @ORM\Column(name="note", type="string")
     */
    protected $note;

    /**
     * @var \DateTime()
     *
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime()
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    protected $updated;

    /**
     * @var string
     * @ORM\Column(name="payment_system", type="string", options={"comment":"Тип кошелька"}, nullable=true)
     */
    protected $paymentSystem;

    /**
     * @var string
     * @ORM\Column(name="payment_destination", type="string", options={"comment":"Номер кошелька"}, nullable=true)
     */
    protected $paymentDestination;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    protected $status = self::STATUS_NEW;

    public function __construct($integration, $currency, $amount, $note, $payment_system = null, $payment_destination = null)
    {
        $this->setCurrency($currency)
            ->setIntegration($integration)
            ->setAmount($amount)
            ->setNote($note)
            ->setPaymentSystem($payment_system)
            ->setPaymentDestination($payment_destination)
        ;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return IntegrationDebit
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Integration
     */
    public function getIntegration()
    {
        return $this->integration;
    }

    /**
     * @param mixed $integration
     *
     * @return IntegrationDebit
     */
    public function setIntegration($integration)
    {
        $this->integration = $integration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     *
     * @return IntegrationDebit
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return IntegrationDebit
     *
     * @ORM\PrePersist()
     */
    public function setCreated()
    {
        $this->created = new \DateTime();

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return IntegrationDebit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return IntegrationDebit
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime();

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

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return IntegrationDebit
     */
    public function setNote(string $note): IntegrationDebit
    {
        $this->note = $note;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'created' => $this->created,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'status' => $this->status,
        ];
    }

    /**
     * Set paymentSystem.
     *
     * @param string $paymentSystem
     *
     * @return IntegrationDebit
     */
    public function setPaymentSystem($paymentSystem)
    {
        $this->paymentSystem = $paymentSystem;

        return $this;
    }

    /**
     * Get paymentSystem.
     *
     * @return string
     */
    public function getPaymentSystem()
    {
        return $this->paymentSystem;
    }

    /**
     * Set paymentDestination.
     *
     * @param string $paymentDestination
     *
     * @return IntegrationDebit
     */
    public function setPaymentDestination($paymentDestination)
    {
        $this->paymentDestination = $paymentDestination;

        return $this;
    }

    /**
     * Get paymentDestination.
     *
     * @return string
     */
    public function getPaymentDestination()
    {
        return $this->paymentDestination;
    }
}
