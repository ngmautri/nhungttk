<?php
namespace Application\Application\Helper\ParamQuery\Contracts;

/**
 * Abstract ParamQuery
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractParamQueryColModel
{

    const RIGHT = 'right';

    const LEFT = 'left';

    const CENTER = 'center';

    const STRING = 'string';

    const INTEGER = 'integer';

    const DATE = 'date';

    const FLOAT = 'float';

    protected $align;

    protected $cb;

    protected $cls;

    protected $colModel;

    protected $copy;

    protected $dataIndx;

    protected $dataType;

    protected $editable;

    protected $editModel;

    protected $editor;

    protected $filter;

    protected $halign;

    protected $hidden;

    protected $maxWidth;

    protected $minWidth;

    protected $render;

    protected $resizable;

    protected $sortable;

    protected $sortType;

    protected $summary;

    protected $title;

    protected $type;

    protected $validations;

    protected $width;

    /**
     *
     * @return mixed
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     *
     * @param mixed $align
     */
    public function setAlign($align)
    {
        $this->align = \sprintf("align: \"%s\"", $align);
        return $this->align;
    }

    /**
     *
     * @return mixed
     */
    public function getCb()
    {
        return $this->cb;
    }

    /**
     *
     * @param mixed $cb
     */
    public function setCb($cb)
    {
        $this->cb = $cb;
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
        $this->cls = \sprintf("cls: \'%s\'", $cls);
        return $this->cls;
    }

    /**
     *
     * @return mixed
     */
    public function getColModel()
    {
        return $this->colModel;
    }

    /**
     *
     * @param mixed $colModel
     */
    public function setColModel($colModel)
    {
        $this->colModel = $colModel;
    }

    /**
     *
     * @return mixed
     */
    public function getCopy()
    {
        return $this->copy;
    }

    /**
     *
     * @param mixed $copy
     */
    public function setCopy($copy)
    {
        $this->copy = $copy;
    }

    /**
     *
     * @return mixed
     */
    public function getDataIndx()
    {
        return $this->dataIndx;
    }

    /**
     *
     * @param mixed $dataIndx
     */
    public function setDataIndx($dataIndx)
    {
        $this->dataIndx = \sprintf("dataIndx: \"%s\"", $dataIndx);
        return $this->dataIndx;
    }

    /**
     *
     * @return mixed
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     *
     * @param mixed $dataType
     */
    public function setDataType($dataType)
    {
        $this->dataType = \sprintf("dataType: \"%s\"", $dataType);
        return $this->dataType;
    }

    /**
     *
     * @return mixed
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     *
     * @param mixed $editable
     */
    public function setEditable($editable)
    {
        $this->editable = \sprintf("editable: %s", $this->_convertBoolean($editable));
        return $this->editable;
    }

    /**
     *
     * @return mixed
     */
    public function getEditModel()
    {
        return $this->editModel;
    }

    /**
     *
     * @param mixed $editModel
     */
    public function setEditModel($editModel)
    {
        $this->editModel = $editModel;
    }

    /**
     *
     * @return mixed
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     *
     * @param mixed $editor
     */
    public function setEditor($editor)
    {
        $this->editor = $editor;
    }

    /**
     *
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     *
     * @param mixed $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     *
     * @return mixed
     */
    public function getHalign()
    {
        return $this->halign;
    }

    /**
     *
     * @param mixed $halign
     */
    public function setHalign($halign)
    {
        $this->halign = $halign;
    }

    /**
     *
     * @return mixed
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     *
     * @param mixed $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     *
     * @return mixed
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     *
     * @param mixed $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = \sprintf("maxWidth: %s", $maxWidth);
        return $this->maxWidth;
    }

    /**
     *
     * @return mixed
     */
    public function getMinWidth()
    {
        return $this->minWidth;
    }

    /**
     *
     * @param mixed $minWidth
     */
    public function setMinWidth($minWidth)
    {
        $this->minWidth = \sprintf("minWidth: %s", $minWidth);
        return $this->minWidth;
    }

    /**
     *
     * @return mixed
     */
    public function getRender()
    {
        return $this->render;
    }

    /**
     *
     * @param mixed $render
     */
    public function setRender($render)
    {
        $this->render = \sprintf("render: %s", $render);
        return $this->render;
    }

    /**
     *
     * @return mixed
     */
    public function getResizable()
    {
        return $this->resizable;
    }

    /**
     *
     * @param mixed $resizable
     */
    public function setResizable($resizable)
    {
        $this->resizable = $resizable;
    }

    /**
     *
     * @return mixed
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     *
     * @param mixed $sortable
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;
    }

    /**
     *
     * @return mixed
     */
    public function getSortType()
    {
        return $this->sortType;
    }

    /**
     *
     * @param mixed $sortType
     */
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;
    }

    /**
     *
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     *
     * @param mixed $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = \sprintf("title: \"%s\"", $title);
        return $this->title;
    }

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
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     *
     * @param mixed $validations
     */
    public function setValidations($validations)
    {
        $this->validations = $validations;
    }

    /**
     *
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     *
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = \sprintf("width: %s", $width);
        return $this->width;
    }

    private function _convertBoolean($v)
    {
        if ($v == true) {
            return 'true';
        }
        return 'false';
    }
}
