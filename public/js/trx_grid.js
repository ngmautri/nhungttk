var toolbar = {
    cls: 'pq-toolbar-crud',
    items: [
        { type: 'button', label: 'Add', icon: 'ui-icon-plus', listeners: [{ click: addhandler}] },
        { type: 'button', label: 'Edit', icon: 'ui-icon-pencil', listeners: [{ click: edithandler}] },
        { type: 'button', label: 'Delete', icon: 'ui-icon-minus', listeners: [{ click: deletehandler}] }
    ]
};


var obj = {};
obj.width = "auto";
obj.height = $(window).height()-300;
obj.showTitle=false;
obj.sortable=true;
obj.hoverMode='row';
obj.pageModel = { type: "remote", rPP: 100, strRpp: "{0}" };
obj.editModel = {allowInvalid: true,saveKey: $.ui.keyCode.ENTER);
obj.editor={select: true};
obj.columnBorders=true;
obj.toolbar = toolbar;
obj.change: function (evt, ui) {};
obj.dataModel = {
	     dataType: "JSON",
            location: "remote",
            method: "GET",
            recIndx: "id",
            url: "<?php
            echo $rowGirdUrl;
            ?>",
            getData: function (response) {
         	    return { data: response.data, curPage: response.curPage, totalRecords: response.totalRecords};
           }
    };
            
 obj.colModel = [
	   { title: "", editable: false, minWidth: 55, sortable: false,
	       render: function (ui) {
	      		return '<button type="button" class="edit_btn">Edit </button>';
	        }
	   },
        { title: "FA Remarks", dataType: "string", dataIndx: "faRemarks", align: 'left',minWidth: 150,editable: true},
        { title: "GL", dataType: "string", dataIndx: "glAccountNumber", width:50,editable: false },
        { title: "CC", dataType: "string", dataIndx: "costCenterName", width:85,editable: false },
        { title: "Ref", dataType: "string", dataIndx: "sysNumber", width:120,editable: false},
        { title: "#", dataType: "integer", dataIndx: "rowNumber", width:10,editable: true },
        { title: "SKU", dataType: "string", dataIndx: "itemSKU", width:50,editable: false },
        { title: "Item", dataType: "string", dataIndx: "itemName", width:220,editable: false },
        { title: "Vendor Item Name", dataType: "string", dataIndx: "vendorItemName", width:180,editable: false },
        { title: "Code", dataType: "string", dataIndx: "vendorItemCode", width:100,editable: false },
        { title: "Doc Qty", dataType: "string", dataIndx: "docQuantity", width: 70,align: 'right',editable: false},
        { title: "Unit Price", dataType: "decimal", dataIndx: "docUnitPrice", width:90, align: 'right',editable: false},
        { title: "Unit", dataType: "string", dataIndx: "rowUnit", width: 50,align: 'right',editable: false},
        { title: "Net", dataType: "decimal", dataIndx: "netAmount", width:90, align: 'right',editable: false},
        { title: "Tax", dataType: "integer", dataIndx: "taxAmount", width:80, align: 'right',editable: false},
        { title: "Gross", dataType: "decimal", dataIndx: "grossAmount", width: 90,align: 'right',editable: false},
        { title: "Receiving", dataType: "decimal", dataIndx: "draftGrQuantity",align: 'right', width:80,editable: false },
        { title: "Received", dataType: "decimal", dataIndx: "postedGrQuantity", align: 'right',width:70,editable: false },
        { title: "Billed qty", dataType: "decimal", dataIndx: "postedAPQuantity", align: 'right',width:70,editable: false },
        { title: "Billed Amt", dataType: "decimal", dataIndx: "billedAmount",align: 'right', width:70,editable: false },
        { title: "Open Amt", dataType: "decimal", dataIndx: "openAPAmount",align: 'right', width:90,editable: false },
        { title: "Standard Qty", dataType: "integer", dataIndx: "convertedStandardQuantity", width: 70,align: 'right',editable: false},
        { title: "Standard Unit", dataType: "integer", dataIndx: "convertedStandardUnitPrice", width: 70,align: 'right',editable: false},
        { title: "Target WH", dataType: "string", dataIndx: "warehouseCode", width:120,editable: false },
        { title: "PR", dataType: "string", dataIndx: "prNumber", width:120,editable: false },
        { title: "Remarks", dataType: "string", dataIndx: "remarks", align: 'left',minWidth: 150,editable: false},
 ];

var $grid = $("#pr_row_gird").pqGrid(obj);

$grid.on('pqgridrefresh pqgridrefreshrow', function () {
        //debugger;
        var $grid = $(this);
        $grid.find("button.edit_btn").button({})
        .unbind("click")
        .bind("click", function (evt) {
            var $tr = $(this).closest("tr"),
            rowIndx = $grid.pqGrid("getRowIndx", { $tr: $tr }).rowIndx;
            rowData = $grid.pqGrid("getRowData", { rowIndx: rowIndx }),
            recIndx = $grid.pqGrid("option", "dataModel.recIndx");
            redirectUrl="/inventory/item-opening-balance/update-row?entity_token="+rowData['token']+"&entity_id="+rowData['id']+"&target_id="+rowData['docId']+"&target_token="+rowData['docToken'];
  			window.location.href = redirectUrl;
        });

     });

// important for datamodel: local.
$( "#pr_row_gird" ).pqGrid( "refreshDataAndView" );

function refreshGird(){
	$( "#pr_row_gird" ).pqGrid( "refreshDataAndView" )
}