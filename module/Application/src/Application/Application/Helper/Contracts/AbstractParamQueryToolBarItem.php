<?php
namespace Application\Application\Helper\Contracts;

/**
 * Abstract ParamQuery
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractParamQueryToolBarItem
{

    protected $type;

    protected $label;

    protected $icon;

    protected $listens;

    /**
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     *
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     *
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     *
     * @return mixed
     */
    public function getListens()
    {
        return $this->listens;
    }

    /**
     *
     * @param mixed $listens
     */
    public function setListens($listens)
    {
        $this->listens = $listens;
    }
}
