<?php
namespace Inventory\Application\DTO\Item\Report;

use Inventory\Application\DTO\Item\ItemDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemInOutOnhandDTO extends ItemDTO
{

    public $beginQty;

    public $beginValue;

    public $grQty;

    public $grValue;

    public $giQty;

    public $giValue;

    public $endQty;

    public $endValue;

    /**
     *
     * @return mixed
     */
    public function getBeginQty()
    {
        return $this->beginQty;
    }

    /**
     *
     * @return mixed
     */
    public function getBeginValue()
    {
        return $this->beginValue;
    }

    /**
     *
     * @return mixed
     */
    public function getGrQty()
    {
        return $this->grQty;
    }

    /**
     *
     * @return mixed
     */
    public function getGrValue()
    {
        return $this->grValue;
    }

    /**
     *
     * @return mixed
     */
    public function getGiQty()
    {
        return $this->giQty;
    }

    /**
     *
     * @return mixed
     */
    public function getGiValue()
    {
        return $this->giValue;
    }

    /**
     *
     * @return mixed
     */
    public function getEndQty()
    {
        return $this->endQty;
    }

    /**
     *
     * @return mixed
     */
    public function getEndValue()
    {
        return $this->endValue;
    }

    /**
     *
     * @param mixed $beginQty
     */
    public function setBeginQty($beginQty)
    {
        $this->beginQty = $beginQty;
    }

    /**
     *
     * @param mixed $beginValue
     */
    public function setBeginValue($beginValue)
    {
        $this->beginValue = $beginValue;
    }

    /**
     *
     * @param mixed $grQty
     */
    public function setGrQty($grQty)
    {
        $this->grQty = $grQty;
    }

    /**
     *
     * @param mixed $grValue
     */
    public function setGrValue($grValue)
    {
        $this->grValue = $grValue;
    }

    /**
     *
     * @param mixed $giQty
     */
    public function setGiQty($giQty)
    {
        $this->giQty = $giQty;
    }

    /**
     *
     * @param mixed $giValue
     */
    public function setGiValue($giValue)
    {
        $this->giValue = $giValue;
    }

    /**
     *
     * @param mixed $endQty
     */
    public function setEndQty($endQty)
    {
        $this->endQty = $endQty;
    }

    /**
     *
     * @param mixed $endValue
     */
    public function setEndValue($endValue)
    {
        $this->endValue = $endValue;
    }
}
