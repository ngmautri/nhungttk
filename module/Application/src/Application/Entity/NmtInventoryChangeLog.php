<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryChangeLog
 *
 * @ORM\Table(name="nmt_inventory_change_log", indexes={@ORM\Index(name="nmt_inventory_change_log_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_change_log_IDX1", columns={"class_name"}), @ORM\Index(name="nmt_inventory_change_log_IDX2", columns={"field_name"}), @ORM\Index(name="nmt_inventory_change_log_IDX3", columns={"object_id"}), @ORM\Index(name="nmt_inventory_change_log_IDX4", columns={"object_token"}), @ORM\Index(name="nmt_inventory_change_log_FK2_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtInventoryChangeLog
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
     * @ORM\Column(name="object_id", type="integer", nullable=false)
     */
    private $objectId;

    /**
     * @var string
     *
     * @ORM\Column(name="object_token", type="string", length=45, nullable=true)
     */
    private $objectToken;

    /**
     * @var string
     *
     * @ORM\Column(name="class_name", type="string", length=255, nullable=true)
     */
    private $className;

    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", length=100, nullable=true)
     */
    private $fieldName;

    /**
     * @var string
     *
     * @ORM\Column(name="column_name", type="string", length=100, nullable=true)
     */
    private $columnName;

    /**
     * @var string
     *
     * @ORM\Column(name="old_value", type="string", length=255, nullable=true)
     */
    private $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(name="new_value", type="string", length=255, nullable=true)
     */
    private $newValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="field_type", type="string", length=45, nullable=true)
     */
    private $fieldType;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effective_from", type="datetime", nullable=true)
     */
    private $effectiveFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effective_to", type="datetime", nullable=true)
     */
    private $effectiveTo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean", nullable=true)
     */
    private $isValid;

    /**
     * @var string
     *
     * @ORM\Column(name="triggeredBy", type="string", length=255, nullable=true)
     */
    private $triggeredby;

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
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;



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
     * @return NmtInventoryChangeLog
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
     * Set objectId
     *
     * @param integer $objectId
     *
     * @return NmtInventoryChangeLog
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set objectToken
     *
     * @param string $objectToken
     *
     * @return NmtInventoryChangeLog
     */
    public function setObjectToken($objectToken)
    {
        $this->objectToken = $objectToken;

        return $this;
    }

    /**
     * Get objectToken
     *
     * @return string
     */
    public function getObjectToken()
    {
        return $this->objectToken;
    }

    /**
     * Set className
     *
     * @param string $className
     *
     * @return NmtInventoryChangeLog
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set fieldName
     *
     * @param string $fieldName
     *
     * @return NmtInventoryChangeLog
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get fieldName
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Set columnName
     *
     * @param string $columnName
     *
     * @return NmtInventoryChangeLog
     */
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * Get columnName
     *
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     *
     * @return NmtInventoryChangeLog
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get oldValue
     *
     * @return string
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set newValue
     *
     * @param string $newValue
     *
     * @return NmtInventoryChangeLog
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * Get newValue
     *
     * @return string
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryChangeLog
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryChangeLog
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
     * Set fieldType
     *
     * @param string $fieldType
     *
     * @return NmtInventoryChangeLog
     */
    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    /**
     * Get fieldType
     *
     * @return string
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtInventoryChangeLog
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;

        return $this;
    }

    /**
     * Get revisionNo
     *
     * @return integer
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * Set effectiveFrom
     *
     * @param \DateTime $effectiveFrom
     *
     * @return NmtInventoryChangeLog
     */
    public function setEffectiveFrom($effectiveFrom)
    {
        $this->effectiveFrom = $effectiveFrom;

        return $this;
    }

    /**
     * Get effectiveFrom
     *
     * @return \DateTime
     */
    public function getEffectiveFrom()
    {
        return $this->effectiveFrom;
    }

    /**
     * Set effectiveTo
     *
     * @param \DateTime $effectiveTo
     *
     * @return NmtInventoryChangeLog
     */
    public function setEffectiveTo($effectiveTo)
    {
        $this->effectiveTo = $effectiveTo;

        return $this;
    }

    /**
     * Get effectiveTo
     *
     * @return \DateTime
     */
    public function getEffectiveTo()
    {
        return $this->effectiveTo;
    }

    /**
     * Set isValid
     *
     * @param boolean $isValid
     *
     * @return NmtInventoryChangeLog
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get isValid
     *
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set triggeredby
     *
     * @param string $triggeredby
     *
     * @return NmtInventoryChangeLog
     */
    public function setTriggeredby($triggeredby)
    {
        $this->triggeredby = $triggeredby;

        return $this;
    }

    /**
     * Get triggeredby
     *
     * @return string
     */
    public function getTriggeredby()
    {
        return $this->triggeredby;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryChangeLog
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
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtInventoryChangeLog
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }
}
