$(document).ready(function() {
	$(document).ready(function() {
		$("#lightgallery").lightGallery();
		countdown();
	});

	$('[data-toggle="tooltip"]').tooltip();

	$("#select_ALL").change(function() {
		$(".checkbox1").prop('checked', $(this).prop("checked"));
	});
});

/**
 * function on calendar dialog
 */
function showDialog() {
	q = $("#asset").val();

	$("#dialog").html('Please wait ...');
	$
			.ajax({
				url : "/inventory/search/asset?json=1&query=" + q,

				success : function(text) {
					var obj = eval(text);
					var n_hits = obj.length;
					// alert(n_hits);
					// var html = "No asset found"
					var s;
					var i;
					s = "";
					if (n_hits > 0) {
						s = '<table class="pure-table pure-table-bordered"><thead><tr><td>ID</td><td>NAME</td><td>TAG</td><td>ACTION</td><td>DETAIL</td></thead></tr>';
						for (i = 0; i < n_hits; i++) {
							s = s + "<tr>"
							var id = obj[i]["id"];
							var name = obj[i]["name"];
							var tag = obj[i]["tag"];
							s = s + '<td>' + id + '</td>';
							s = s + '<td>' + name + '</td>';
							s = s + '<td>' + tag + '</td>';
							s = s
									+ '<td><a href="javascript:;" onclick="selectAsset(\''
									+ id + '\',\'' + name + '\',\'' + tag
									+ '\')">  Select  </a></td>';
							s = s + '<td><a href="/inventory/asset/show?id='
									+ id
									+ '" target="_blank">  Detail  </a></td>';
							s = s + "</tr>";

						}
						s = s + "</table>";

					} else {

						s = "No asset found!";
					}

					// alert(s);

					$("#dialog").html(s);
				}
			});

	// $( "#dialog" ).text(t);
	$("#dialog").dialog({
		width : 860,
		height : 500,
		title : "Select asset",
		modal : true,
		dialogClass : 'dialogClass'
	});
}

function selectAsset(id, name, tag) {
	$("#asset_id").val(id);
	$("#asset").val(tag);
	$("#dialog").dialog("close");

}

/**
 * function on calendar dialog
 */
function checkSparePartCode() {
	q = $("#sparepart_code").val();

	$("#dialog").html('Please wait ...');
	$
			.ajax({
				url : "/inventory/search/sparepart?json=1&query=" + q,

				success : function(text) {
					var obj = eval(text);
					var n_hits = obj.length;
					// alert(n_hits);
					// var html = "No asset found"
					var s;
					var i;
					s = "";
					if (n_hits > 0) {

						s = 'Sparepart code:<b>"' + q
								+ '"</b> exits already. Please recheck!<hr>';

						s = s
								+ '<table <table class="pure-table pure-table-bordered"><thead><tr><td>ID</td><td>NAME</td><td>TAG</td><td>CODE</td><td>DETAIL</td></thead></tr>';
						for (i = 0; i < n_hits; i++) {
							s = s + "<tr>"
							var id = obj[i]["id"];
							var name = obj[i]["name"];
							var tag = obj[i]["tag"];
							var code = obj[i]["code"];
							s = s + '<td>' + id + '</td>';
							s = s + '<td>' + name + '</td>';
							s = s + '<td>' + tag + '</td>';
							s = s + '<td>' + code + '</td>';
							// s = s + '<td><a href="javascript:;"
							// onclick="selectAsset(\''+ id + '\',\''+ name +
							// '\',\''+ tag +'\')"> SELECT </a></td>';
							s = s
									+ '<td><a href="/inventory/spareparts/show?id='
									+ id
									+ '" target="_blank">  Detail  </a></td>';

							s = s + "</tr>";

						}
						s = s + "</table>";

					} else {

						s = 'Sparepart code:<b>"' + q + '"</b> can be used<hr>';

					}

					// alert(s);

					$("#dialog").html(s);
				}
			});

	// $( "#dialog" ).text(t);
	$("#dialog").dialog({
		width : 650,
		height : 500,
		title : "Check Sparepart Code",
		modal : true,
		dialogClass : 'dialogClass'
	});
}

