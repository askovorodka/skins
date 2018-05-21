<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IntegrationReports.
 *
 * @ORM\Table(
 *    name="integration_reports",
 *    indexes={
 *      @ORM\Index(name="user_id", columns={"user_id"}),
 *      @ORM\Index(name="integration_id", columns={"integration_id"}),
 *    }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IntegrationReportsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class IntegrationReports implements \JsonSerializable
{
    const FILE_TYPE_CSV = 'csv';
    const FILE_TYPE_XLSX = 'xlsx';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ ORM\ManyToOne(targetEntity="AppBundle\Entity\Integration")
     */
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Integration")
     * @ORM\JoinColumn(name="integration_id", referencedColumnName="id", nullable=true)
     */
    protected $integration;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $user;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", options={"comment":"date of create record"})
     */
    protected $created;

    /**
     * @var string
     * @ORM\Column(name="file_type", type="string", nullable=true)
     */
    protected $fileType;
    /**
     * @var string
     * @ORM\Column(name="file", type="string", options={"comment":"report file"})
     */
    protected $file;

    /**
     * @var int
     * @ORM\Column(name="file_size", type="integer", options={"comment":"size of file"}, nullable=true)
     */
    protected $fileSize;

    /**
     * @var json_array
     * @ORM\Column(name="filter_params", type="json_array", nullable=true)
     */
    protected $filterParams;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return IntegrationReports
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set fileType.
     *
     * @param string $fileType
     *
     * @return IntegrationReports
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * Get fileType.
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Set file.
     *
     * @param string $file
     *
     * @return IntegrationReports
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set integration.
     *
     * @param \AppBundle\Entity\Integration $integration
     *
     * @return IntegrationReports
     */
    public function setIntegration(\AppBundle\Entity\Integration $integration = null)
    {
        $this->integration = $integration;

        return $this;
    }

    /**
     * Get integration.
     *
     * @return \AppBundle\Entity\Integration
     */
    public function getIntegration()
    {
        return $this->integration;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return IntegrationReports
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set fileSize.
     *
     * @param int $fileSize
     *
     * @return IntegrationReports
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize.
     *
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'integration_id' => $this->integration->getId(),
            'created' => $this->created->format('c'),
            'file' => $this->file,
            'file_size' => $this->fileSize,
            'user' => $this->user->getId(),
        ];
    }

    /**
     * Set filterParams.
     *
     * @param array $filterParams
     *
     * @return IntegrationReports
     */
    public function setFilterParams($filterParams)
    {
        $this->filterParams = $filterParams;

        return $this;
    }

    /**
     * Get filterParams.
     *
     * @return array
     */
    public function getFilterParams()
    {
        return $this->filterParams;
    }
}
