<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemGroup
 *
 * @ORM\Table(name="nmt_inventory_item_group")
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
     * @var integer
     *
     * @ORM\Column(name="inventory_account", type="integer", nullable=true)
     */
    private $inventoryAccount;

    /**
     * @var integer
     *
     * @ORM\Column(name="cogs_account", type="integer", nullable=true)
     */
    private $cogsAccount;

    /**
     * @var integer
     *
     * @ORM\Column(name="allocation_account", type="integer", nullable=true)
     */
    private $allocationAccount;

    /**
     * @var integer
     *
     * @ORM\Column(name="revenue_account", type="integer", nullable=true)
     */
    private $revenueAccount;

    /**
     * @var integer
     *
     * @ORM\Column(name="expense_account", type="integer", nullable=true)
     */
    private $expenseAccount;



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
     * Set inventoryAccount
     *
     * @param integer $inventoryAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setInventoryAccount($inventoryAccount)
    {
        $this->inventoryAccount = $inventoryAccount;

        return $this;
    }

    /**
     * Get inventoryAccount
     *
     * @return integer
     */
    public function getInventoryAccount()
    {
        return $this->inventoryAccount;
    }

    /**
     * Set cogsAccount
     *
     * @param integer $cogsAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setCogsAccount($cogsAccount)
    {
        $this->cogsAccount = $cogsAccount;

        return $this;
    }

    /**
     * Get cogsAccount
     *
     * @return integer
     */
    public function getCogsAccount()
    {
        return $this->cogsAccount;
    }

    /**
     * Set allocationAccount
     *
     * @param integer $allocationAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setAllocationAccount($allocationAccount)
    {
        $this->allocationAccount = $allocationAccount;

        return $this;
    }

    /**
     * Get allocationAccount
     *
     * @return integer
     */
    public function getAllocationAccount()
    {
        return $this->allocationAccount;
    }

    /**
     * Set revenueAccount
     *
     * @param integer $revenueAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setRevenueAccount($revenueAccount)
    {
        $this->revenueAccount = $revenueAccount;

        return $this;
    }

    /**
     * Get revenueAccount
     *
     * @return integer
     */
    public function getRevenueAccount()
    {
        return $this->revenueAccount;
    }

    /**
     * Set expenseAccount
     *
     * @param integer $expenseAccount
     *
     * @return NmtInventoryItemGroup
     */
    public function setExpenseAccount($expenseAccount)
    {
        $this->expenseAccount = $expenseAccount;

        return $this;
    }

    /**
     * Get expenseAccount
     *
     * @return integer
     */
    public function getExpenseAccount()
    {
        return $this->expenseAccount;
    }
}