/**
 * 
 * 
 * 
 */
function uploadPictures() {

	var pic_to_upload = [];

	var pics = $('#UploadPicsForm input[type=file]');

	for (var i = 0; i < pics.length; i++) {

		var pic = pics[i];
		var pic_file = pic.files[0];

		// resize pic
		if (typeof pic_file !== "undefined") {

			// Ensure it's an image
			if (pic_file.type.match(/image.*/)) {

				console.log(pic_file.size);
				var filetype = pic_file.type;

				// Load the image
				var reader = new FileReader();

				reader.onload = function(e) {

					var image = new Image();
					image.scr = reader.result;
					image.onload = function(imageEvent) {

						// Resize the image
						var canvas = document.createElement('canvas'), max_size = 1500, // TODO
																						// :
																						// pull
																						// max
																						// size
																						// from
																						// a
																						// site
																						// config
						width = image.width, height = image.height;
						if (width > height) {
							if (width > max_size) {
								height *= max_size / width;
								width = max_size;
							}
						} else {
							if (height > max_size) {
								width *= max_size / height;
								height = max_size;
							}
						}
						canvas.width = width;
						canvas.height = height;
						canvas.getContext('2d').drawImage(image, 0, 0, width,
								height);

						switch (filetype) {
						case "image/jpeg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						case "image/png":
							var dataUrl = canvas.toDataURL('image/png');
							break;
						case "image/bmp":
							var dataUrl = canvas.toDataURL('image/bmp');
							break;
						case "image/jpg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						default:
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						}

						var asset_id = $("#asset_id").val();

						$.post("/inventory/asset/upload-picture1", {
							pictures : dataUrl,
							id : asset_id,
							filetype : filetype,
						}, function(data, status) {
							alert("Picture was uploaded \nStatus: " + status);
							$('#UploadPicsForm input[type=file]').val('')
									.clone(true);

						});

					}
					image.src = reader.result;
				}

				reader.readAsDataURL(pic_file);

			}
		}
	}
}

/**
 * 
 * 
 * 
 */
function countAsset() {

	var pic_to_upload = [];
	var pic_to_upload_resized = [];

	var pics = $('#CountAssetForm input[type=file]');

	// checking input
	for (var i = 0; i < pics.length; i++) {
		var pic = pics[i];
		var pic_file = pic.files[0];
		// resize pic
		if (typeof pic_file !== "undefined") {

			// Ensure it's an image
			if (pic_file.type.match(/image.*/)) {
				pic_to_upload.push(pic_file);
			}
		}
	}

	if (pic_to_upload.length < 1) {

		$('#modal1').modal();
		return;
	}

	$('#myModal').modal();

	// checking input
	for (var j = 0; j < pic_to_upload.length; j++) {
		var p = pic_to_upload[j];

		console.log(p.size);
		var filetype = p.type;

		// Load the image
		var reader = new FileReader();

		reader.onload = (function(p, pic_to_upload_resized, n) {
			return function(e) {
				var contents = e.target.result;

				// resize
				var image = new Image();

				image.onload = (function(p, pic_to_upload_resized, n) {
					return function(imageEvent) {

						// Resize the image
						var canvas = document.createElement('canvas'), max_size = 1200,
						width = image.width, height = image.height;
						if (width > height) {
							if (width > max_size) {
								height *= max_size / width;
								width = max_size;
							}
						} else {
							if (height > max_size) {
								width *= max_size / height;
								height = max_size;
							}
						}
						canvas.width = width;
						canvas.height = height;
						canvas.getContext('2d').drawImage(image, 0, 0, width,
								height);

						switch (filetype) {
						case "image/jpeg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						case "image/png":
							var dataUrl = canvas.toDataURL('image/png');
							break;
						case "image/bmp":
							var dataUrl = canvas.toDataURL('image/bmp');
							break;
						case "image/jpg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						default:
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						}

						var p_tmp = [];
						p_tmp.push(filetype);
						p_tmp.push(dataUrl);

						pic_to_upload_resized.push(p_tmp);

						isCountingCompleted(pic_to_upload_resized, n)
					};
				})(p, pic_to_upload_resized, n);

				image.src = contents;
			};

		})(p, pic_to_upload_resized, pic_to_upload.length);

		reader.readAsDataURL(p);
	}

}

