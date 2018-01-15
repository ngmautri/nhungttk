/**
 *	@Calendar
 *	@main program:
 */
$(document).ready(function(){
						   
		$('#indicator').hide();
		$('#indicator').ajaxStart(function() {
			  $(this).html('<img src="public/images/indicator6.gif"/> Loading... Please wait!')	
		      $(this).show();
     	}).ajaxStop(function() {
			  $(this).html('Done...')	
		      $(this).fadeOut();
	    });
		
		$.ajax({
		  url: "calendar/index/month/",
		  success: function(text){
				$("#calendar_div").html(text);
				if ($('input[name="toggle_lunar_calendar"]').val()=="1") add_lunar();
				
				var mm = getCurrentMonth();
				var yy = getCurrentYear();
				$.ajax({
				  type:"post",	   
				  data: {month:mm,year:yy },
				  url: "calendar/event/show/",
				  success: function(text){
					showEvent(text);
						$(function() {
							$('.event_link a').Tooltip({
								track: true,
								delay: 0,
								showURL: false,
								showBody: " - ",
								extraClass: "pretty",
								fixPNG: true,
								opacity: 100,
								left: -120
							});
						});
				    }
				});
		  }
		});
		
		$('input[name="toggle_lunar_calendar"]').click( function(){
			if($(this).val()=="1"){
				$(this).val("0");
				remove_lunar();
			}else{
				$(this).val("1");
				add_lunar();
			}
		});
		
	 $(function() {
		$('.helper').Tooltip({
		track: true,
		delay: 0,
		showURL: false,
		showBody: " - ",
		extraClass: "pretty",
		fixPNG: true,
		opacity: 100,
		left: -120
		});
	});

     
});

/* load calendar*/
function loadCalendar(url, div_id)
{
	$.ajax({
		url: url,
		success: function(text){
				$("#"  + div_id).html(text);
				if ($('input[name="toggle_lunar_calendar"]').val()=="1") add_lunar();
				
				var mm = getCurrentMonth();
				var yy = getCurrentYear();
				$.ajax({
				  type:"post",	   
				  data: {month: mm, year:yy},
				  url: "calendar/event/show/",
				  success: function(text){
				   	showEvent(text);
	
					$(function() {
					$('.event_link a').Tooltip({
					track: true,
					delay: 0,
					showURL: false,
					showBody: " - ",
					extraClass: "pretty",
					fixPNG: true,
					opacity: 1,
					left: -120
					});
					});

				  }
				});
		}
	});
}

/**
 *	Add event in Calendar
 *	JSon text input expected 	
 */
