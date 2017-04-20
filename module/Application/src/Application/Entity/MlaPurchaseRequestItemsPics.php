<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPurchaseRequestItemsPics
 *
 * @ORM\Table(name="mla_purchase_request_items_pics", indexes={@ORM\Index(name="mla_purchase_request_item_pics_FK1_idx", columns={"request_item_id"}), @ORM\Index(name="mla_purchase_request_item_pics_FK2_idx", columns={"uploaded_by"})})
 * @ORM\Entity
 */
class MlaPurchaseRequestItemsPics
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
     * @ORM\Column(name="size", type="string", length=50, nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="filetype", type="string", length=50, nullable=true)
     */
    private $filetype;

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
     * @var \Application\Entity\MlaPurchaseRequestItems
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaPurchaseRequestItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="request_item_id", referencedColumnName="id")
     * })
     */
    private $requestItem;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uploaded_by", referencedColumnName="id")
     * })
     */
    private $uploadedBy;



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
     * @return MlaPurchaseRequestItemsPics
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
     * Set size
     *
     * @param string $size
     *
     * @return MlaPurchaseRequestItemsPics
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
     * Set filetype
     *
     * @param string $filetype
     *
     * @return MlaPurchaseRequestItemsPics
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
     * Set visibility
     *
     * @param boolean $visibility
     *
     * @return MlaPurchaseRequestItemsPics
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
     * @return MlaPurchaseRequestItemsPics
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
     * @return MlaPurchaseRequestItemsPics
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
     * Set requestItem
     *
     * @param \Application\Entity\MlaPurchaseRequestItems $requestItem
     *
     * @return MlaPurchaseRequestItemsPics
     */
    public function setRequestItem(\Application\Entity\MlaPurchaseRequestItems $requestItem = null)
    {
        $this->requestItem = $requestItem;

        return $this;
    }

    /**
     * Get requestItem
     *
     * @return \Application\Entity\MlaPurchaseRequestItems
     */
    public function getRequestItem()
    {
        return $this->requestItem;
    }

    /**
     * Set uploadedBy
     *
     * @param \Application\Entity\MlaUsers $uploadedBy
     *
     * @return MlaPurchaseRequestItemsPics
     */
    public function setUploadedBy(\Application\Entity\MlaUsers $uploadedBy = null)
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    /**
     * Get uploadedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getUploadedBy()
    {
        return $this->uploadedBy;
    }
}
