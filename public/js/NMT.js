/**
 * 
 * @param cat_id
 * @param cat_name
 * @returns
 */
function loadCategory(cat_id, cat_name) {
	
	var defaut_width = 1000;
	var container_with = $("#main_container").width();
	
	if(container_with > defaut_width){
		width = defaut_width;
	}else{
		width = container_with - 2;
	}
	
	$("#dialog1").dialog({
		width : width,
		height : 550,
		title : cat_name,
		modal : true,
		dialogClass : 'dialogClass'
	});

	// alert(cat_id);
	$('#content').text("Loading...");
		// alert(cat_id);
		$.get("/application/item-category/show", {
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
		
		if(container_with > width){
			width = width;
		}else{
			width = container_with - 2;
		}
	}
	
	
	$("#jquery_dialog").dialog({
		width : width,
		height : height,
		title : title,
		modal : true,
		dialogClass : 'dialogClass',
		//position: ['center', 'center'],
	});
	loadData(title, source, target, "J");
}


function searchEntity(source, context = null) {
	// $( "#dialog" ).text(t);
	var q;
	var target_id;

	q = $("#search_term_"+context).val();
	
	if(context == "J"){
		target_id = '#j_loaded_data';
	}else if(context == "B"){
		target_id = '#b_loaded_data';
	}
	
	$(target_id).text("Loading...");
	//$('#b_loaded_data').empty()
	//alert(q);
	
	$.ajax({
		url : source + '?context='+ context + '&q='+ q,
		success : function(text) {
			//alert(text);
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
	
	$(target_id).text("Loading...");
	
	if(context == "J"){
		target_id = '#j_loaded_data';
		search_term_id = "#search_term_J";
	}else if(context == "B"){
		target_id = '#b_loaded_data';
		search_term_id = "#search_term_B";
	}
		$.ajax({
		url : source + "?context="+ context,
		success : function(text) {
			//alert(target_id);
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
/**
 * 
 * @param id
 * @param target
 */
function selectId(id, target, name, target_name, context = null){
	var target_id = '#' + target;
	$(target_id).val(id);
	
	var target_name_id = '#' + target_name;
	$(target_name_id).val(name);
	
	$("#node_selected").html('"' + name + '" selected');
	
	// alert($(target_id).val());
	$('#modal1 .close').click();
	$('#global-notice').show();
	$('#global-notice').html('"' + name + '" selected');
	$('#global-notice').fadeOut(2500);
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
	return (tag[T.getDay()] + ", " + T.getDate() + ". " + monat[T.getMonth()]
			+ " " + T.getFullYear());
}

/*
 * 
 */
function uploadImages(url, target_id, redirectUrl) {

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
		var filename = p.name;

		// Load the image
		var reader = new FileReader();

		reader.onload = (function(p, pic_to_upload_resized, n, url, target_id,
				redirectUrl) {
			return function(e) {
				var contents = e.target.result;

				// alert('URL' + url);

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

											// alert('URL' + target_id);
											// alert('URL:::' + url);

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
													url, target_id, redirectUrl)
											// alert(n+'URL:::' + url);
										};

									})(p, pic_to_upload_resized, n, url,
											target_id, redirectUrl); // must
									// the
									// same

									image.src = contents;

								});
				;
			};

		})(p, pic_to_upload_resized, pic_to_upload.length, url, target_id,
				redirectUrl); // must the same

		reader.readAsDataURL(p);
	}

}

function isUploadImagesCompleted(pic_to_upload_resized, n, url, target_id,
		redirectUrl) {
	// alert('URL complete' + url);

	if (pic_to_upload_resized.length >= n) {
		// $('#myModal').modal(hide);

		/*
		 * var article_id = $("#article_id").val(); var redirectUrl =
		 * $("#redirectUrl").val();
		 */
		$.post(url, {
			target_id : target_id,
			pictures : pic_to_upload_resized,
		}, function(data, status, dataType) {
			 // alert(data);
			// alert(dataType);
			window.location = redirectUrl;
			// $('#global-notice').show();
			// $('#global-notice').text(data.message).fadeOut(3800);
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