function isCountingCompleted(pic_to_upload_resized, n) {
	if (pic_to_upload_resized.length >= n) {

		var asset_id = $("#asset_id").val();
		var counting_id = $("#counting_id").val();
		var updated_location = $("#updated_location").val();
		var redirectUrl = $("#redirectUrl").val();

		// alert(pic_to_upload_resized.length);

		$.post("/inventory/count/add-counting-item1", {
			asset_id : asset_id,
			counting_id : counting_id,
			updated_location : updated_location,
			pictures : pic_to_upload_resized,
		}, function(data, status) {
			window.location = redirectUrl;
		});
	}

}

/*
 * 
 * 
 */
function uploadAssetPictures() {

	var pic_to_upload = [];
	var pic_to_upload_resized = [];

	var pics = $('#UploadPicsForm input[type=file]');

	// checking input
	for (var i = 0; i < pics.length; i++) {
		var pic = pics[i];
		var pic_file = pic.files[0];
		// resize pic
		if (typeof pic_file !== "undefined") {

			// Ensure it's an image
			if (pic_file.type.match(/image.*/)) {
				pic_to_upload.push(pic_file);
			}
		}
	}

	if (pic_to_upload.length < 1) {

		$('#modal1').modal();
		return;
	}

	$('#myModal').modal();

	// checking input
	for (var j = 0; j < pic_to_upload.length; j++) {
		var p = pic_to_upload[j];

		console.log(p.size);
		var filetype = p.type;

		// Load the image
		var reader = new FileReader();

		reader.onload = (function(p, pic_to_upload_resized, n) {
			return function(e) {
				var contents = e.target.result;

				// resize
				var image = new Image();

				image.onload = (function(p, pic_to_upload_resized, n) {
					return function(imageEvent) {

						// Resize the image
						var canvas = document.createElement('canvas'), max_size = 1200, // TODO
																						// :
																						// config
						width = image.width, height = image.height;
						if (width > height) {
							if (width > max_size) {
								height *= max_size / width;
								width = max_size;
							}
						} else {
							if (height > max_size) {
								width *= max_size / height;
								height = max_size;
							}
						}
						canvas.width = width;
						canvas.height = height;
						canvas.getContext('2d').drawImage(image, 0, 0, width,
								height);

						switch (filetype) {
						case "image/jpeg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						case "image/png":
							var dataUrl = canvas.toDataURL('image/png');
							break;
						case "image/bmp":
							var dataUrl = canvas.toDataURL('image/bmp');
							break;
						case "image/jpg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						default:
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						}

						var p_tmp = [];
						p_tmp.push(filetype);
						p_tmp.push(dataUrl);

						pic_to_upload_resized.push(p_tmp);

						isUploadAssetPicturedCompleted(pic_to_upload_resized, n)
					};
				})(p, pic_to_upload_resized, n);

				image.src = contents;
			};

		})(p, pic_to_upload_resized, pic_to_upload.length);

		reader.readAsDataURL(p);
	}

}

function isUploadAssetPicturedCompleted(pic_to_upload_resized, n) {
	if (pic_to_upload_resized.length >= n) {

		var asset_id = $("#asset_id").val();
		var redirectUrl = $("#redirectUrl").val();

		$.post("/inventory/asset/upload-picture1", {
			asset_id : asset_id,
			pictures : pic_to_upload_resized,
		}, function(data, status) {
			window.location = redirectUrl;
		});
	}
}

