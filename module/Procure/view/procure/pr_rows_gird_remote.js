<div id="param_query_div"></div>
var obj = {
    height: 400,
    width: 400,
    showTop: true,
    collapsible: true,
    showHeader: true,
    showBottom: true,
    editable: true,
    wrap: true
    /*
     * |=============================
     * |DATA MODEL
     * |
     * |=============================
     */
    ,
    dataModel: {
        location: "local",
        url: "sdsddsfgfd"
    }
    /*
     * |=============================
     * |COLS MODEL
     * |
     * |=============================
     */
    ,
    colModel: [
        {
            dataIndx: "OK",
            dataType: "integer",
            title: "OK"
        },
        {
            dataIndx: "OK",
            dataType: "integer",
            title: "Test",
            align: "left",
            minWidth: 55
        }
    ],

};
var obj = {
    height: 400,
    width: 400,
    showTop: true,
    collapsible: true,
    showHeader: true,
    showBottom: true,
    editable: true,
    wrap: true
    /*
     * |=============================
     * |DATA MODEL
     * |
     * |=============================
     */
    ,
    dataModel: {
        location: "local",
        url: "sdsddsfgfd"
    }
    /*
     * |=============================
     * |COLS MODEL
     * |
     * |=============================
     */
    ,
    colModel: [
        {
            dataIndx: "OK",
            dataType: "integer",
            title: "OK"
        },
        {
            dataIndx: "OK",
            dataType: "integer",
            title: "Test",
            align: "left",
            minWidth: 55
        }
    ],

};

// Create ParamQuery Object!
var $grid = $("#param_query_div").pqGrid(obj)