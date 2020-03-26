<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PoEventData
 *
 * @ORM\Table(name="po_event_data")
 * @ORM\Entity
 */
class PoEventData
{
    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", length=255, nullable=true)
     */
    private $fieldName;

    /**
     * @var string
     *
     * @ORM\Column(name="field_type", type="string", nullable=true)
     */
    private $fieldType;

    /**
     * @var string
     *
     * @ORM\Column(name="string_value", type="string", length=255, nullable=true)
     */
    private $stringValue;

    /**
     * @var string
     *
     * @ORM\Column(name="string_value_old", type="string", length=255, nullable=true)
     */
    private $stringValueOld;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_value", type="integer", nullable=true)
     */
    private $intValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="int_value_old", type="integer", nullable=true)
     */
    private $intValueOld;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_value", type="datetime", nullable=true)
     */
    private $dateValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_value_old", type="datetime", nullable=true)
     */
    private $dateValueOld;

    /**
     * @var string
     *
     * @ORM\Column(name="text_value", type="text", length=65535, nullable=true)
     */
    private $textValue;

    /**
     * @var string
     *
     * @ORM\Column(name="text_value_old", type="text", length=65535, nullable=true)
     */
    private $textValueOld;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolean_value", type="boolean", nullable=true)
     */
    private $booleanValue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolean_value_old", type="boolean", nullable=true)
     */
    private $booleanValueOld;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var \Application\Entity\PoEvent
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Application\Entity\PoEvent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private $event;



    /**
     * Set fieldName
     *
     * @param string $fieldName
     *
     * @return PoEventData
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
     * Set fieldType
     *
     * @param string $fieldType
     *
     * @return PoEventData
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
     * Set stringValue
     *
     * @param string $stringValue
     *
     * @return PoEventData
     */
    public function setStringValue($stringValue)
    {
        $this->stringValue = $stringValue;

        return $this;
    }

    /**
     * Get stringValue
     *
     * @return string
     */
    public function getStringValue()
    {
        return $this->stringValue;
    }

    /**
     * Set stringValueOld
     *
     * @param string $stringValueOld
     *
     * @return PoEventData
     */
    public function setStringValueOld($stringValueOld)
    {
        $this->stringValueOld = $stringValueOld;

        return $this;
    }

    /**
     * Get stringValueOld
     *
     * @return string
     */
    public function getStringValueOld()
    {
        return $this->stringValueOld;
    }

    /**
     * Set intValue
     *
     * @param integer $intValue
     *
     * @return PoEventData
     */
    public function setIntValue($intValue)
    {
        $this->intValue = $intValue;

        return $this;
    }

    /**
     * Get intValue
     *
     * @return integer
     */
    public function getIntValue()
    {
        return $this->intValue;
    }

    /**
     * Set intValueOld
     *
     * @param integer $intValueOld
     *
     * @return PoEventData
     */
    public function setIntValueOld($intValueOld)
    {
        $this->intValueOld = $intValueOld;

        return $this;
    }

    /**
     * Get intValueOld
     *
     * @return integer
     */
    public function getIntValueOld()
    {
        return $this->intValueOld;
    }

    /**
     * Set dateValue
     *
     * @param \DateTime $dateValue
     *
     * @return PoEventData
     */
    public function setDateValue($dateValue)
    {
        $this->dateValue = $dateValue;

        return $this;
    }

    /**
     * Get dateValue
     *
     * @return \DateTime
     */
    public function getDateValue()
    {
        return $this->dateValue;
    }

    /**
     * Set dateValueOld
     *
     * @param \DateTime $dateValueOld
     *
     * @return PoEventData
     */
    public function setDateValueOld($dateValueOld)
    {
        $this->dateValueOld = $dateValueOld;

        return $this;
    }

    /**
     * Get dateValueOld
     *
     * @return \DateTime
     */
    public function getDateValueOld()
    {
        return $this->dateValueOld;
    }

    /**
     * Set textValue
     *
     * @param string $textValue
     *
     * @return PoEventData
     */
    public function setTextValue($textValue)
    {
        $this->textValue = $textValue;

        return $this;
    }

    /**
     * Get textValue
     *
     * @return string
     */
    public function getTextValue()
    {
        return $this->textValue;
    }

    /**
     * Set textValueOld
     *
     * @param string $textValueOld
     *
     * @return PoEventData
     */
    public function setTextValueOld($textValueOld)
    {
        $this->textValueOld = $textValueOld;

        return $this;
    }

    /**
     * Get textValueOld
     *
     * @return string
     */
    public function getTextValueOld()
    {
        return $this->textValueOld;
    }

    /**
     * Set booleanValue
     *
     * @param boolean $booleanValue
     *
     * @return PoEventData
     */
    public function setBooleanValue($booleanValue)
    {
        $this->booleanValue = $booleanValue;

        return $this;
    }

    /**
     * Get booleanValue
     *
     * @return boolean
     */
    public function getBooleanValue()
    {
        return $this->booleanValue;
    }

    /**
     * Set booleanValueOld
     *
     * @param boolean $booleanValueOld
     *
     * @return PoEventData
     */
    public function setBooleanValueOld($booleanValueOld)
    {
        $this->booleanValueOld = $booleanValueOld;

        return $this;
    }

    /**
     * Get booleanValueOld
     *
     * @return boolean
     */
    public function getBooleanValueOld()
    {
        return $this->booleanValueOld;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return PoEventData
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
     * Set event
     *
     * @param \Application\Entity\PoEvent $event
     *
     * @return PoEventData
     */
    public function setEvent(\Application\Entity\PoEvent $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Application\Entity\PoEvent
     */
    public function getEvent()
    {
        return $this->event;
    }
}
