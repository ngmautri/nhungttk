var toolbar = {
    cls: 'pq-toolbar-crud',
    items: [{
            type: 'button',
            label: 'Add New Line',
            icon: 'ui-icon-plus',
            listeners: [{
                "click": function(evt, ui) {
                    var $grid = $(this).closest('.pq-grid');
                    redirectUrl = $add_row_url;
                    window.location.href = redirectUrl;
                }
            }]
        },

        //{ type: 'button', label: 'Edit', icon: 'ui-icon-pencil', listeners: [{ click: edithandler}] },
        //{ type: 'button', label: 'Delete', icon: 'ui-icon-minus', listeners: [{ click: deletehandler}] }
    ]
};

//ParamQuery Object
var obj = {
    width: "auto",
    height: $(window).height() - 300,
    showTitle: false,
    resizable: true,
    wrap: false,
    roundCorners: true,
    sortable: true,
    freezeCols: 2,
    hoverMode: 'row',
    pageModel: { type: "remote", rPP: 100, strRpp: "{0}" },
    editModel: {
        allowInvalid: true,
        saveKey: $.ui.keyCode.ENTER
    },
    editor: {
        select: true
    },
    columnBorders: true,
    toolbar: toolbar,
    change: function(evt, ui) {
        if (ui.source == 'commit' || ui.source == 'rollback') {
            return;
        }
        console.log(ui);
        var $grid = $(this),
            grid = $grid.pqGrid('getInstance').grid;
        var rowList = ui.rowList,
            addList = [],
            recIndx = grid.option('dataModel').recIndx,
            deleteList = [],
            updateList = [];

        for (var i = 0; i < rowList.length; i++) {
            var obj = rowList[i],
                rowIndx = obj.rowIndx,
                newRow = obj.newRow,
                type = obj.type,
                rowData = obj.rowData;
            if (type == 'add') {
                var valid = grid.isValid({ rowData: newRow, allowInvalid: true }).valid;
                if (valid) {
                    addList.push(newRow);
                }
            } else if (type == 'update') {
                var valid = grid.isValid({ rowData: rowData, allowInvalid: true }).valid;
                if (valid) {
                    if (rowData[recIndx] == null) {
                        addList.push(rowData);
                    }
                    //else if (grid.isDirty({rowData: rowData})) {
                    else {
                        updateList.push(rowData);
                        //alert(rowData[recIndx]  + "remarks: " + rowData.item_name);

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
            //alert(sent_list);

            $.ajax({
                url: '/procure/po/update-row',
                data: {
                    sent_list: sent_list
                },
                dataType: "json",
                type: "POST",
                async: true,
                beforeSend: function(jqXHR, settings) {},
                success: function(changes) {

                },
                complete: function() {
                    $("#global-notice").delay(2200).fadeOut(500);
                    refreshGird();
                }
            });
        }
    },

};

// Data Model
obj.dataModel = {
    dataType: "JSON",
    location: "remote",
    method: "GET",
    recIndx: "id",
    url: rows_gird_url,
    getData: function(response) {
        return { data: response.data, curPage: response.curPage, totalRecords: response.totalRecords };
    }
};

// Columns
obj.colModel = [{
        title: "",
        editable: false,
        minWidth: 55,
        sortable: false,
        render: function(ui) {
            return '<button type="button" class="edit_btn">Edit</button>';
        }
    },

    { title: "FA Remarks", dataType: "string", dataIndx: "faRemarks", align: 'left', minWidth: 150, editable: true },
    { title: "GL", dataType: "string", dataIndx: "glAccountNumber", width: 50, editable: false },
    { title: "CC", dataType: "string", dataIndx: "costCenterName", width: 85, editable: false },
    { title: "Ref", dataType: "string", dataIndx: "sysNumber", width: 120, editable: false },
    { title: "#", dataType: "integer", dataIndx: "rowNumber", width: 10, editable: true },
    { title: "SKU", dataType: "string", dataIndx: "itemSKU", width: 50, editable: false },
    { title: "Item", dataType: "string", dataIndx: "itemName", width: 220, editable: false },
    { title: "Vendor Item Name", dataType: "string", dataIndx: "vendorItemName", width: 180, editable: false },
    { title: "Code", dataType: "string", dataIndx: "vendorItemCode", width: 100, editable: false },
    { title: "Cogs", dataType: "string", dataIndx: "cogsLocal", width: 70, align: 'right', editable: false },
    { title: "Doc Qty", dataType: "string", dataIndx: "docQuantity", width: 70, align: 'right', editable: false },
    { title: "Unit Price", dataType: "decimal", dataIndx: "docUnitPrice", width: 90, align: 'right', editable: false },
    { title: "Onhand Qty", dataType: "decimal", dataIndx: "stockQty", align: 'right', width: 80, editable: false },
    { title: "Unit", dataType: "string", dataIndx: "rowUnit", width: 50, align: 'right', editable: false },
    { title: "Net", dataType: "decimal", dataIndx: "netAmount", width: 90, align: 'right', editable: false },
    { title: "Tax", dataType: "integer", dataIndx: "taxAmount", width: 80, align: 'right', editable: false },
    { title: "Gross", dataType: "decimal", dataIndx: "grossAmount", width: 90, align: 'right', editable: false },
    { title: "Target WH", dataType: "string", dataIndx: "warehouseCode", width: 120, editable: false },
    { title: "PR", dataType: "string", dataIndx: "prNumber", width: 120, editable: false },
    { title: "Remarks", dataType: "string", dataIndx: "remarks", align: 'left', minWidth: 150, editable: false },
];

var $grid = $("#pr_row_gird").pqGrid(obj);

$grid.on('pqgridrefresh pqgridrefreshrow', function() {
    //debugger;
    var $grid = $(this);

    $grid.find("button.edit_btn").button({})
        .unbind("click")
        .bind("click", function(evt) {

            var $tr = $(this).closest("tr");
            var rowIndx = $grid.pqGrid("getRowIndx", { $tr: $tr }).rowIndx;
            var rowData = $grid.pqGrid("getRowData", { rowIndx: rowIndx });
            var recIndx = $grid.pqGrid("option", "dataModel.recIndx");

            redirectUrl = "/update-row?entity_token=" + rowData['token'] + "&entity_id=" + rowData['id'] + "&target_id=" + rowData['docId'] + "&target_token=" + rowData['docToken'];
            window.location.href = redirectUrl;
        });

});