/**
 * Upload SP pictures.
 */
function uploadSPPictures() {

	var pic_to_upload = [];
	var pic_to_upload_resized = [];

	var pics = $('#sp_upload_pic_form input[type=file]');

	// checking input
	for (var i = 0; i < pics.length; i++) {
		var pic = pics[i];
		var pic_file = pic.files[0];
		// resize pic
		if (typeof pic_file !== "undefined") {

			// Ensure it's an image
			if (pic_file.type.match(/image.*/)) {
				pic_to_upload.push(pic_file);
			}
		}
	}

	if (pic_to_upload.length < 1) {
		$('#modal1').modal();
		return;
	}

	$('#myModal').modal();

	// checking input
	for (var j = 0; j < pic_to_upload.length; j++) {
		var p = pic_to_upload[j];

		console.log(p.size);
		var filetype = p.type;

		// Load the image
		var reader = new FileReader();

		reader.onload = (function(p, pic_to_upload_resized, n) {
			return function(e) {
				var contents = e.target.result;

				// resize
				var image = new Image();

				image.onload = (function(p, pic_to_upload_resized, n) {
					return function(imageEvent) {

						// Resize the image
						var canvas = document.createElement('canvas'), max_size = 1200, // TODO
																						// :
																						// config
						width = image.width, height = image.height;
						if (width > height) {
							if (width > max_size) {
								height *= max_size / width;
								width = max_size;
							}
						} else {
							if (height > max_size) {
								width *= max_size / height;
								height = max_size;
							}
						}
						canvas.width = width;
						canvas.height = height;
						canvas.getContext('2d').drawImage(image, 0, 0, width,
								height);

						switch (filetype) {
						case "image/jpeg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						case "image/png":
							var dataUrl = canvas.toDataURL('image/png');
							break;
						case "image/bmp":
							var dataUrl = canvas.toDataURL('image/bmp');
							break;
						case "image/jpg":
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						default:
							var dataUrl = canvas.toDataURL('image/jpeg');
							break;
						}

						var p_tmp = [];
						p_tmp.push(filetype);
						p_tmp.push(dataUrl);

						pic_to_upload_resized.push(p_tmp);

						isUploadSPPicturedCompleted(pic_to_upload_resized, n)
					};
				})(p, pic_to_upload_resized, n);

				image.src = contents;
			};

		})(p, pic_to_upload_resized, pic_to_upload.length);

		reader.readAsDataURL(p);
	}

}

function isUploadSPPicturedCompleted(pic_to_upload_resized, n) {
	if (pic_to_upload_resized.length >= n) {

		var sparepart_id = $("#sparepart_id").val();
		var redirectUrl = $("#redirectUrl").val();
	

		$.post("/inventory/spareparts/upload-picture1", {
			sparepart_id : sparepart_id,
			pictures : pic_to_upload_resized,
		}, function(data, status) {
			window.location = redirectUrl;
		});
	}
}

function submitPR(ID) {
	$('#myModal').modal('hide');
	$('#myModal1').modal();
	redirectUrl = "/procurement/pr/my-pr"
	$.get("/procurement/pr/submit", {
		pr_id : ID,
	}, function(data, status) {
		window.location = redirectUrl;
	});

}

function approvePR(ID) {
	$('#myModal').modal('hide');
	$('#myModal1').modal();
	redirectUrl = "/procurement/pr/all-pr"
	$.get("/procurement/pr/approve", {
		pr_id : ID,
	}, function(data, status) {
		window.location = redirectUrl;
	});
}

function completeNotifyDNConfirm(ID) {
	$('#myModal').modal();
}

function completeNotifyDN(ID) {
	$('#myModal').modal('hide');
	$('#myModal1').modal();

	redirectUrl = "/procurement/delivery/my-delivery"
	$.get("/procurement/delivery/complete-notify", {
		dn_id : ID,
	}, function(data, status) {
		window.location = redirectUrl;
	});
}

/**
 * function on calendar dialog
 */
