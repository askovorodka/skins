<?php
/**
 * Created by PhpStorm.
 * User: ahimas
 * Date: 24.09.16
 * Time: 13:50
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Integration
 *
 * @ORM\Table(
 *    name="integration",
 *    uniqueConstraints={
 *     @ORM\UniqueConstraint(name="name", columns={"name"}),
 *     @ORM\UniqueConstraint(name="public_key", columns={"public_key"})
 *    }),
 * )
 * @ORM\Entity()
 */
class Integration
{
    const DEFAULT_TAX = 15;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var \DateTime()
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="pushback_url", type="string")
     */
    private $pushbackUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="home_url", type="string", nullable=true)
     */
    private $homeUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="success_url", type="string")
     */
    private $successUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="private_key", type="string")
     */
    private $privateKey;

    /**
     * @var int
     *
     * @ORM\Column(name="value_tax_percent", type="integer")
     *
     * @Assert\Range(
     *      min = 0,
     *      max = 100,
     *      minMessage = "Percent must be least {{ limit }}",
     *      maxMessage = "Percent must be below {{ limit }}"
     * )
     */
    private $valueTaxPercent;

    /**
     * @var int
     *
     * @ORM\Column(name="integration_tax_percent", type="integer")
     *
     * @Assert\Range(
     *      max = 100,
     *      min = 0,
     *      minMessage = "Percent must be least {{ limit }}",
     *      maxMessage = "Percent must be below {{ limit }}"
     * )
     */
    private $integrationTaxPercent;

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="string")
     */
    private $publicKey;

    /**
     * @var string
     *
     * @ORM\Column(name="http_auth_username", type="string", nullable=true)
     */
    private $httpAuthUsername;

    /**
     * @var string
     *
     * * @ORM\Column(name="http_auth_password", type="string", nullable=true)
     */
    private $httpAuthPassword;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_demo", type="boolean", nullable=true)
     */
    private $isDemo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_whitelabel", type="boolean", nullable=true)
     */
    private $isWhitelabel;

    /**
     * @var text
     *
     * @ORM\Column(name="logo_url", type="text", nullable=true)
     */
    private $logoUrl;


    public function __construct()
    {
        $this->created = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPushbackUrl()
    {
        return $this->pushbackUrl;
    }

    /**
     * @param mixed $pushbackUrl
     */
    public function setPushbackUrl($pushbackUrl)
    {
        $this->pushbackUrl = $pushbackUrl;
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param mixed $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param mixed $publicKey
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return int
     */
    public function getValueTaxPercent()
    {
        return $this->valueTaxPercent;
    }

    /**
     * @param int $valueTaxPercent
     */
    public function setValueTaxPercent($valueTaxPercent)
    {
        $this->valueTaxPercent = $valueTaxPercent;
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->successUrl;
    }

    /**
     * @param string $successUrl
     */
    public function setSuccessUrl($successUrl)
    {
        $this->successUrl = $successUrl;
    }

    /**
     * @return int
     */
    public function getIntegrationTaxPercent()
    {
        return $this->integrationTaxPercent;
    }

    /**
     * @param int $integrationTaxPercent
     * @return Integration
     */
    public function setIntegrationTaxPercent(int $integrationTaxPercent): Integration
    {
        $this->integrationTaxPercent = $integrationTaxPercent;
        return $this;
    }

    public function __toString()
    {
        return ($this->getName()) ? : '';
    }

    /**
     * @return string
     */
    public function getHttpAuthUsername()
    {
        return $this->httpAuthUsername;
    }

    /**
     * @param string $httpAuthUsername
     */
    public function setHttpAuthUsername($httpAuthUsername)
    {
        $this->httpAuthUsername = $httpAuthUsername;
    }

    /**
     * @return string
     */
    public function getHttpAuthPassword()
    {
        return $this->httpAuthPassword;
    }

    /**
     * @param string $httpAuthPassword
     */
    public function setHttpAuthPassword($httpAuthPassword)
    {
        $this->httpAuthPassword = $httpAuthPassword;
    }


    /**
     * Set isDemo
     *
     * @param boolean $isDemo
     *
     * @return Integration
     */
    public function setIsDemo($isDemo)
    {
        $this->isDemo = $isDemo;

        return $this;
    }

    /**
     * Get isDemo
     *
     * @return boolean
     */
    public function getIsDemo()
    {
        return $this->isDemo;
    }

    /**
     * @return bool
     */
    public function isWhitelabel()
    {
        return $this->isWhitelabel;
    }

    /**
     * @param bool $isWhitelabel
     */
    public function setIsWhitelabel(bool $isWhitelabel)
    {
        $this->isWhitelabel = $isWhitelabel;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * @param string $logoUrl
     *
     * @return $this
     */
    public function setLogoUrl(string $logoUrl)
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getHomeUrl()
    {
        return $this->homeUrl;
    }

    /**
     * @param string $homeUrl
     *
     * @return $this
     */
    public function setHomeUrl(string $homeUrl)
    {
        $this->homeUrl = $homeUrl;

        return $this;
    }

    /**
     * Get isWhitelabel
     *
     * @return boolean
     */
    public function getIsWhitelabel()
    {
        return $this->isWhitelabel;
    }
}
