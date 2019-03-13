<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemGroup
 *
 * @ORM\Table(name="nmt_inventory_item_group", indexes={@ORM\Index(name="nmt_inventory_item_group_FK1_idx", columns={"inventory_account_id"}), @ORM\Index(name="nmt_inventory_item_group_FK5_idx", columns={"revenue_account_id"}), @ORM\Index(name="nmt_inventory_item_group_FK3_idx", columns={"cogs_account_id"}), @ORM\Index(name="nmt_inventory_item_group_FK4_idx", columns={"allocation_account_id"}), @ORM\Index(name="nmt_inventory_item_group_FK6_idx", columns={"expense_account_id"}), @ORM\Index(name="nmt_inventory_item_group_FK2_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_group_FK7_idx", columns={"cost_center_id"}), @ORM\Index(name="nmt_inventory_item_group_FK8_idx", columns={"default_warehouse_id"})})
 * @ORM\Entity
 */
class NmtInventoryItemGroup
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
     * @ORM\Column(name="group_name", type="string", length=45, nullable=true)
     */
    private $groupName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_account_id", referencedColumnName="id")
     * })
     */
    private $inventoryAccount;

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
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cogs_account_id", referencedColumnName="id")
     * })
     */
    private $cogsAccount;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="allocation_account_id", referencedColumnName="id")
     * })
     */
    private $allocationAccount;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="revenue_account_id", referencedColumnName="id")
     * })
     */
    private $revenueAccount;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="expense_account_id", referencedColumnName="id")
     * })
     */
    private $expenseAccount;

    /**
     * @var \Application\Entity\FinCostCenter
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinCostCenter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cost_center_id", referencedColumnName="id")
     * })
     */
    private $costCenter;

    /**
     * @var \Application\Entity\NmtInventoryWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="default_warehouse_id", referencedColumnName="id")
     * })
     */
    private $defaultWarehouse;



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
     * Set groupName
     *
     * @param string $groupName
     *
     * @return NmtInventoryItemGroup
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryItemGroup
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryItemGroup
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NmtInventoryItemGroup
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
     * Set token
     *
     * @param string $token
     *
     * @return NmtInventoryItemGroup
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
     * Set inventoryAccount
     *
     * @param \Application\Entity\FinAccount $inventoryAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setInventoryAccount(\Application\Entity\FinAccount $inventoryAccount = null)
    {
        $this->inventoryAccount = $inventoryAccount;

        return $this;
    }

    /**
     * Get inventoryAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getInventoryAccount()
    {
        return $this->inventoryAccount;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryItemGroup
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

    /**
     * Set cogsAccount
     *
     * @param \Application\Entity\FinAccount $cogsAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setCogsAccount(\Application\Entity\FinAccount $cogsAccount = null)
    {
        $this->cogsAccount = $cogsAccount;

        return $this;
    }

    /**
     * Get cogsAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getCogsAccount()
    {
        return $this->cogsAccount;
    }

    /**
     * Set allocationAccount
     *
     * @param \Application\Entity\FinAccount $allocationAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setAllocationAccount(\Application\Entity\FinAccount $allocationAccount = null)
    {
        $this->allocationAccount = $allocationAccount;

        return $this;
    }

    /**
     * Get allocationAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getAllocationAccount()
    {
        return $this->allocationAccount;
    }

    /**
     * Set revenueAccount
     *
     * @param \Application\Entity\FinAccount $revenueAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setRevenueAccount(\Application\Entity\FinAccount $revenueAccount = null)
    {
        $this->revenueAccount = $revenueAccount;

        return $this;
    }

    /**
     * Get revenueAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getRevenueAccount()
    {
        return $this->revenueAccount;
    }

    /**
     * Set expenseAccount
     *
     * @param \Application\Entity\FinAccount $expenseAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setExpenseAccount(\Application\Entity\FinAccount $expenseAccount = null)
    {
        $this->expenseAccount = $expenseAccount;

        return $this;
    }

    /**
     * Get expenseAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getExpenseAccount()
    {
        return $this->expenseAccount;
    }

    /**
     * Set costCenter
     *
     * @param \Application\Entity\FinCostCenter $costCenter
     *
     * @return NmtInventoryItemGroup
     */
    public function setCostCenter(\Application\Entity\FinCostCenter $costCenter = null)
    {
        $this->costCenter = $costCenter;

        return $this;
    }

    /**
     * Get costCenter
     *
     * @return \Application\Entity\FinCostCenter
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * Set defaultWarehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $defaultWarehouse
     *
     * @return NmtInventoryItemGroup
     */
    public function setDefaultWarehouse(\Application\Entity\NmtInventoryWarehouse $defaultWarehouse = null)
    {
        $this->defaultWarehouse = $defaultWarehouse;

        return $this;
    }

    /**
     * Get defaultWarehouse
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getDefaultWarehouse()
    {
        return $this->defaultWarehouse;
    }
}
