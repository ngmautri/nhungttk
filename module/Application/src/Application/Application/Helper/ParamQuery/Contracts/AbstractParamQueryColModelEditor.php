<?php
namespace Application\Application\Helper\ParamQuery\Contracts;

/**
 * Abstract ParamQuery
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractParamQueryColModelEditor
{

    const textbox = 'textbox';

    const textarea = 'textarea';

    const contenteditable = 'contenteditable';

    protected $type;

    protected $init;

    protected $prepend;

    protected $options;

    protected $labelIndx;

    protected $valueIndx;

    protected $groupIndx;

    protected $dataMap;

    protected $mapIndices;

    protected $getData;

    protected $cls;

    protected $style;

    protected $attr;

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
    public function getInit()
    {
        return $this->init;
    }

    /**
     *
     * @param mixed $init
     */
    public function setInit($init)
    {
        $this->init = $init;
    }

    /**
     *
     * @return mixed
     */
    public function getPrepend()
    {
        return $this->prepend;
    }

    /**
     *
     * @param mixed $prepend
     */
    public function setPrepend($prepend)
    {
        $this->prepend = $prepend;
    }

    /**
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     *
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     *
     * @return mixed
     */
    public function getLabelIndx()
    {
        return $this->labelIndx;
    }

    /**
     *
     * @param mixed $labelIndx
     */
    public function setLabelIndx($labelIndx)
    {
        $this->labelIndx = $labelIndx;
    }

    /**
     *
     * @return mixed
     */
    public function getValueIndx()
    {
        return $this->valueIndx;
    }

    /**
     *
     * @param mixed $valueIndx
     */
    public function setValueIndx($valueIndx)
    {
        $this->valueIndx = $valueIndx;
    }

    /**
     *
     * @return mixed
     */
    public function getGroupIndx()
    {
        return $this->groupIndx;
    }

    /**
     *
     * @param mixed $groupIndx
     */
    public function setGroupIndx($groupIndx)
    {
        $this->groupIndx = $groupIndx;
    }

    /**
     *
     * @return mixed
     */
    public function getDataMap()
    {
        return $this->dataMap;
    }

    /**
     *
     * @param mixed $dataMap
     */
    public function setDataMap($dataMap)
    {
        $this->dataMap = $dataMap;
    }

    /**
     *
     * @return mixed
     */
    public function getMapIndices()
    {
        return $this->mapIndices;
    }

    /**
     *
     * @param mixed $mapIndices
     */
    public function setMapIndices($mapIndices)
    {
        $this->mapIndices = $mapIndices;
    }

    /**
     *
     * @return mixed
     */
    public function getGetData()
    {
        return $this->getData;
    }

    /**
     *
     * @param mixed $getData
     */
    public function setGetData($getData)
    {
        $this->getData = $getData;
    }

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
    public function getStyle()
    {
        return $this->style;
    }

    /**
     *
     * @param mixed $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     *
     * @return mixed
     */
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     *
     * @param mixed $attr
     */
    public function setAttr($attr)
    {
        $this->attr = $attr;
    }
}
