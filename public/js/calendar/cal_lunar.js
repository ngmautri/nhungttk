/**
 *	Lunar calendar
 *	based on the jscript. 
 */

function add_lunar(){

	
	//get current month, year von cal_lunar.asp
	var yy= getCurrentYear();
	var mm= parseInt(getCurrentMonth()) + 1;
	var n_tagen = days(mm,yy);
	var DATE_SEPARATOR = "-";
	
	//convert to lunar day and add in kalender
	for(var k=1; k<= n_tagen; k++){
		var dd=k;
		lunar=getLunarDate(parseInt(dd), parseInt(mm),parseInt(yy));
		
		/*add in kalender*/
		var dd_solar=dd;
		var mm_solar=mm;
		var yy_solar=yy;
		if (dd_solar<10) dd_solar="0" + dd_solar;
		if (mm_solar<10) mm_solar="0"+ mm_solar;
					
		var s_id_solar=yy_solar + DATE_SEPARATOR + mm_solar + DATE_SEPARATOR + dd_solar + '_lunar';
		var elem=document.getElementById(s_id_solar);
		if (elem!=null){
			var dd_lunar=lunar.day;
			var mm_lunar=lunar.month;
			var yy_lunar=lunar.year;
			if (dd_lunar<10) dd_lunar="0" + dd_lunar;
			if (mm_lunar<10) mm_lunar="0" + mm_lunar;

			var s_id_tooltip=yy_lunar + DATE_SEPARATOR + mm_lunar + DATE_SEPARATOR +dd_lunar;   //20007/02/04_lunar
			var s_id_lunar=yy_lunar + DATE_SEPARATOR + mm_lunar + DATE_SEPARATOR + dd_lunar + "_lunar";   //02/04_lunar
			
			var args = lunar.day + "," + lunar.month + "," + lunar.year + "," + lunar.leap;
			args += ("," + lunar.jd + "," + dd_solar + "," + mm_solar + "," + yy_solar);
/*			var s_tooltip='<div class="tooltip2" id="'+ s_id_tooltip + '">';
			s_tooltip+='<div class="head" align="center">' + lunar.day + '. ' + lunar.month + ' \u00E2m l\u1ECBch\n</div>';
			s_tooltip+='<div class="content">'+ getDayName(lunar)+ '<hr size="1">click for more detail!</div></div>';
			var s_temp="'" + s_id_tooltip+ "'";
*/					
			if (dd==1||dd_lunar==1){
				var s_temp1="";
				s_temp1+=('<div id="'+ s_id_lunar + '">');
					s_temp1+=('<div class="lunar" title="Xem gio Hoang Dao"' );
					s_temp1+=(' onClick="alertDayInfo('+ args +');">');
					//s_temp1+=(' onMouseMove="showWMTT('+ s_temp +');"');
					//s_temp1+=(' onMouseOut="hideWMTT();">');
				//s_temp1+=(dd_lunar + '/' + mm_lunar + '/' + yy_lunar +'</div>');
				s_temp1+=(dd_lunar + '/' + mm_lunar + '</div>');
				s_temp1+=('</div>');
				//s_temp1+=s_tooltip;
				elem.innerHTML+=s_temp1;
			}else{
				var s_temp1="";
				s_temp1+=('<div id="'+ s_id_lunar + '">');
					s_temp1+=('<div class="lunar"  title="Xem gio Hoang Dao"' );
					s_temp1+=(' onClick="alertDayInfo('+ args +');">');
					//s_temp1+=(' onMouseMove="showWMTT('+ s_temp +');"');
					//s_temp1+=(' onMouseOut="hideWMTT();">');
					s_temp1+=(dd_lunar +'</div>');
				s_temp1+=('</div>');
				//s_temp1+=s_tooltip;
				elem.innerHTML+=s_temp1;
			}
			// Return für Event: date1, date2, und first date
			if (dd==1) var d1=yy_lunar + DATE_SEPARATOR + mm_lunar + DATE_SEPARATOR + dd_lunar;
			if (dd==n_tagen) var d2=yy_lunar + DATE_SEPARATOR + mm_lunar + DATE_SEPARATOR + dd_lunar;
			if (dd_lunar==1) var d_first=yy_lunar + DATE_SEPARATOR + mm_lunar + DATE_SEPARATOR + dd_lunar;
		}

	}
	
}



/**
 *	remove lunar function;
 */
function remove_lunar()
{
	$(".lunar").each( function(){$(this).remove();});	
}

/**
 *	days in month;
 */
function days(monat, jahr){
	var month_days=new Array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	month_days[2]=schaltjahr(jahr);
	return month_days[monat];
}

/**
 *	Schaltjahr
 */
function schaltjahr(j) {
	t = 28;
	if (j % 4 == 0) {
		t = 29;
		if (j % 100 == 0 && j % 400 != 0) t = 28;
	}
	return t;
}
