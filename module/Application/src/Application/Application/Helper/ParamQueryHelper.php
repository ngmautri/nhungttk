<?php
namespace Application\Application\Helper;

use Application\Application\Helper\Contracts\AbstractParamQuery;

/**
 * to create paramquerey gird
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ParamQueryHelper extends AbstractParamQuery
{

    protected $output;

    protected $changeEvent;

    protected $remoteUrl;

    public function addComment($comment)
    {
        $tmp = $this->output;

        $format = "
       /*
        * |=============================
        * |%s
        * |
        * |=============================
        */";
        $c = sprintf($format, $comment);

        $tmp = $tmp . $c . "\n";
        $this->output = $tmp;
    }

    public function addOption($option)
    {
        $tmp = $this->output;

        if ($tmp == null) {
            $tmp = $option;
        } else {
            $tmp = $tmp . ',' . "\n" . $option;
        }

        $this->output = $tmp;
    }

    public function openTag()
    {
        return "\n var obj = {\n ";
    }

    public function endTag()
    {
        return "\n };";
    }

    public function getOutPut()
    {
        $this->addOption($this->getChangeEvent());
        return $this->openTag() . $this->output . $this->endTag();
    }

    /**
     * event
     * Type: Event
     *
     * ui
     * Type: Object
     *
     * rowList
     * Type: Array
     * Array of objects { rowData: rowData, newRow: newRow, oldRow: oldRow, type: type } where type may be 'add', 'update' or 'delete'.
     *
     * source
     * Type: String
     * origin of the change e.g., 'edit', 'update', 'add' , 'delete', 'paste', 'undo', 'redo' or a custom source passed to addRow, updateRow, deleteRow methods.
     *
     * allowInvalid
     * Type: Boolean
     * Allows invalid value and adds an invalid class to the cell/cells.
     *
     * history
     * Type: Boolean
     * Whether add this operation in history.
     *
     * checkEditable
     * Type: Boolean
     * Checks whether the row/cell is editable before making any change.
     *
     * @return string
     */
    public function getChangeEventTemplate($url)
    {
        $t = '';
        return $t;
    }

    public function setChangeEventTemplate($url)
    {}

    public function getParamQueryTemplate()
    {
        $t = "
        var obj = {
                    width: 400,
                    height: 360,
                    showTop: false,
                    showBottom: false,
                    //collapsible: false,
                    showHeader: false,
                    roundCorners: false,
                    rowBorders: false,
                    columnBorders: false,
                    selectionModel: { type: \'cell' },
                    numberCell: { show: false },
                    stripeRows: false,
                    title: \"Grid Constituents\",
                    toolbar: {
                        items: [
                        { type: \"button\", label: 'button on toolbar' }
                        ]
                    }
                };
        ";
        return $t;
    }

    /**
     *
     * @return string
     */
    public function getChangeEvent()
    {
        return $this->changeEvent;
    }

    /**
     *
     * @param string $changeEvent
     */
    public function setChangeEvent($changeEvent)
    {
        $this->changeEvent = $changeEvent;
    }

    /**
     *
     * @return mixed
     */
    public function getRemoteUrl()
    {
        return $this->remoteUrl;
    }

    /**
     *
     * @param mixed $remoteUrl
     */
    public function setRemoteUrl($remoteUrl)
    {
        $this->remoteUrl = $remoteUrl;
    }
}
