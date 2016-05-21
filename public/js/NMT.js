/**
 * NMT
 */
function countdown(){
	
	//alert(id_array.length);
	
	for(var i=0; i < id_array.length; i++){
		
		var t =$('#'+id_array[i]+'_EDT').text();
		t=t.substring(0,10);
		
		//alert(t);
		var d= new Date( t);
		
		var obverver_obj = $('#'+id_array[i]+'_timer');
		var a = d.getTime();	
		setTimerObserver(timerSubject, obverver_obj, a);
	}
	
	
	//alert(a);
	//alert((timerSubject.observers.Count());
	
	
	if(timerSubject.observers.Count() > 0)
	{
		t = window.setInterval('setEventTimer(timerSubject)', 1000);
	}
	
}

/**
 *
 */
function setEventTimer(timerSubject)
{
	var today = new Date();
	var today_timestamp = today.getTime();
	//alert(today_timestamp);
	//alert(timerSubject.observers.Count());
	timerSubject.Notify(today_timestamp);	
	
	//t = setTimeout('setEventTimer()', 500);
}
 
function setTimerObserver(timerSubject, o, a)
{
	if(typeof o == 'object')
	{
		//vererben vom Observer
		inherits(new Observer(), o);
		
		//alert(o);
		
		o.Update = function(value)
		{
			if(a > value)
			{
				var t = a - value;
				var s_remain = getRemain(t);
				o.html(s_remain);
			}else if (a == value){
				o.text('Event beginnt now!');
			}else{
				o.html('<span style="color:#999999; text-decoration:line-through">due</span>');
			}
		}
		
		timerSubject.AddObserver(o);
		
	}
}
			

/**
*
*/
function getRemain(input)
{
	input = Math.round(input/1000);	// second
	
	var day_timestamp = 60 * 60 * 24;
	var hh_timestamp  = 60 * 60;
	var mm_timestamp  = 60;
	//d.setTime(input);
	var rest;
	var n_day = Math.floor(input/day_timestamp);
	var s_day = (n_day > 0) ?  (n_day) + 'd ' : '';
	
	rest = input - n_day * day_timestamp;
	var n_hh  = Math.floor(rest/hh_timestamp);
	var s_hh = (n_hh > 0) ? (n_hh) + 'h ' : '';
	
	rest = rest - n_hh * hh_timestamp;
	var n_mm  = Math.floor(rest/mm_timestamp);
	var s_mm = (n_mm > 0) ? (n_mm) + 'm ' : '';
	
	rest = rest - n_mm * mm_timestamp;
	var n_ss  = Math.floor(rest);
	
	//return ('<img src="/images/reminder.gif"/>&nbsp;' + s_day  + s_hh + s_mm + n_ss + 's');
	return ('<i class="icon-time"></i>&nbsp;'  + s_day  + s_hh + s_mm + n_ss + 's');
	//return (input);
}