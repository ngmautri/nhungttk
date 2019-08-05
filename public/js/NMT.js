/**
 * Nguyen Mau TRI
 */



function submitForm(form_id){
	
	var form_id_tmp;
	$('#b_modal_no_header').modal();
	form_id_tmp = '#' + form_id;
	$(form_id_tmp).submit();
	//$(form_id_tmp)[0].reset();
	//document.getElementById(form_id).reset();
	
}


function submitAjaxForm(url, form_id){
	var form_id_tmp;
	form_id_tmp = '#' + form_id;
	$( form_id_tmp).submit(function( event ) {
		 var form_data =   JSON.stringify($( this ).serializeArray());
		  event.preventDefault();
		  // alert(form_data);

          $.ajax({
              url: url,
              data: {
                  submited_form: form_data
              },
              dataType: "json",
              type: "POST",
              async: true,
              beforeSend: function (jqXHR, settings) {
            	  $("#global-notice").text("Working on it ...").show();
              },
              success: function (result) {
            	   // $("#global-notice").text("Updated...").show();
            		var n_errors = result.errors.length,s,i;
            		var messages = result.errors;
            		var redirectUrl = result.redirect_url;
            		//alert(result.status);
            		if(result.status ==-1){
	           			if (n_errors > 0) {
	           				s = '<div class="alert alert-danger" role="alert" style="font-size: 10.5pt">';
	           				s = s + '<ul>';
	           				for (i = 0; i < n_errors; i++) {
	           					s = s + '<li>' + messages[i] + '</li>';
	           				}
	           				s = s + '</ul></div>';
	           				$("#global-notice").html(s).show();
	           			}
		              }else{		            	    
		              	window.location.href = redirectUrl;
		              }
                },
              complete: function () {
            	 	$("#global-notice").delay(3500).fadeOut(500);
              }
          });
		  
	});
}


/**
 * 
 * @param cat_id
 * @param cat_name
 * @returns
 */
function loadPrRow(url) {

	$('#pr_rows').text("Loading...");
		// alert(cat_id);
		$.get(url, {
	}, function(data, status) {
		// alert(status);
		$('#pr_rows').html(data);

	});
}

/**
 * 
 * @param cat_id
 * @param cat_name
 * @returns
 */
function ajaxloadData(url,target_id) {
	
	$(target_id).text("Loading...");
		$.get(url, {
	}, function(data, status) {
		// alert(status);
		$(target_id).html(data);

	});
}

/**
 * 
 * @param cat_id
 * @param cat_name
 * @returns
 */
function filterPrRow(url) {
	var row_balance;
	var is_active;
	var sort_by;
	var sort;
	var perPage;

	
	row_balance = $( "#row_balance option:selected" ).val();
	is_active = $( "#is_active option:selected" ).val();
	sort_by = $( "#sort_by option:selected" ).val();
	perPage = $( "#perPage option:selected" ).val();
	sort = $('input[name=sort]:checked').val();
	url= url+'&balance=' + row_balance;
	url= url+'&is_active=' + is_active;
	url= url+'&perPage=' + perPage;
	url= url+'&sort_by='+ sort_by;
	url= url+'&sort=' + sort;

	// alert(url);
	
	$('#pr_rows').text("Loading...");
		// alert(cat_id);
		$.get(url, {
	}, function(data, status) {
		// alert(status);
		$('#pr_rows').html(data);

	});
}


/**
 * 
 * @param cat_id
 * @param cat_name
 * @returns
 */
function loadCategory(cat_id, cat_name) {
	
	var defaut_width = 1350;
	var container_with = $("#main_container").width();
	
	if(container_with > defaut_width){
		width = defaut_width;
	}else{
		width = container_with - 2;
	}
	
	$("#dialog1").dialog({
		width : width,
		height : $(window).height()-80,
		title : cat_name,
		modal : true,
		dialogClass : 'dialogClass'
	});

	// alert(cat_id);
	$('#content').text("Loading...");
		// alert(cat_id);
		$.get("/inventory/item-category/show", {
		cat_id : cat_id,
	}, function(data, status) {
		// updateCarts();
		
		
		$('#content').html(data);

	});
}

