<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryTrxType
 *
 * @ORM\Table(name="nmt_inventory_trx_type", indexes={@ORM\Index(name="nmt_inventory_movement_type_FK1_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtInventoryTrxType
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
     * @ORM\Column(name="movement_flow", type="string", nullable=true)
     */
    private $movementFlow;

    /**
     * @var string
     *
     * @ORM\Column(name="movement_code", type="string", length=45, nullable=true)
     */
    private $movementCode;

    /**
     * @var string
     *
     * @ORM\Column(name="movement_name", type="string", length=100, nullable=true)
     */
    private $movementName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

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
     * Set movementFlow
     *
     * @param string $movementFlow
     *
     * @return NmtInventoryTrxType
     */
    public function setMovementFlow($movementFlow)
    {
        $this->movementFlow = $movementFlow;

        return $this;
    }

    /**
     * Get movementFlow
     *
     * @return string
     */
    public function getMovementFlow()
    {
        return $this->movementFlow;
    }

    /**
     * Set movementCode
     *
     * @param string $movementCode
     *
     * @return NmtInventoryTrxType
     */
    public function setMovementCode($movementCode)
    {
        $this->movementCode = $movementCode;

        return $this;
    }

    /**
     * Get movementCode
     *
     * @return string
     */
    public function getMovementCode()
    {
        return $this->movementCode;
    }

    /**
     * Set movementName
     *
     * @param string $movementName
     *
     * @return NmtInventoryTrxType
     */
    public function setMovementName($movementName)
    {
        $this->movementName = $movementName;

        return $this;
    }

    /**
     * Get movementName
     *
     * @return string
     */
    public function getMovementName()
    {
        return $this->movementName;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryTrxType
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryTrxType
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
