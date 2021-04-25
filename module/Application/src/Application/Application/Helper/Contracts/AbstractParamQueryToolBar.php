<?php
namespace Application\Application\Helper\Contracts;

/**
 * Abstract ParamQuery
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractParamQueryToolBar
{

    protected $cls;

    protected $items;

    /**
     *
     * @return mixed
     */
    public function getCls()
    {
        return $this->cls;
    }

    /**
     *
     * @param mixed $cls
     */
    public function setCls($cls)
    {
        $this->cls = $cls;
    }

    /**
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     *
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}