/**
 * 
 * @param title
 * @param source
 * @param target
 */
function showDialog1(title, source, target) {
	
	// $( "#dialog" ).text(t);
	$("#dialog1").dialog({
		width : 500,
		height : 530,
		title : "Select asset",
		modal : true,
		dialogClass : 'dialogClass'
	});
	loadData(title, source, target);
}


/**
 * 
 * @param modal_id
 * @param title
 * @param body
 * @returns
 */
function showBootstrapModal(modal_id, title, body) {
	var modal_id_tmp;
	var title_id;
	var body_id;
	
	modal_id_tmp = '#' + modal_id;
	title_id= modal_id_tmp+ "_title";
	body_id= modal_id_tmp+ "_body";
	
	$(title_id).html(title);
	$(body_id).html(body);
	$(modal_id_tmp).modal();
}


/**
 * 
 * @param title
 * @param source
 * @param target
 */
function showBootstrapDialog(title, source, target) {
	
	$('#modal_title').text(title);
	$('#modal1').modal();
	loadData(title, source, target,"B");
}

/**
 * 
 * @param title
 * @param source
 * @param target
 */
function showJqueryDialog(title, width, height, source, target, reponsive = true) {
	
	if (reponsive == true){
		var container_with = $("#main_container").width();
		//alert(container_with);
		if(container_with > width){
			width = width;
		}else{
			width = container_with - 5;
		}
	}
	
	
	$("#jquery_dialog").dialog({
		width : width,
		height : height,
		title : title,
		modal : true,
		dialogClass : 'dialogClass',
		// position: ['center', 'center'],
	});
	loadData(title, source, target, "J");
}


function searchEntity(source, context = null) {
	// $( "#dialog" ).text(t);
	var q;
	var target_id;
	
	var connector_symbol;
	var re = new RegExp("\\?");

	if (source.match(re)) {
		connector_symbol='&';
	}else{
		connector_symbol='?';
	}

	q = $("#search_term_"+context).val();
	
	if(context == "J"){
		target_id = '#j_loaded_data';
	}else if(context == "B"){
		target_id = '#b_loaded_data';
	}
	
	$(target_id).text("Loading...");
	// $('#b_loaded_data').empty()
	// alert(q);
	
	$.ajax({
		url : source + connector_symbol+'context='+ context + '&q='+ q,
		success : function(text) {
			// alert(text);
			$(target_id).html(text);
			$("#search_term_"+context).focus();
			
			
		}
	});
}

/**
 * 
 * @param source
 * @param target
 */
function loadData(title, source, target, context = null) {
	var target_id = '#' + target;
	var search_term_id;
	var connector_symbol;
	var re = new RegExp("\\?");

	if (source.match(re)) {
		connector_symbol='&';
	}else{
		connector_symbol='?';
	}
	
	// alert(source + connector_symbol + "context="+ context);
		
	$(target_id).text("Loading...");
	
	if(context == "J"){
		target_id = '#j_loaded_data';
		search_term_id = "#search_term_J";
	}else if(context == "B"){
		target_id = '#b_loaded_data';
		search_term_id = "#search_term_B";
	}
		$.ajax({
		url : source + connector_symbol + "context="+ context,
		success : function(text) {
			// alert(target_id);
			$(target_id).html(text);
			$(search_term_id).focus();
		}
	});
}

/*
 * @param source @param target
 */
function loadData1(source, context = null) {
	var search_term_id;
	
	if(context == "J"){
		target_id = '#j_loaded_data';
	}else if(context == "B"){
		target_id = '#b_loaded_data';
	}
	
	
	$(target_id).text("Loading...");
	search_term_id = "#search_term_"+context;
	
		
	$.ajax({
		url : source + "?context=" + context,
		success : function(text) {
			$(target_id).html(text);
			$(search_term_id).focus();
		}
	});
}


