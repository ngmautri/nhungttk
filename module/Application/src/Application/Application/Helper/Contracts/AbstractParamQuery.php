<?php
namespace Application\Application\Helper\Contracts;

/**
 * Abstract ParamQuery
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractParamQuery
{

    protected $collapsible;

    protected $colModel;

    protected $columnBorders;

    protected $columnTemplate;

    protected $dataModel;

    protected $detailModel;

    protected $dragColumns;

    protected $draggable;

    protected $editable;

    protected $editModel;

    protected $editor;

    protected $filterModel;

    protected $flexHeight;

    protected $flexWidth;

    protected $freezeCols;

    protected $freezeRows;

    protected $groupModel;

    protected $height;

    protected $historyModel;

    protected $hoverMode;

    protected $hwrap;

    protected $minWidth;

    protected $numberCell;

    protected $pageModel;

    protected $pasteModel;

    protected $resizable;

    protected $roundCorners;

    protected $rowBorders;

    protected $scrollModel;

    protected $selectionModel;

    protected $showBottom;

    protected $showHeader;

    protected $showTitle;

    protected $showTop;

    protected $showToolbar;

    protected $sortable;

    protected $stringify;

    protected $stripeRows;

    protected $swipeModel;

    protected $title;

    protected $toolbar;

    protected $trackModel;

    protected $validation;

    protected $virtualX;

    protected $virtualXHeader;

    protected $virtualY;

    protected $warning;

    protected $width;

    protected $wrap;

    /**
     *
     * @return mixed
     */
    public function getCollapsible()
    {
        return $this->collapsible;
    }

    /**
     *
     * @param mixed $collapsible
     */
    public function setCollapsible($collapsible)
    {
        $this->collapsible = \sprintf("collapsible: %s", $this->_convertBoolean($collapsible));
        return $this->collapsible;
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
        $this->colModel = \sprintf("colModel: %s", $colModel);
        return $this->colModel;
    }

    /**
     *
     * @return mixed
     */
    public function getColumnBorders()
    {
        return $this->columnBorders;
    }

    /**
     *
     * @param mixed $columnBorders
     */
    public function setColumnBorders($columnBorders)
    {
        $this->columnBorders = \sprintf("columnBorders: %s", $this->_convertBoolean($columnBorders));
        return $this->columnBorders;
    }

    /**
     *
     * @return mixed
     */
    public function getColumnTemplate()
    {
        return $this->columnTemplate;
    }

    /**
     *
     * @param mixed $columnTemplate
     */
    public function setColumnTemplate($columnTemplate)
    {
        $this->columnTemplate = \sprintf("columnTemplate: %s", $columnTemplate);
        return $this->columnTemplate;
    }

    /**
     *
     * @return mixed
     */
    public function getDataModel()
    {
        return $this->dataModel;
    }

    /**
     *
     * @param mixed $dataModel
     */
    public function setDataModel($dataModel)
    {
        $this->dataModel = \sprintf("dataModel: %s", $dataModel);
        return $this->dataModel;
    }

    /**
     *
     * @return mixed
     */
    public function getDetailModel()
    {
        return $this->detailModel;
    }

    /**
     *
     * @param mixed $detailModel
     */
    public function setDetailModel($detailModel)
    {
        $this->detailModel = \sprintf("detailModel: %s", $detailModel);
        return $this->detailModel;
    }

    /**
     *
     * @return mixed
     */
    public function getDragColumns()
    {
        return $this->dragColumns;
    }

    /**
     *
     * @param mixed $dragColumns
     */
    public function setDragColumns($dragColumns)
    {
        $this->dragColumns = \sprintf("dragColumns: %s", $dragColumns);
        return $this->dragColumns;
    }

    /**
     *
     * @return mixed
     */
    public function getDraggable()
    {
        return $this->draggable;
    }

    /**
     *
     * @param mixed $draggable
     */
    public function setDraggable($draggable)
    {
        $this->draggable = \sprintf("draggable: %s", $this->_convertBoolean($draggable));
        return $this->draggable;
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
        $this->editModel = \sprintf("editModel: %s", $editModel);
        return $this->editModel;
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
        $this->editor = \sprintf("editor: %s", $editor);
        return $this->editor;
    }

    /**
     *
     * @return mixed
     */
    public function getFilterModel()
    {
        return $this->filterModel;
    }

    /**
     *
     * @param mixed $filterModel
     */
    public function setFilterModel($filterModel)
    {
        $this->filterModel = \sprintf("filterModel: %s", $filterModel);
        return $this->filterModel;
    }

    /**
     *
     * @return mixed
     */
    public function getFlexHeight()
    {
        return $this->flexHeight;
    }

    /**
     *
     * @param mixed $flexHeight
     */
    public function setFlexHeight($flexHeight)
    {
        $this->flexHeight = \sprintf("flexHeight: %s", $flexHeight);
        return $this->flexHeight;
    }

    /**
     *
     * @return mixed
     */
    public function getFlexWidth()
    {
        return $this->flexWidth;
    }

    /**
     *
     * @param mixed $flexWidth
     */
    public function setFlexWidth($flexWidth)
    {
        $this->flexWidth = \sprintf("flexWidth: %s", $flexWidth);
        return $this->flexWidth;
    }

    /**
     *
     * @return mixed
     */
    public function getFreezeCols()
    {
        return $this->freezeCols;
    }

    /**
     *
     * @param mixed $freezeCols
     */
    public function setFreezeCols($freezeCols)
    {
        $this->freezeCols = \sprintf("freezeCols: %s", $freezeCols);
        return $this->freezeCols;
    }

    /**
     *
     * @return mixed
     */
    public function getFreezeRows()
    {
        return $this->freezeRows;
    }

    /**
     *
     * @param mixed $freezeRows
     */
    public function setFreezeRows($freezeRows)
    {
        $this->freezeRows = \sprintf("freezeRows: %s", $freezeRows);
        return $this->freezeRows;
    }

    /**
     *
     * @return mixed
     */
    public function getGroupModel()
    {
        return $this->groupModel;
    }

    /**
     *
     * @param mixed $groupModel
     */
    public function setGroupModel($groupModel)
    {
        $this->groupModel = \sprintf("groupModel: %s", $groupModel);
        return $this->groupModel;
    }

    /**
     *
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     *
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = \sprintf("height: %s", $height);
        return $this->height;
    }

    /**
     *
     * @return mixed
     */
    public function getHistoryModel()
    {
        return $this->historyModel;
    }

    /**
     *
     * @param mixed $historyModel
     */
    public function setHistoryModel($historyModel)
    {
        $this->historyModel = \sprintf("historyModel: %s", $historyModel);
        return $this->historyModel;
    }

    /**
     *
     * @return mixed
     */
    public function getHoverMode()
    {
        return $this->hoverMode;
    }

    /**
     *
     * @param mixed $hoverMode
     */
    public function setHoverMode($hoverMode)
    {
        $this->hoverMode = \sprintf("hoverMode: %s", $hoverMode);
        return $this->hoverMode;
    }

    /**
     *
     * @return mixed
     */
    public function getHwrap()
    {
        return $this->hwrap;
    }

    /**
     *
     * @param mixed $hwrap
     */
    public function setHwrap($hwrap)
    {
        $this->$hwrap = \sprintf("hwrap: %s", $hwrap);
        return $this->$hwrap;
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
    public function getNumberCell()
    {
        return $this->numberCell;
    }

    /**
     *
     * @param mixed $numberCell
     */
    public function setNumberCell($numberCell)
    {
        $v = \sprintf("{show: %s}", $this->_convertBoolean($numberCell));
        $this->numberCell = \sprintf("numberCell: %s", $v);
        return $this->numberCell;
    }

    /**
     *
     * @return mixed
     */
    public function getPageModel()
    {
        return $this->pageModel;
    }

    /**
     *
     * @param mixed $pageModel
     */
    public function setPageModel($pageModel)
    {
        $this->pageModel = \sprintf("pageModel: %s", $pageModel);
        return $this->pageModel;
    }

    /**
     *
     * @return mixed
     */
    public function getPasteModel()
    {
        return $this->pasteModel;
    }

    /**
     *
     * @param mixed $pasteModel
     */
    public function setPasteModel($pasteModel)
    {
        $this->pasteModel = \sprintf("pasteModel: %s", $pasteModel);
        return $this->pasteModel;
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
        $this->resizable = \sprintf("resizable: %s", $this->_convertBoolean($resizable));
        return $this->resizable;
    }

    /**
     *
     * @return mixed
     */
    public function getRoundCorners()
    {
        return $this->roundCorners;
    }

    /**
     *
     * @param mixed $roundCorners
     */
    public function setRoundCorners($roundCorners)
    {
        $this->roundCorners = \sprintf("roundCorners: %s", $this->_convertBoolean($roundCorners));
        return $this->roundCorners;
    }

    /**
     *
     * @return mixed
     */
    public function getRowBorders()
    {
        return $this->rowBorders;
    }

    /**
     *
     * @param mixed $rowBorders
     */
    public function setRowBorders($rowBorders)
    {
        $this->rowBorders = \sprintf("rowBorders: %s", $this->_convertBoolean($rowBorders));
        return $this->rowBorders;
    }

    /**
     *
     * @return mixed
     */
    public function getScrollModel()
    {
        return $this->scrollModel;
    }

    /**
     *
     * @param mixed $scrollModel
     */
    public function setScrollModel($scrollModel)
    {
        $this->scrollModel = \sprintf("scrollModel: %s", $scrollModel);
        return $this->scrollModel;
    }

    /**
     *
     * @return mixed
     */
    public function getSelectionModel()
    {
        return $this->selectionModel;
    }

    /**
     *
     * @param mixed $selectionModel
     */
    public function setSelectionModel($selectionModel)
    {
        $this->selectionModel = \sprintf("selectionModel: %s", $selectionModel);
        return $this->selectionModel;
    }

    /**
     *
     * @return mixed
     */
    public function getShowBottom()
    {
        return $this->showBottom;
    }

    /**
     *
     * @param mixed $showBottom
     */
    public function setShowBottom($showBottom)
    {
        $this->showBottom = \sprintf("showBottom: %s", $this->_convertBoolean($showBottom));
        return $this->showBottom;
    }

    /**
     *
     * @return mixed
     */
    public function getShowHeader()
    {
        return $this->showHeader;
    }

    /**
     *
     * @param mixed $showHeader
     */
    public function setShowHeader($showHeader)
    {
        $this->showHeader = \sprintf("showHeader: %s", $this->_convertBoolean($showHeader));
        return $this->showHeader;
    }

    /**
     *
     * @return mixed
     */
    public function getShowTitle()
    {
        return $this->showTitle;
    }

    /**
     *
     * @param mixed $showTitle
     */
    public function setShowTitle($showTitle)
    {
        $this->showTitle = \sprintf("showTitle: %s", $showTitle);
        return $this->showTitle;
    }

    /**
     *
     * @return mixed
     */
    public function getShowTop()
    {
        return $this->showTop;
    }

    /**
     *
     * @param mixed $showTop
     */
    public function setShowTop($showTop)
    {
        $this->showTop = \sprintf("showTop: %s", $this->_convertBoolean($showTop));
        return $this->showTop;
    }

    private function _convertBoolean($v)
    {
        if ($v == true) {
            return 'true';
        }
        return 'false';
    }

    /**
     *
     * @return mixed
     */
    public function getShowToolbar()
    {
        return $this->showToolbar;
    }

    /**
     *
     * @param mixed $showToolbar
     */
    public function setShowToolbar($showToolbar)
    {
        $this->showToolbar = \sprintf("showToolbar: %s", $this->_convertBoolean($showToolbar));
        return $this->showToolbar;
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
        $this->sortable = \sprintf("sortable: %s", $this->_convertBoolean($sortable));
        return $this->showToolbar;
    }

    /**
     *
     * @return mixed
     */
    public function getStringify()
    {
        return $this->stringify;
    }

    /**
     *
     * @param mixed $stringify
     */
    public function setStringify($stringify)
    {
        $this->stringify = \sprintf("stringify: %s", $this->_convertBoolean($stringify));
        return $this->stringify;
    }

    /**
     *
     * @return mixed
     */
    public function getStripeRows()
    {
        return $this->stripeRows;
    }

    /**
     *
     * @param mixed $stripeRows
     */
    public function setStripeRows($stripeRows)
    {
        $this->stripeRows = \sprintf("stripeRows: %s", $this->_convertBoolean($stripeRows));
        return $this->stripeRows;
    }

    /**
     *
     * @return mixed
     */
    public function getSwipeModel()
    {
        return $this->swipeModel;
    }

    /**
     *
     * @param mixed $swipeModel
     */
    public function setSwipeModel($swipeModel)
    {
        $this->swipeModel = \sprintf("swipeModel: %s", $swipeModel);
        return $this->swipeModel;
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
        $this->title = \sprintf("title: %s", $title);
        return $this->title;
    }

    /**
     *
     * @return mixed
     */
    public function getToolbar()
    {
        return $this->toolbar;
    }

    /**
     *
     * @param mixed $toolbar
     */
    public function setToolbar($toolbar)
    {
        $this->toolbar = \sprintf("toolbar: %s", $toolbar);
        return $this->toolbar;
    }

    /**
     *
     * @return mixed
     */
    public function getTrackModel()
    {
        return $this->trackModel;
    }

    /**
     *
     * @param mixed $trackModel
     */
    public function setTrackModel($trackModel)
    {
        $v = \sprintf("{on: %s}", $this->_convertBoolean($trackModel));
        $this->trackModel = \sprintf("trackModel: %s", $v);
        return $this->trackModel;
    }

    /**
     *
     * @return mixed
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     *
     * @param mixed $validation
     */
    public function setValidation($validation)
    {
        $this->validation = \sprintf("validation: %s", $validation);
        return $this->validation;
    }

    /**
     *
     * @return mixed
     */
    public function getVirtualX()
    {
        return $this->virtualX;
    }

    /**
     *
     * @param mixed $virtualX
     */
    public function setVirtualX($virtualX)
    {
        $this->virtualX = \sprintf("virtualX: %s", $this->_convertBoolean($virtualX));
        return $this->virtualX;
    }

    /**
     *
     * @return mixed
     */
    public function getVirtualXHeader()
    {
        return $this->virtualXHeader;
    }

    /**
     *
     * @param mixed $virtualXHeader
     */
    public function setVirtualXHeader($virtualXHeader)
    {
        $this->virtualXHeader = \sprintf("virtualXHeader: %s", $this->_convertBoolean($virtualXHeader));
        return $this->virtualXHeader;
    }

    /**
     *
     * @return mixed
     */
    public function getVirtualY()
    {
        return $this->virtualY;
    }

    /**
     *
     * @param mixed $virtualY
     */
    public function setVirtualY($virtualY)
    {
        $this->virtualY = \sprintf("virtualY: %s", $this->_convertBoolean($virtualY));
        return $this->virtualY;
    }

    /**
     *
     * @return mixed
     */
    public function getWarning()
    {
        return $this->warning;
    }

    /**
     *
     * @param mixed $warning
     */
    public function setWarning($warning)
    {
        $this->warning = \sprintf("warning: %s", $warning);
        return $this->warning;
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

    /**
     *
     * @return mixed
     */
    public function getWrap()
    {
        return $this->wrap;
    }

    /**
     *
     * @param mixed $wrap
     */
    public function setWrap($wrap)
    {
        $this->wrap = \sprintf("wrap: %s", $this->_convertBoolean($wrap));
        return $this->wrap;
    }
}
