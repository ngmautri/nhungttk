<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaArticlesPurchasing
 *
 * @ORM\Table(name="mla_articles_purchasing", indexes={@ORM\Index(name="mla_articles_purchasing_FK1_idx", columns={"article_id"}), @ORM\Index(name="mla_articles_purchasing_FK2_idx", columns={"vendor_id"}), @ORM\Index(name="mla_articles_purchasing_FK2_idx1", columns={"created_by"})})
 * @ORM\Entity
 */
class MlaArticlesPurchasing
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
     * @ORM\Column(name="vendor_article_code", type="string", length=100, nullable=true)
     */
    private $vendorArticleCode;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_unit", type="string", length=45, nullable=true)
     */
    private $vendorUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $vendorUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=45, nullable=true)
     */
    private $currency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="price_valid_from", type="datetime", nullable=true)
     */
    private $priceValidFrom;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_preferred", type="boolean", nullable=true)
     */
    private $isPreferred;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="conversion_factor", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $conversionFactor;

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
     * @var \Application\Entity\MlaVendors
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaVendors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor;

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
     * Set vendorArticleCode
     *
     * @param string $vendorArticleCode
     *
     * @return MlaArticlesPurchasing
     */
    public function setVendorArticleCode($vendorArticleCode)
    {
        $this->vendorArticleCode = $vendorArticleCode;

        return $this;
    }

    /**
     * Get vendorArticleCode
     *
     * @return string
     */
    public function getVendorArticleCode()
    {
        return $this->vendorArticleCode;
    }

    /**
     * Set vendorUnit
     *
     * @param string $vendorUnit
     *
     * @return MlaArticlesPurchasing
     */
    public function setVendorUnit($vendorUnit)
    {
        $this->vendorUnit = $vendorUnit;

        return $this;
    }

    /**
     * Get vendorUnit
     *
     * @return string
     */
    public function getVendorUnit()
    {
        return $this->vendorUnit;
    }

    /**
     * Set vendorUnitPrice
     *
     * @param string $vendorUnitPrice
     *
     * @return MlaArticlesPurchasing
     */
    public function setVendorUnitPrice($vendorUnitPrice)
    {
        $this->vendorUnitPrice = $vendorUnitPrice;

        return $this;
    }

    /**
     * Get vendorUnitPrice
     *
     * @return string
     */
    public function getVendorUnitPrice()
    {
        return $this->vendorUnitPrice;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return MlaArticlesPurchasing
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set priceValidFrom
     *
     * @param \DateTime $priceValidFrom
     *
     * @return MlaArticlesPurchasing
     */
    public function setPriceValidFrom($priceValidFrom)
    {
        $this->priceValidFrom = $priceValidFrom;

        return $this;
    }

    /**
     * Get priceValidFrom
     *
     * @return \DateTime
     */
    public function getPriceValidFrom()
    {
        return $this->priceValidFrom;
    }

    /**
     * Set isPreferred
     *
     * @param boolean $isPreferred
     *
     * @return MlaArticlesPurchasing
     */
    public function setIsPreferred($isPreferred)
    {
        $this->isPreferred = $isPreferred;

        return $this;
    }

    /**
     * Get isPreferred
     *
     * @return boolean
     */
    public function getIsPreferred()
    {
        return $this->isPreferred;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MlaArticlesPurchasing
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
     * Set conversionFactor
     *
     * @param string $conversionFactor
     *
     * @return MlaArticlesPurchasing
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;

        return $this;
    }

    /**
     * Get conversionFactor
     *
     * @return string
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     * Set article
     *
     * @param \Application\Entity\MlaArticles $article
     *
     * @return MlaArticlesPurchasing
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
     * Set vendor
     *
     * @param \Application\Entity\MlaVendors $vendor
     *
     * @return MlaArticlesPurchasing
     */
    public function setVendor(\Application\Entity\MlaVendors $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \Application\Entity\MlaVendors
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return MlaArticlesPurchasing
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
