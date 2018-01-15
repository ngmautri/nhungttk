/**
 *	@Package Calendar
 *	@Global function
 *
 
 /**
 *	Clock
 */
function startTime()
{
	var today=new Date();
	var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	// add a zero in front of numbers<10
	m=checkTime(m);
	s=checkTime(s);
	var s_today = DatumZeigen();
	$('#clock').html(s_today + "<br/>" + h+ ":" + m + ":"+s);
	t=setTimeout('startTime()',500);
}

function checkTime(i)
{
	if (i<10) 
	  {i="0" + i};
	  return i;
}

/**
 *	Date
 */
function DatumZeigen() {
	var T =new Date();
	var tag=new Array(7);
	tag[0]="Sonntag"
	tag[1]="Montag"
	tag[2]="Dienstag"
	tag[3]="Mittwoch"
	tag[4]="Donnerstag"
	tag[5]="Freitag"
	tag[6]="Samstag"
	var monat=new Array(12);
	monat[0]="Januar"
	monat[1]="Februar"
	monat[2]="März"
	monat[3]="April"
	monat[4]="Mai"
	monat[5]="Juni"
	monat[6]="Juli"
	monat[7]="August"
	monat[8]="September"
	monat[9]="Oktober"
	monat[10]="November"
	monat[11]="Dezember"
	return (tag[T.getDay()] + ", " + T.getDate() + ". " + monat[T.getMonth()] + " " + T.getFullYear());
}

/**
 *	getCurrentMonth, zero based
 */
function getCurrentMonth()
{
	return $('#nmt_current_month').text();
}

/**
 *	getCurrentYear
 */
function getCurrentYear()
{
	return $('#nmt_current_year').text();
}

/**
 *	======================
 *	helper for class Event
 *	by Nguyen Mau Tri
 *	======================
 */
 
 
/**
 * function
 * return end datetim in UNIX format
 */
function start_end(a, vl)
{
	var n=vl.length;
	var s_indicator=vl.substr(n-1,1);
	var s_dauer=vl.substr(0,n-1);
	var b;
	
	switch (s_indicator)
	{
		//Minute: 60*1000
		case  "m":								
			b= a + parseInt(s_dauer) * 60 * 1000;
			break;
		//Hours	
		case  "h":								
			b= a + parseInt(s_dauer) * 60 * 60 * 1000;
			break;
		//Day	
		case  "d":								
			b= a + parseInt(s_dauer) *  24 * 60 * 60 * 1000;
			break;
		//Week	
		case  "w":								
			b= a +  parseInt(s_dauer) * 7 * 24 * 60 * 60 * 1000;
			break;
	}
	
return b;

}

/**
 *	function toDate from Text:  30-01-2007
 *	return new Date
 */
function toDate(text)
{
	var a_text	= text.split("-");
	
	var s_year	= a_text[2];
	var s_month	= parseInt(a_text[1],10) - 1;
	var s_day	= a_text[0];
	var d_text	= new Date(s_year,s_month,s_day,0,0,0);
	return  d_text;
}

/**
 *	function toDateArray from Text: 30-01-2007
 *	return an Array
 */
function toDateArray(text)
{
	var a_text = text.split("-");
	return a_text;
}

/**
 *	function toDateString UNIX format
 *	return an string date for PHP
 */
function toDateString(b)
{
	var tmp_date = new Date();
	var tmp_day;
	var tmp_month;
	var tmp_year;
	var tmp_hh;
	var tmp_mm;
	var tmp_ss;
	tmp_date.setTime(b);
	tmp_day 	= tmp_date.getDate();
	tmp_month 	= parseInt(tmp_date.getMonth(), 10) + 1;
	tmp_year 	= tmp_date.getFullYear();
	tmp_hh 		= tmp_date.getHours();
	tmp_mm 		= tmp_date.getMinutes();
	tmp_ss 		= tmp_date.getSeconds();
	if (parseInt(tmp_month)<10) tmp_month 	= "0" + tmp_month;
	if (parseInt(tmp_day)<10) 	tmp_day 	= "0" + tmp_day;
	if (parseInt(tmp_hh)<10) 	tmp_hh 		= "0" + tmp_hh;
	if (parseInt(tmp_mm)<10) 	tmp_mm 		= "0" + tmp_mm;
	if (parseInt(tmp_ss)<10)	tmp_ss 		= "0" + tmp_ss;
	
return (tmp_year + "-" + tmp_month  +  "-" + tmp_day + " " + tmp_hh + ":" + tmp_mm + ":" + tmp_ss);	
}

/**
 *	function toDateString UNIX format
 *	return an string
 */
function UnixTimeToString(a)
{
	var tmp_date = new Date();
	tmp_date.setTime(a);
	
	var tmp_dd = tmp_date.getDate();
	var tmp_mm = tmp_date.getMonth();
	var tmp_yy = tmp_date.getFullYear();
	
	return (tmp_dd + "-" + tmp_mm + "-" + tmp_yy);
}