/*
 * @param source @param target
 */
function doPaginator(source, target=null) {
	var target_id = '#' + target;
	
	$(target_id).text("Loading...");
	//$('#b_modal_no_header').modal();
	
	$.ajax({
		url : source,
		success : function(text) {
			//alert(id);
			
			$(target_id).html(text);
			//$('#b_modal_no_header').modal('hide');

			}
	});
}
/**
 * 
 * @param id
 * @param target
 */
function selectId(id, target, name, target_name, context = null){
	var target_id = '#' + target;
	$(target_id).val(id);
	
	// alert(name);
	
	// alert(target_name);
	var target_name_id = '#' + target_name;
	$(target_name_id).val(name);
	
	$("#node_selected").html('"' + name + '" selected');
	
	// alert($(target_id).val());
	$('#modal1 .close').click();
	$('#global-notice').show();
	$('#global-notice').html('"' + name + '" selected');
	$('#global-notice').fadeOut(5000);
	$("#jquery_dialog").dialog("close");
}
/**
 * Clock
 */
function startTime() {
	var today = new Date();
	var h = today.getHours();
	var m = today.getMinutes();
	var s = today.getSeconds();
	// add a zero in front of numbers<10
	m = checkTime(m);
	s = checkTime(s);
	var s_today = DatumZeigen();
	$('#clock1').html(s_today + "<br>" + h + ":" + m + ":" + s);
	t = setTimeout('startTime()', 500);
}

function checkTime(i) {
	if (i < 10) {
		i = "0" + i
	}
	;
	return i;
}
/**
 * Date
 */
function DatumZeigen() {
	var T = new Date();
	var tag = new Array(7);
	tag[0] = "Su"
	tag[1] = "Mo"
	tag[2] = "Tu"
	tag[3] = "We"
	tag[4] = "Th"
	tag[5] = "Fr"
	tag[6] = "Sa"

	/*
	 * tag[0]="Sonntag" tag[1]="Montag" tag[2]="Dienstag" tag[3]="Mittwoch"
	 * tag[4]="Donnerstag" tag[5]="Freitag" tag[6]="Samstag"
	 */
	var monat = new Array(12);
	monat[0] = "Jan"
	monat[1] = "Feb"
	monat[2] = "Mar"
	monat[3] = "Apr"
	monat[4] = "May"
	monat[5] = "Jun"
	monat[6] = "Jul"
	monat[7] = "Aug"
	monat[8] = "Sep"
	monat[9] = "Oct"
	monat[10] = "Nov"
	monat[11] = "Dec"
	/*
	 * monat[0]="Januar" monat[1]="Februar" monat[2]="M�rz" monat[3]="April"
	 * monat[4]="Mai" monat[5]="Juni" monat[6]="Juli" monat[7]="August"
	 * monat[8]="September" monat[9]="Oktober" monat[10]="November"
	 * monat[11]="Dezember"
	 */
	// return (tag[T.getDay()] + ", " + T.getDate() + ". " + monat[T.getMonth()]
	// + " " + T.getFullYear());
	return (T.getDate() + ". " + monat[T.getMonth()]	+ " " + T.getFullYear());
	
}


function doUploadAttachment(form_id, attachment_id, url=null, 
		target_id=null, redirectUrl=null, checksum = null, 
		token = null, attachmentRequired = 1,
		entity_id = null, entity_token=null){
	
	var attachment_id_tmp;
	var form_id_tmp;
	var attachment;
	attachment_id_tmp = '#'+ attachment_id;
	form_id_tmp = '#'+ form_id;
	attachment = $(attachment_id_tmp)[0].files[0];
		
	if (typeof attachment !== "undefined") {
		// Ensure it's an image
		if (attachment.type.match(/image.*/)) {
			// alert(form_id_tmp);
			uploadImages(url, target_id, redirectUrl, checksum, token,form_id,$('#documentSubject').val(),entity_id,entity_token);
		}else{
			$('#b_modal_no_header').modal();
			$(form_id_tmp).submit();
		}
	}else{
		if(attachmentRequired==0){
			$('#b_modal_no_header').modal();
			$(form_id_tmp).submit();
		}else{
			showBootstrapModal('b_modal_sm','<b>Warning!</b>','<p>Please select attachment</p>');
		}
	}
}