function loadVendorList() {
	$("#dialog").html('Please wait ...');
	$
			.ajax({
				url : "/procurement/vendor/list-json",
				success : function(text) {
					var obj = eval(text);
					var n_hits = obj.length;
					// alert(n_hits);
					// var html = "No asset found"
					var s;
					var i;
					s = '<span><a href="/procurement/vendor/add"><i class="icon-plus"> </i> <b>CREATE NEW VENDOR</b></a></span>';
					
					if (n_hits > 0) {
						
						s = s +'<div><table class="table table-striped table-bordered"><thead><tr><td><b>ID</b></td><td><b>NAME</b></td><td><b>KEY-WORDS</b></td><td><b>ACTION</b></td><td><b>DETAIL</b></td></thead></tr>';
						for (i = 0; i < n_hits; i++) {
							s = s + "<tr>"
							var id = obj[i]["id"];
							var name = obj[i]["name"];
							var keywords = obj[i]["keywords"];
							s = s + '<td>' + id + '</td>';
							s = s + '<td>' + name + '</td>';
							s = s + '<td>' + keywords + '</td>';
							s = s
									+ '<td><a href="javascript:;" onclick="selectVendor(\''
									+ id + '\',\'' + name + '\',\'' + keywords
									+ '\')">  Select  </a></td>';
							s = s + '<td><a href="/procurement/vendor/show?id='
									+ id
									+ '" target="_blank">  Detail  </a></td>';
							s = s + "</tr>";

						}
						s = s + "</table></div>";

					} else {

						s = "No Vendors found!";
					}

					// alert(s);

					$("#dialog").html(s);
				}
			});

	// $( "#dialog" ).text(t);
	$("#dialog").dialog({
		width : 860,
		height : 500,
		title : "Select Vendor",
		modal : true,
		dialogClass : 'dialogClass'
	});
}

function selectVendor(id, name, tag) {
	$("#vendor_id").val(id);
	$("#vendor").val(name);
	$("#dialog").dialog("close");

}

/* =============================== */
function openPRCart(ID) {
	var item_name_id;
	var item_unit_id;
	item_name_id = '#' + ID + '_name';
	item_unit_id = '#' + ID + '_unit';

	$('#end_date').val("");
	$('#item_quantity').val("").select();
	$('#item_remarks').val("");

	$('#item_name').val($(item_name_id).text());
	$('#item_unit').val($(item_unit_id).text());

	$('#item_id').val(ID);
	$('#myModal').modal();
	$('#item_quantity').select();

}

function addItemToCart(type) {
	$('#myModal').modal('hide');
	$('#myModal1').modal();

	var item_name;
	var item_priority;
	var item_edt;
	var item_unit;
	var item_quantity;
	var item_remarks;
	var ID;

	ID = $('#item_id').val();
	item_name = $('#item_name').val();
	item_unit = $('#item_unit').val();
	item_priority = $('#item_priority').val();

	item_edt = $('#end_date').val();
	item_quantity = $('#item_quantity').val();
	item_remarks = $('#item_remarks').val();
	$('#_status').text("");

	$.post("/procurement/pr/add-pr-cart", {
		item_id : ID,
		item_type : type,
		priority : item_priority,
		name : item_name,
		unit:item_unit,
		quantity : item_quantity,
		EDT : item_edt,
		remarks : item_remarks,

	}, function(data, status) {
		// window.location = redirectUrl;
		var obj = eval(data);
		var status = obj['status'];
		var messages = obj['messages'];
		if (status == 1) {
			$('#myModal1').modal('hide');
			$('#_status').show();
			updateCarts();
		} else {
			$('#myModal1').modal('hide');
			var n_errors = messages.length;
			if (n_errors > 0) {
				s = '<div class="alert alert-error">';
				s = s + '<ul>';
				for (i = 0; i < n_errors; i++) {
					s = s + '<li>' + messages[i] + '</li>';
				}
				s = s + '</ul></div>';
			}

			$('#_status').html(s);
			$('#_status').show();
			$('#myModal').modal();
			$('#_status').fadeOut(6000);
		}
	});

}