function showEvent(text)
{
	var obj = eval('(' + text + ')');
	var events = obj.events;
	var  eventsCount= events.length;
	var s_del;
	var s_edit='';
	var s_tooltip;
	var  text ='';
	var a_start
	var x;
	var d;
	
	if (eventsCount>0 && eventsCount <=1 )
	{
		$('#event_count').html( eventsCount + " event in  found!");
	}else if (eventsCount >1){
		$('#event_count').html( eventsCount + " events in  found!");
	}else{
		$('#event_count').html("No events found!");
	}

	
	for (var i=0; i<eventsCount; i++)
	{	
		var dd = '';
		var yy = '';
		var mm = '';
		
		var range 	= parseInt(events[i]["range"],10);
		var step 	= parseInt(events[i]["step"],10);
		var repeat 	= parseInt(events[i]["step"],10) * parseInt(events[i]["repeat"],10); 
		
		var s_freq;
		
		switch(events[i]["freq"])
		{
			case"0":
				s_freq = "- Single Event";
				break;
			case"1":
				s_freq = "- Daily Event";
				break;
			case"2":
				s_freq = "- Weekly Event";
				break;
			case"3":
				s_freq = "- Monthly Event";
				break;
			case"4":
				s_freq = "- Yearly Event";
				break;
		}
		
		
		for (j=0; j < repeat; j+=step)
		{	
			x = (events[i]["a"])*1000;
			x += j*24*60*60*1000;
			

			
			for (k=0; k < range; k++)
			{
				a_start = x;
				a_start+= k*24*60*60*1000;
				

				
				d = new Date(a_start);
				dd = d.getDate();
				mm = parseInt(d.getMonth(),10) + 1;			
				yy = d.getFullYear();
				

				if (mm<10) mm = "0" + mm ;
				if (dd<10) dd = "0" + dd ;
				
				var a = "#"  + yy + "-" + mm  + "-" +  dd;
				
				s_tooltip= dd + "-" + mm  + "-" +  yy +  ' - ';
				if (events[i]["t_start"]!="00:00"){
				s_tooltip += '- Time: '  + events[i]["t_start"] + '-'+ events[i]["t_end"] + '<br/>';
				}
				s_tooltip+= s_freq + '&nbsp;';
				if (events[i]["until"]!="01-01-1970"){
				s_tooltip += '- Until: '  + events[i]["until"];
				}
				
				s_tooltip += '<p><strong>' +  events[i]["summary"]  + '</strong></p>';
				
				var s_fn="'" + events[i]['event_id']  +"'";
				s_del = ('&nbsp;&nbsp;<img src="public/images/trash.gif" title="edit this event"');
				s_del+= (' onClick="deleteDialog(' + s_fn + ');"/>');
				
				s_edit = '<div class="event_link">';
				if (events[i]["t_start"]!="00:00"){
				/*
				s_edit += '<div class="event_time"><img src="public/images/clock3.gif" title="serial event"/>&nbsp;';
				s_edit += events[i]["t_start"] + '-'+ events[i]["t_end"] +  '</div>';
				*/
				s_edit += '<img src="public/images/clock3.gif"/>&nbsp;';
				}
				if (range>1 || parseInt(events[i]["freq"])> 1){
					s_edit += '<img src="public/images/recurrence.gif" title="serial event"/>&nbsp;';
				}
				
				
				//s_edit += '<a href="calendar/event/edit/id/' + events[i]['event_id'] + '" ';
	s_edit += '<a href="javascript:;" onclick="eventDialog(\'' + events[i]['event_id'] + '\',\'' +  events[i]['summary'] + '\');"';
				s_edit += 'title="' +  s_tooltip + '">';
				s_edit +=  events[i]["summary"] + '</a></div>';
							
				var a = "#"  + yy + "-" + mm  + "-" +  dd;
				
				if ($('input[name="toggle_cal_' + events[i]["cal_id"] + '"]').val()=="1")
				{	
					text ='<div class="cal_' + events[i]["cal_id"]  + '">';
				}else{
					text ='<div class="cal_' + events[i]["cal_id"]  + '" style="display:none">';
				}
				
				text +='<div class="event_bg_' +  events[i]["label"] + '" id="event_' + events[i]['event_id'] + '">';
				text+= s_edit  +  '</div></div>' 
				
				$(a).append(text);
			}
		}
	}
}
		
			

/**
 *	toggle Calendar
 */
function toggleCalendar(obj, calid)
{
	var s_class = "."  + calid;
	
	if($(obj).val() == "1"){
		$(obj).val("0");
		$(s_class).each(function(){$(this).fadeOut();});
	}else{
		$(obj).val("1");
		$(s_class).each(function(){$(this).fadeIn();});
	}
}


/**
 *	function on Delete modal
 */
 
function eventDialog(id, title)
{
	var s_text = "<div id=\"modal_title\">event editing</div>";
	s_text += "<h5>What do you want to do with event {<em>" +  title + "</em>} ?</h5>";
		s_text += '<ul>';
s_text += '<li><img src="public/images/edit.png"/>&nbsp;<a href="calendar/event/edit/id/' + id + '" >Edit this Event </a></li>';
s_text += '<li><img src="public/images/editdelete.png"/>&nbsp;<a href="javascript:;" class="modalClose"';
s_text += ' onclick="deleteDialog(\'' + id + '\',\'' + title + '\');">Delete this Event </a></li>';
s_text += '<li><img src="public/images/back.png"/>&nbsp;<a href="#" class="modalClose">Do Nothing</a></li>';

	s_text += '</ul>';
	
	$('#modal_data').html(s_text).modal({
		overlayId: 'modalOverlay',
		containerId: 'modalContainer_event',
		close: true});
}

function deleteDialog(id, title)
{
	if (confirm('Do you really want to delete this event {' +  title+  '} ?' ))
	{
		deleteEvent(id);
	}
}

/**
 *	delete Event
 */
function deleteEvent(id)
{
var url="calendar/event/delete/";
	$.post(url, {id:id}, function(){
					 			    var e = "#event_" + id;
									$(e).fadeOut(function(){$(this).remove()});
								    });
}


 

