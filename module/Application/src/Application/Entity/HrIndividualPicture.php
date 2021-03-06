<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrIndividualPicture
 *
 * @ORM\Table(name="hr_individual_picture", indexes={@ORM\Index(name="hr_individual_picture_FK1_idx", columns={"individual_id"})})
 * @ORM\Entity
 */
class HrIndividualPicture
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="filetype", type="string", length=45, nullable=true)
     */
    private $filetype;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=50, nullable=true)
     */
    private $size;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visibility", type="boolean", nullable=true)
     */
    private $visibility;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    private $comments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uploaded_on", type="datetime", nullable=true)
     */
    private $uploadedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=200, nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="folder", type="string", length=255, nullable=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=100, nullable=true)
     */
    private $checksum;

    /**
     * @var \Application\Entity\HrIndividual
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\HrIndividual")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="individual_id", referencedColumnName="id")
     * })
     */
    private $individual;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return HrIndividualPicture
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set filetype
     *
     * @param string $filetype
     *
     * @return HrIndividualPicture
     */
    public function setFiletype($filetype)
    {
        $this->filetype = $filetype;

        return $this;
    }

    /**
     * Get filetype
     *
     * @return string
     */
    public function getFiletype()
    {
        return $this->filetype;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return HrIndividualPicture
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set visibility
     *
     * @param boolean $visibility
     *
     * @return HrIndividualPicture
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return boolean
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return HrIndividualPicture
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set uploadedOn
     *
     * @param \DateTime $uploadedOn
     *
     * @return HrIndividualPicture
     */
    public function setUploadedOn($uploadedOn)
    {
        $this->uploadedOn = $uploadedOn;

        return $this;
    }

    /**
     * Get uploadedOn
     *
     * @return \DateTime
     */
    public function getUploadedOn()
    {
        return $this->uploadedOn;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return HrIndividualPicture
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set folder
     *
     * @param string $folder
     *
     * @return HrIndividualPicture
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     *
     * @return HrIndividualPicture
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set individual
     *
     * @param \Application\Entity\HrIndividual $individual
     *
     * @return HrIndividualPicture
     */
    public function setIndividual(\Application\Entity\HrIndividual $individual = null)
    {
        $this->individual = $individual;

        return $this;
    }

    /**
     * Get individual
     *
     * @return \Application\Entity\HrIndividual
     */
    public function getIndividual()
    {
        return $this->individual;
    }
}