/**
 * function on calendar dialog
 */
function updateCarts() {
	$("#cart_items").text('???');

	$.ajax({
		url : "/procurement/pr/update-cart",
		success : function(text) {
			var obj = eval(text);
			var total_cart_items = obj['total_cart_items'];

			$("#cart_items").text(total_cart_items);
			$("#cart_items").attr('class', 'label label-warning');
		}
	});
}

function openConfirmation(ID) {
	$('#myModal').modal();
}

function submitCartItems() {

	// var all_options = [];
	var selected_items = '';
	var i = 0;

	var pr_number = $('#pr_number').val();
	var SelectAll = $('#select_ALL').prop('checked') ? "YES" : "NO";

	selected_items = selected_items + '(';
	$(".checkbox1").each(function() {
		if (this.checked) {
			i = i + 1;
			selected_items = selected_items + this.value + ',';
		}
	});

	// remove last ','
	selected_items = selected_items.substring(selected_items.length - 1, 1);
	selected_items = '(' + selected_items + ')';

	if (SelectAll === 'NO' && i === 0) {
		$('#myModal').modal('hide');
		alert('no item selected');
		return;
	}

	$('#myModal').modal('hide');
	$('#myModal1').modal();

	redirectUrl = "/procurement/pr/my-pr";
	$.get("/procurement/pr/submit-cart-items", {
		pr_number : pr_number,
		cart_items : selected_items,
		SelectAll : SelectAll,
	}, function(data, status) {
		updateCarts();
		window.location = redirectUrl;
	});
}

/**
 * 
 * @param ID
 */
function deleteCartItemDialog(ID) {
	$('#cart_item_to_delete').val(ID);
	$('#myModal2').modal();
}

/**
 * 
 */
function deleteCartItem() {
	$('#myModal2').modal('hide');
	$('#myModal1').modal();
	var cart_item_id = $('#cart_item_to_delete').val();

	redirectUrl = "/procurement/pr/cart";
	$.get("/procurement/pr/delete-cart-item", {
		id : cart_item_id,
	}, function(data, status) {
		updateCarts();
		window.location = redirectUrl;

	});
}

/**
 * 
 */
function createArticleCat(parent_id, node_id, node_text) {
	$('#myModal1').modal();

	redirectUrl = "/inventory/article/category";
	$.post("/inventory/article/add-category", {
		parent_id : parent_id,
		name : node_text,
		cat_id : node_id,
		redirectUrl : redirectUrl
	}, function(data, status) {
		window.location = redirectUrl;
		// alert(status);
		// $('#myModal1').hide();
	});
}

function deleteArticleCat(cat_id) {	
	$('#myModal1').modal();
	var redirectUrl = "/inventory/article/category";
	$.get("/inventory/article/delete-category", {
		cat_id : cat_id,
	}, function(data, status) {
		var obj = eval(data);
		var status = obj['status'];
		var messages = obj['messages'];
		
		
		if (status == 1) {
			window.location = redirectUrl;
		} else {
			$('#myModal1').modal('hide');
			var n_errors = messages.length;
			if (n_errors > 0) {
				s = '<div class="alert alert-error">';
				s = s + '<ul>';
				for (i = 0; i < n_errors; i++) {
					s = s + '<li>' + messages[i] + '</li>';
				}
				s = s + '</ul></div>';
			}

			$('#_status').html(s);
			$('#_status').show();
			$('#myModal').modal();
		}
	});
}

/**
 * 
 */
