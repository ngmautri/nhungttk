<?php
namespace Application\Application\DTO\Common;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 * @var \Application\Entity\NmtApplicationCompany ;
 */
class FormOptionDTO
{

    public $value;

    public $name;

    public $otherName;

    public $contextObject;

    /**
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return mixed
     */
    public function getContextObject()
    {
        return $this->contextObject;
    }

    /**
     *
     * @param mixed $contextObject
     */
    public function setContextObject($contextObject)
    {
        $this->contextObject = $contextObject;
    }

    /**
     *
     * @return mixed
     */
    public function getOtherName()
    {
        return $this->otherName;
    }

    /**
     *
     * @param mixed $otherName
     */
    public function setOtherName($otherName)
    {
        $this->otherName = $otherName;
    }
}
