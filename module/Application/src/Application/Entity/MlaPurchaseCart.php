<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPurchaseCart
 *
 * @ORM\Table(name="mla_purchase_cart", indexes={@ORM\Index(name="mla_purchase_cart_FK2_idx", columns={"asset_id"}), @ORM\Index(name="mla_purchase_cart_FK1_idx", columns={"sparepart_id"}), @ORM\Index(name="mla_purchase_cart_FK3_idx", columns={"article_id"}), @ORM\Index(name="mla_purchase_cart_FK4_idx1", columns={"created_by"})})
 * @ORM\Entity
 */
class MlaPurchaseCart
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
     * @ORM\Column(name="priority", type="string", length=45, nullable=true)
     */
    private $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=150, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=45, nullable=true)
     */
    private $unit;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", precision=10, scale=0, nullable=false)
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EDT", type="datetime", nullable=true)
     */
    private $edt;

    /**
     * @var integer
     *
     * @ORM\Column(name="other_res_id", type="integer", nullable=true)
     */
    private $otherResId;

    /**
     * @var string
     *
     * @ORM\Column(name="asset_name", type="string", length=255, nullable=true)
     */
    private $assetName;

    /**
     * @var string
     *
     * @ORM\Column(name="purpose", type="text", length=65535, nullable=true)
     */
    private $purpose;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var \Application\Entity\MlaSpareparts
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaSpareparts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sparepart_id", referencedColumnName="id")
     * })
     */
    private $sparepart;

    /**
     * @var \Application\Entity\MlaAsset
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaAsset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     * })
     */
    private $asset;

    /**
     * @var \Application\Entity\MlaArticles
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     * })
     */
    private $article;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;



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
     * Set priority
     *
     * @param string $priority
     *
     * @return MlaPurchaseCart
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MlaPurchaseCart
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MlaPurchaseCart
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return MlaPurchaseCart
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return MlaPurchaseCart
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set unit
     *
     * @param string $unit
     *
     * @return MlaPurchaseCart
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set quantity
     *
     * @param float $quantity
     *
     * @return MlaPurchaseCart
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set edt
     *
     * @param \DateTime $edt
     *
     * @return MlaPurchaseCart
     */
    public function setEdt($edt)
    {
        $this->edt = $edt;

        return $this;
    }

    /**
     * Get edt
     *
     * @return \DateTime
     */
    public function getEdt()
    {
        return $this->edt;
    }

    /**
     * Set otherResId
     *
     * @param integer $otherResId
     *
     * @return MlaPurchaseCart
     */
    public function setOtherResId($otherResId)
    {
        $this->otherResId = $otherResId;

        return $this;
    }

    /**
     * Get otherResId
     *
     * @return integer
     */
    public function getOtherResId()
    {
        return $this->otherResId;
    }

    /**
     * Set assetName
     *
     * @param string $assetName
     *
     * @return MlaPurchaseCart
     */
    public function setAssetName($assetName)
    {
        $this->assetName = $assetName;

        return $this;
    }

    /**
     * Get assetName
     *
     * @return string
     */
    public function getAssetName()
    {
        return $this->assetName;
    }

    /**
     * Set purpose
     *
     * @param string $purpose
     *
     * @return MlaPurchaseCart
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * Get purpose
     *
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return MlaPurchaseCart
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MlaPurchaseCart
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return MlaPurchaseCart
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set sparepart
     *
     * @param \Application\Entity\MlaSpareparts $sparepart
     *
     * @return MlaPurchaseCart
     */
    public function setSparepart(\Application\Entity\MlaSpareparts $sparepart = null)
    {
        $this->sparepart = $sparepart;

        return $this;
    }

    /**
     * Get sparepart
     *
     * @return \Application\Entity\MlaSpareparts
     */
    public function getSparepart()
    {
        return $this->sparepart;
    }

    /**
     * Set asset
     *
     * @param \Application\Entity\MlaAsset $asset
     *
     * @return MlaPurchaseCart
     */
    public function setAsset(\Application\Entity\MlaAsset $asset = null)
    {
        $this->asset = $asset;

        return $this;
    }

    /**
     * Get asset
     *
     * @return \Application\Entity\MlaAsset
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * Set article
     *
     * @param \Application\Entity\MlaArticles $article
     *
     * @return MlaPurchaseCart
     */
    public function setArticle(\Application\Entity\MlaArticles $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \Application\Entity\MlaArticles
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return MlaPurchaseCart
     */
    public function setCreatedBy(\Application\Entity\MlaUsers $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