/**
 * 
 */
function uploadImages(url, target_id, redirectUrl, checksum = null, token = null,form_id=null, subject =null,entity_id = null, entity_token=null) {

	var pic_to_upload = [];
	var pic_to_upload_resized = [];
	var pics;

	if(form_id==null){
		var pics = $('#sp_upload_pic_form input[type=file]');
	}else{
		var form_file_id;
		form_file_id = '#'+ form_id + ' input[type=file]';
		pics = $(form_file_id);
	}

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
		showBootstrapModal('b_modal_sm','<b>Info</b>','<p>Please select picture</p>');
		return;
	}

	$('#b_modal_no_header').modal();

	// checking input
	for (var j = 0; j < pic_to_upload.length; j++) {
		var p = pic_to_upload[j];

		// console.log(p.size);
		// var filetype = p.type;
		// var filename = p.name;
		// alert(filename);
		// Load the image
		var reader = new FileReader();

		reader.onload = (function(p, pic_to_upload_resized, n, url, target_id,
				redirectUrl,checksum, token,subject,entity_id,entity_token) {
			return function(e) {
				var contents = e.target.result;

				
				// alert('URL:' + token);
				// EXIF.jS
				EXIF
						.getData(
								p,
								function() {
									var pic_orientation;
									pic_orientation = this.exifdata.Orientation;

									// alert('URL:::' + url);

									// resize
									var image = new Image();

									image.onload = (function(p,
											pic_to_upload_resized) {

										return function(imageEvent) {

											var filetype = p.type;
											var filename = p.name;
										
											// alert('URL' + target_id);
											// alert('URL:::' + token);

											// Resize the image
											
											
											var canvas = document
													.createElement('canvas'), max_size = 1350, width = image.width, height = image.height;
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

											var ctx = canvas.getContext('2d')
											ctx.save();

											canvas.width = width;
											canvas.height = height;

											if (pic_orientation == 6) {
												canvas.width = height;
												canvas.height = width;
												ctx.rotate(0.5 * Math.PI);
												ctx.translate(0, -height);
											}

											ctx.drawImage(image, 0, 0, width,
													height);
											ctx.restore();
											
											
											switch (filetype) {
											case "image/jpeg":
												var dataUrl = canvas
														.toDataURL('image/jpeg');
												break;
											case "image/png":
												var dataUrl = canvas
														.toDataURL('image/png');
												break;
											case "image/bmp":
												var dataUrl = canvas
														.toDataURL('image/bmp');
												break;
											case "image/jpg":
												var dataUrl = canvas
														.toDataURL('image/jpeg');
												break;
											default:
												var dataUrl = canvas
														.toDataURL('image/jpeg');
												break;
											}
											
											var p_tmp = [];
											p_tmp.push(filetype);
											p_tmp.push(dataUrl);
											p_tmp.push(filename);

											pic_to_upload_resized.push(p_tmp);

											isUploadImagesCompleted(
													pic_to_upload_resized, n,
													url, target_id, redirectUrl,checksum, token,subject,entity_id,entity_token)
													// alert(n+'URL:::' +
													// subject);
										};

									})(p, pic_to_upload_resized, n, url,
											target_id, redirectUrl,checksum, token,subject,entity_id,entity_token); // must
																						// the
																						// //
																						// same

									image.src = contents;

								});
				;
			};

		})(p, pic_to_upload_resized, pic_to_upload.length, url, target_id,
				redirectUrl, checksum, token,subject,entity_id,entity_token); // must
																				// the
																				// same

		reader.readAsDataURL(p);
	}

}

