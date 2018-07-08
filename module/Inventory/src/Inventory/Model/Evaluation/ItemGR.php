<?php

namespace Inventory\Model;


/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemGr
{
    protected $grDate;
    protected $grQuantity;
    protected $issueQuantity;
    protected $remainingQuantity;
    /**
     * @return mixed
     */
    public function getGrDate()
    {
        return $this->grDate;
    }

    /**
     * @return mixed
     */
    public function getGrQuantity()
    {
        return $this->grQuantity;
    }

    /**
     * @return mixed
     */
    public function getIssueQuantity()
    {
        return $this->issueQuantity;
    }

    /**
     * @return mixed
     */
    public function getRemainingQuantity()
    {
        return $this->remainingQuantity;
    }

    /**
     * @param mixed $grDate
     */
    public function setGrDate($grDate)
    {
        $this->grDate = $grDate;
    }

    /**
     * @param mixed $grQuantity
     */
    public function setGrQuantity($grQuantity)
    {
        $this->grQuantity = $grQuantity;
    }

    /**
     * @param mixed $issueQuantity
     */
    public function setIssueQuantity($issueQuantity)
    {
        $this->issueQuantity = $issueQuantity;
    }

    /**
     * @param mixed $remainingQuantity
     */
    public function setRemainingQuantity($remainingQuantity)
    {
        $this->remainingQuantity = $remainingQuantity;
    }

    
       
}