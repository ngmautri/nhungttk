<?php
namespace Inventory\Model\Evaluation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class StockValation
{

    private $receivingList;

    private $valuationMethod;

    /**
     *
     * @return mixed
     */
    public function getReceivingList()
    {
        return $this->receivingList;
    }

    /**
     *
     * @return mixed
     */
    public function getValuationMethod()
    {
        return $this->valuationMethod;
    }

    /**
     *
     * @param mixed $receivingList
     */
    public function setReceivingList($receivingList)
    {
        $this->receivingList = $receivingList;
    }

    /**
     *
     * @param mixed $valuationMethod
     */
    public function setValuationMethod($valuationMethod)
    {
        $this->valuationMethod = $valuationMethod;
    }
}