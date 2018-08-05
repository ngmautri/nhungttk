<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryOpeningBalanceRow
 *
 * @ORM\Table(name="nmt_inventory_opening_balance_row", indexes={@ORM\Index(name="nmt_inventory_opening_balance_row_FK1_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_opening_balance_row_FK2_idx", columns={"opening_balance_id"}), @ORM\Index(name="nmt_inventory_opening_balance_row_KF3_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_opening_balance_row_FK4_idx", columns={"GL_account_id"})})
 * @ORM\Entity
 */
class NmtInventoryOpeningBalanceRow
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
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantiy", type="integer", nullable=true)
     */
    private $quantiy;

    /**
     * @var integer
     *
     * @ORM\Column(name="unit_price", type="integer", nullable=true)
     */
    private $unitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="gross_amount", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $grossAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="net_amount", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $netAmount;

    /**
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

    /**
     * @var \Application\Entity\NmtInventoryOpeningBalance
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryOpeningBalance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opening_balance_id", referencedColumnName="id")
     * })
     */
    private $openingBalance;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="GL_account_id", referencedColumnName="id")
     * })
     */
    private $glAccount;

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
     * Set token
     *
     * @param string $token
     *
     * @return NmtInventoryOpeningBalanceRow
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set quantiy
     *
     * @param integer $quantiy
     *
     * @return NmtInventoryOpeningBalanceRow
     */
    public function setQuantiy($quantiy)
    {
        $this->quantiy = $quantiy;

        return $this;
    }

    /**
     * Get quantiy
     *
     * @return integer
     */
    public function getQuantiy()
    {
        return $this->quantiy;
    }

    /**
     * Set unitPrice
     *
     * @param integer $unitPrice
     *
     * @return NmtInventoryOpeningBalanceRow
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return integer
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * Set grossAmount
     *
     * @param string $grossAmount
     *
     * @return NmtInventoryOpeningBalanceRow
     */
    public function setGrossAmount($grossAmount)
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    /**
     * Get grossAmount
     *
     * @return string
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryOpeningBalanceRow
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
     * Set netAmount
     *
     * @param string $netAmount
     *
     * @return NmtInventoryOpeningBalanceRow
     */
    public function setNetAmount($netAmount)
    {
        $this->netAmount = $netAmount;

        return $this;
    }

    /**
     * Get netAmount
     *
     * @return string
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryOpeningBalanceRow
     */
    public function setItem(\Application\Entity\NmtInventoryItem $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \Application\Entity\NmtInventoryItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set openingBalance
     *
     * @param \Application\Entity\NmtInventoryOpeningBalance $openingBalance
     *
     * @return NmtInventoryOpeningBalanceRow
     */
    public function setOpeningBalance(\Application\Entity\NmtInventoryOpeningBalance $openingBalance = null)
    {
        $this->openingBalance = $openingBalance;

        return $this;
    }

    /**
     * Get openingBalance
     *
     * @return \Application\Entity\NmtInventoryOpeningBalance
     */
    public function getOpeningBalance()
    {
        return $this->openingBalance;
    }

    /**
     * Set glAccount
     *
     * @param \Application\Entity\FinAccount $glAccount
     *
     * @return NmtInventoryOpeningBalanceRow
     */
    public function setGlAccount(\Application\Entity\FinAccount $glAccount = null)
    {
        $this->glAccount = $glAccount;

        return $this;
    }

    /**
     * Get glAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryOpeningBalanceRow
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