function createRole(parent_id, node_id, node_text) {
	$('#myModal1').modal();
	var redirectUrl = "/user/role/list";
	$.post("/user/role/add", {
		parent_name : parent_id,
		role : node_text,
		role_id : node_id,
	}, function(data, status) {
		var obj = eval(data);
		var status = obj['status'];
		var messages = obj['messages'];
		if (status == 1) {
			window.location = redirectUrl;
		} else {
			$('#myModal1').modal('hide');
			var n_errors = messages.length;
			if (n_errors > 0) {
				s = '<div class="alert alert-error">';
				s = s + '<ul>';
				for (i = 0; i < n_errors; i++) {
					s = s + '<li>' + messages[i] + '</li>';
				}
				s = s + '</ul></div>';
			}

			$('#_status').html(s);
			$('#_status').show();
			$('#myModal').modal();
		}
	});
}



/* =============================== */
function openDeliveryConfirmation(ID,URL) {
	var delivered_quantity;
	var item_name;
	delivered_quantity_id = '#' + ID + '_delivered_quantity';
	item_name = '#' + ID + '_item_name';
	$('#delivered_quantity').val($(delivered_quantity_id).text());
	$('#item_name').val($(item_name).text());
	
	$('#confirmed_quantity').on('input',function(e){
			$('#rejected_quantity').val($('#delivered_quantity').val()- $('#confirmed_quantity').val());

		});
	
	$('#end_date').val("");
	$('#_uri').val(URL);
	

	$('#item_id').val(ID);
	$('#myModal').modal();
	$('#confirmed_quantity').val($(delivered_quantity_id).text());
	$('#rejected_quantity').val(0);

	$('#confirmed_quantity').select();
}

/**
 * 	dn_id : dn_id,	
	dn_item_id:dn_item_id,
	pr_item_id:pr_item_id,
	sparepart_id:sparepart_id,
	article_id:,
	asset_id:,

 */
function doConfirmDelivery() {
	
	var delivered_quantity = $('#delivered_quantity').val();
	var confirmed_quantity = $('#confirmed_quantity').val();
	var rejected_quantity = $('#rejected_quantity').val();
	var remarks = $('#confirmation_remarks').val();

	var uri = $('#_uri').val();

	$('#myModal').modal('hide');
	$('#myModal1').modal();
	redirectUrl = "/procurement/delivery/get-notification";
	$.get(uri, {
	confirmed_quantity : confirmed_quantity,
	rejected_quantity : rejected_quantity,
	remarks:remarks,
	}, function(data, status) {
		//alert(data);
		window.location = redirectUrl;
	});
	
}

/**
 * 
 * @param ID
 */
function deleteDNCartItemDialog(ID) {
	$('#cart_item_to_delete').val(ID);
	$('#myModal2').modal();
}

/**
 * 
 */
function deleteDNCartItem() {
	$('#myModal2').modal('hide');
	$('#myModal1').modal();
	var cart_item_id = $('#cart_item_to_delete').val();

	redirectUrl = "/procurement/delivery/review-cart";
	$.get("/procurement/delivery/delete-cart-item", {
		id : cart_item_id,
	}, function(data, status) {
		//updateCarts();
		window.location = redirectUrl;

	});
}

function submitDNCartItems() {

	// var all_options = [];
	var selected_items = '';
	var i = 0;

	var dn_number = $('#dn_number').val();
	var SelectAll = $('#select_ALL').prop('checked') ? "YES" : "NO";

	selected_items = selected_items + '(';
	$(".checkbox1").each(function() {
		if (this.checked) {
			i = i + 1;
			selected_items = selected_items + this.value + ',';
		}
	});

	// remove last ','
	selected_items = selected_items.substring(selected_items.length - 1, 1);
	selected_items = '(' + selected_items + ')';

	if (SelectAll === 'NO' && i === 0) {
		$('#myModal').modal('hide');
		alert('no item selected');
		return;
	}

	$('#myModal').modal('hide');
	$('#myModal1').modal();

	redirectUrl = "/procurement/delivery/my-delivery";
	$.get("/procurement/delivery/submit-cart-items", {
		dn_number : dn_number,
		cart_items : selected_items,
		SelectAll : SelectAll,
	}, function(data, status) {
		updateCarts();
		window.location = redirectUrl;
	});
}


