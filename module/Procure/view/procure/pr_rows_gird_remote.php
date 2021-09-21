var toolbar = {
cls: 'pq-toolbar-crud',
items: [
{ type: 'button', label: 'Add New Line', icon: 'ui-icon-plus', listeners: [{ "click":

function (evt, ui) {
var $grid = $(this).closest('.pq-grid');
redirectUrl=$add_row_url;
?>";
window.location.href= redirectUrl;
}

}] },

//{ type: 'button', label: 'Edit', icon: 'ui-icon-pencil', listeners: [{ click: edithandler}] },
//{ type: 'button', label: 'Delete', icon: 'ui-icon-minus', listeners: [{ click: deletehandler}] }
]
};