function isUploadImagesCompleted(pic_to_upload_resized, n, url, target_id,
		redirectUrl,checksum, token,subject,entity_id,entity_token) {
	
		
		// alert('URL complete' + token);

	if (pic_to_upload_resized.length >= n) {
		// $('#myModal').modal(hide);

		/*
		 * var article_id = $("#article_id").val(); var redirectUrl =
		 * $("#redirectUrl").val();
		 */
		$.post(url, {
			target_id : target_id,
			checksum: checksum,
			token: token,
			subject: subject,
			entity_id:entity_id,
			entity_token:entity_token,
			pictures : pic_to_upload_resized,
		}, function(data, status, dataType) {
			// alert(data);
			// alert(data.success);
			if(data.success > 0){
				window.location = redirectUrl;
			}else{
				var index;
				var result = "";
				
				if(data.message.length>0){
  				    result = "<ul>";
					for (index = 0; index < data.message.length; ++index) {
						result = result + "<li>" + data.message[index] + "</li>";
					}
					result = result + "</ul>"
				}
					
				/*
				 * $('#b_modal_no_header').modal('toggle'); $( "#flash_messages"
				 * ).html(result); $( "#flash_messages" ).show(); $(
				 * "#flash_messages" ).delay(2200).fadeOut(1000);
				 */
				location.reload();
			}
		});
	}
}

/**
 * NMT
 */
function countdown() {

	// alert(id_array.length);

	for (var i = 0; i < id_array.length; i++) {

		var t = $('#' + id_array[i] + '_EDT').text();
		t = t.substring(0, 10);

		// alert(t);
		var d = new Date(t);

		var obverver_obj = $('#' + id_array[i] + '_timer');
		var a = d.getTime();
		setTimerObserver(timerSubject, obverver_obj, a);
	}

	// alert(a);
	// alert((timerSubject.observers.Count());

	if (timerSubject.observers.Count() > 0) {
		t = window.setInterval('setEventTimer(timerSubject)', 1000);
	}

}

/**
 * 
 */
function setEventTimer(timerSubject) {
	var today = new Date();
	var today_timestamp = today.getTime();
	// alert(today_timestamp);
	// alert(timerSubject.observers.Count());
	timerSubject.Notify(today_timestamp);

	// t = setTimeout('setEventTimer()', 500);
}

function setTimerObserver(timerSubject, o, a) {
	if (typeof o == 'object') {
		// vererben vom Observer
		inherits(new Observer(), o);

		// alert(o);

		o.Update = function(value) {
			if (a > value) {
				var t = a - value;
				var s_remain = getRemain(t);
				o.html(s_remain);
			} else if (a == value) {
				o.text('Event beginnt now!');
			} else {
				o
						.html('<span style="color:#999999; text-decoration:line-through">due</span>');
			}
		}

		timerSubject.AddObserver(o);

	}
}

/**
 * 
 */
function getRemain(input) {
	input = Math.round(input / 1000); // second

	var day_timestamp = 60 * 60 * 24;
	var hh_timestamp = 60 * 60;
	var mm_timestamp = 60;
	// d.setTime(input);
	var rest;
	var n_day = Math.floor(input / day_timestamp);
	var s_day = (n_day > 0) ? (n_day) + 'd ' : '';

	rest = input - n_day * day_timestamp;
	var n_hh = Math.floor(rest / hh_timestamp);
	var s_hh = (n_hh > 0) ? (n_hh) + 'h ' : '';

	rest = rest - n_hh * hh_timestamp;
	var n_mm = Math.floor(rest / mm_timestamp);
	var s_mm = (n_mm > 0) ? (n_mm) + 'm ' : '';

	rest = rest - n_mm * mm_timestamp;
	var n_ss = Math.floor(rest);

	// return ('<img src="/images/reminder.gif"/>&nbsp;' + s_day + s_hh + s_mm +
	// n_ss + 's');
	return ('<i class="icon-time"></i>&nbsp;' + s_day + s_hh + s_mm + n_ss + 's');
	// return (input);
}