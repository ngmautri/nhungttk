<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPaymentMethod
 *
 * @ORM\Table(name="mla_payment_method")
 * @ORM\Entity
 */
class MlaPaymentMethod
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
     * @ORM\Column(name="method", type="string", length=100, nullable=true)
     */
    private $method;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_last_status", type="string", length=45, nullable=true)
     */
    private $employeeLastStatus;



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
     * Set method
     *
     * @param string $method
     *
     * @return MlaPaymentMethod
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return MlaPaymentMethod
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MlaPaymentMethod
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
     * Set employeeLastStatus
     *
     * @param string $employeeLastStatus
     *
     * @return MlaPaymentMethod
     */
    public function setEmployeeLastStatus($employeeLastStatus)
    {
        $this->employeeLastStatus = $employeeLastStatus;

        return $this;
    }

    /**
     * Get employeeLastStatus
     *
     * @return string
     */
    public function getEmployeeLastStatus()
    {
        return $this->employeeLastStatus;
    }
}
