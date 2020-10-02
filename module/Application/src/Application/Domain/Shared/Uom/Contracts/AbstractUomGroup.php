<?php
namespace Application\Domain\Shared\Uom\Contracts;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract  class AbstractUomGroup
{

    protected $baseUom;

    protected $members;

    protected $groupName ='Quantity';


    public function jsonSerialize()
    {}
    /**
     * @return \Application\Domain\Shared\Uom\Uom
     */
    public function getBaseUom()
    {
        return $this->baseUom;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

}
