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
        $t = <<<EOD
\n change: function (evt, ui) {
    if (ui.source == 'commit' || ui.source == 'rollback') {return;}
    console.log(ui);
    var \$grid = $(this), grid = \$grid.pqGrid('getInstance').grid;
    var rowList = ui.rowList, addList = [], recIndx = grid.option('dataModel').recIndx, deleteList = [],updateList = [];
 
    for (var i = 0; i < rowList.length; i++) {
        var obj = rowList[i], rowIndx = obj.rowIndx, newRow = obj.newRow, type = obj.type, rowData = obj.rowData;   
        
        if (type == 'add') {
            var valid = grid.isValid({ rowData: newRow, allowInvalid: true }).valid;
            if (valid) {
                addList.push(newRow);
            }
        }else if (type == 'update') {
            var valid = grid.isValid({ rowData: rowData, allowInvalid: true }).valid;
            if (valid) {
                if (rowData[recIndx] == null) {
                    addList.push(rowData);
                }else{
                    updateList.push(rowData);
                }
            }
        }
    }

    if (addList.length || updateList.length || deleteList.length) {
        
        var sent_list = JSON.stringify({
            updateList: updateList,
            addList: addList,
            deleteList: deleteList
        });
        
        $.ajax({
            url: $url,
            data: {
                sent_list: sent_list
            },
            dataType: "json",
            type: "POST",
            async: true,
            beforeSend: function (jqXHR, settings) {
                //@todo:


            },
            success: function (changes) {
                //@todo:
            },
            complete: function () {
                //@todo:
                refreshGird();
                location.reload();
                $("#global-notice").delay(5000).fadeOut(500); 
            }
        };
    }

},
EOD;
        return $t;
    }

    public function setChangeEventTemplate($url)
    {
        $t = <<<EOD
\n change: function (evt, ui) {
    if (ui.source == 'commit' || ui.source == 'rollback') {return;}
    console.log(ui);
    var \$grid = $(this), grid = \$grid.pqGrid('getInstance').grid;
    var rowList = ui.rowList, addList = [], recIndx = grid.option('dataModel').recIndx, deleteList = [],updateList = [];
    
    for (var i = 0; i < rowList.length; i++) {
        var obj = rowList[i], rowIndx = obj.rowIndx, newRow = obj.newRow, type = obj.type, rowData = obj.rowData;
        
        if (type == 'add') {
            var valid = grid.isValid({ rowData: newRow, allowInvalid: true }).valid;
            if (valid) {
                addList.push(newRow);
            }
        }else if (type == 'update') {
            var valid = grid.isValid({ rowData: rowData, allowInvalid: true }).valid;
            if (valid) {
                if (rowData[recIndx] == null) {
                    addList.push(rowData);
                }else{
                    updateList.push(rowData);
                }
            }
        }
    }
    
    if (addList.length || updateList.length || deleteList.length) {
    
        var sent_list = JSON.stringify({
            updateList: updateList,
            addList: addList,
            deleteList: deleteList
        });
        
        $.ajax({
            url: "$url",
            data: {
                sent_list: sent_list
            },
            dataType: "json",
            type: "POST",
            async: true,
            beforeSend: function (jqXHR, settings) {
                //@todo:
                
                
            },
            success: function (changes) {
                //@todo:
            },
            complete: function () {
                //@todo:
                refreshGird();
                location.reload();
                $("#global-notice").delay(5000).fadeOut(500);
            }
        };
    }
    
},
EOD;
        $this->changeEvent = $t;
    }

    public function getParamQueryTemplate()
    {
        $t = <<<EOD
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
            selectionModel: { type: 'cell' },
            numberCell: { show: false },
            stripeRows: false,
            title: "Grid Constituents",
            toolbar: {
                items: [
                { type: "button", label: 'button on toolbar' }
                ]
            }
        };
EOD;
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
}
