<?php

namespace Calendar\Service;


/**
 * 
 * @author nmt
 *
 */
class CalendarService
{
	
	/**
	 *	draw calendar
	 *	@public
	 *	return string
	 */
	public function drawMonthCal($mm=null, $yy=null, $base_url)
	{
		$calendar_url =  $base_url . '/calendar/index/month/';
		
		$date = date("d");
		$month = date("n")-1;
		$year = date("Y");
		$showcalendar = '<div style="height: 500px"><table style="height:100%;width=100%"><trstyle="width=10%">';
		$sevendaysaweek = 0;
		
		if ($mm==null) {
			$mm = $month;
		}
		
		if ($yy==null) {
			$yy = $year;
		}
		
		if ($mm<0){
			$mm = 11;
			$yy -= 1;
		}
		
		if ($mm>11){
			$mm = 0;
			$yy += 1;
		}
		
		if (($mm != $month) || ($yy != $year)){
			$today= 'onclick = "loadCalendar(\'' . $calendar_url .  '\', \'calendar_div\');"';
			$today= '<a href="javascript:;" ' . $today . '><img src="'. $base_url . '/public/images/today.png"/></a>';
		}else {
			$today = '';
		}
		
		$next_url = $calendar_url . '?month=' . ($mm+1) . '&year=' . $yy;
		$previous_url = $calendar_url . '?month=' . ($mm-1) . '&year=' . $yy;
		
		$next= 'onclick = "loadCalendar(\'' . $next_url .  '\', \'calendar_div\');"';
		$previous= 'onclick = "loadCalendar(\'' . $previous_url . '\', \'calendar_div\');"';
		$next= 		'<a href="javascript:;" ' . $next . '><img src="'. $base_url . '/public/images/next1.png"/></a>';
		$previous= 	'<a href="javascript:;" ' . $previous . '><img src="'. $base_url . '/public/images/previous1.png"/></a>';
		$nav = '<div id="calendar_nav">' . $today . '&nbsp;' .  $previous . '&nbsp;' . $next . '</div>';
		
		
		$begin = mktime(0,0,0,($mm+1),1,$yy);
		$firstday = date("w",$begin)-1;
		$dayspermonth = date("t",$begin);
		if ($firstday < 0){
			$firstday = 6;
		}
		
		$dayname = array("Mo.","Di.","Mi.","Do.","Fr.","Sa.","So.");
		$monthname = array("Januar","Februar","M&auml;rz","April","Mai",
				"Juni","Juli","August","September","Oktober","November","Dezember");
		
		$calendar_header = '<div class="calendar_header">' . $monthname[$mm] . ' ' . $yy .'</div>';
		
		if (($mm != $month) || ($yy != $year)){
			$today = '';
		}
		else {
			$today = $date. '.';
		}
		
		
		for ($i=0; $i<count($dayname); $i++){
			$showcalendar .= "<th class='days'>".$dayname[$i]."</th>";
		}
		$showcalendar .= "</tr><tr>";
		
		for ($i=0; $i<$firstday; $i++){
			$showcalendar .= "<td>&nbsp;</td>";
			$sevendaysaweek++;
		}
		
		for ($i=1; $i<=$dayspermonth; $i++){
			
			if ($i<10)
			{
				$tmp_dd ='0' . $i;
			}else{
				$tmp_dd = $i;
			}
			
			if (((int)$mm + 1)<10)
			{
				$tmp_mm='0' . (string)($mm +1);
			}else{
				$tmp_mm = (string)($mm+1);
			}
			
			$id	= $yy . '-' . $tmp_mm . '-' . $tmp_dd;
			$id_lunar = $yy . '-' . $tmp_mm . '-' . $tmp_dd . '_lunar';
			
			if (count($dayname) == $sevendaysaweek){
				if ($i==$date && $mm==$month && $yy==$year)
				{
					$showcalendar .= '</tr><tr><td class="today"><div id="' . $id .'">';
					$showcalendar .= '<div><div class="solar_div today">' . $i . ' (Today)</div>';
					$showcalendar .= '<div class="lunar_div" id="' . $id_lunar . '"></div></div></div></td>';
					
				}else{
					$showcalendar .= '</tr><tr><td><div id="'. $id .'"><div><div class="solar_div">' . $i . '</div>';
					$showcalendar .= '<div class="lunar_div" id="' . $id_lunar . '"></div></div></div></td>';
					
				}
				$sevendaysaweek = 0;
			}
			else{
				if ($i==$date && $mm==$month && $yy==$year)
				{
					$showcalendar .= '<td class="today"><div id="' . $id . '"><div><div class="solar_div today">' . $i . ' (Today)</div>';
					$showcalendar .= '<div class="lunar_div" id="' . $id_lunar . '"></div></div></div></td>';
				}else{
					$showcalendar .= '<td><div id="' . $id . '"><div><div  class="solar_div">' . $i . '</div>';
					$showcalendar .= '<div class="lunar_div" id="' . $id_lunar . '"></div></div></div></td>';
				}
				
			}
			$sevendaysaweek++;
		}
		
		for ($i=$sevendaysaweek; $i<count($dayname); $i++){
			$showcalendar .= "<td>&nbsp;</td>";
		}
		$showcalendar .= "</tr></table></div>";
		return $calendar_header . $nav . $showcalendar;
	}
	
	/**
	 *	draw calendar
	 *	@public
	 *	return string
	 */
	public function createMonthView($mm=null, $yy=null, $base_url)
	{
	    $calendar_url =  $base_url . '/calendar/index/month/';
	    
	    $date = date("d");
	    $month = date("n")-1;
	    $year = date("Y");
	    $showcalendar = '';
	    $sevendaysaweek = 0;
	    
	    if ($mm==null) {
	        $mm = $month;
	    }
	    
	    if ($yy==null) {
	        $yy = $year;
	    }
	    
	    if ($mm<0){
	        $mm = 11;
	        $yy -= 1;
	    }
	    
	    if ($mm>11){
	        $mm = 0;
	        $yy += 1;
	    }
	    
	    
	    $begin = mktime(0,0,0,($mm+1),1,$yy);
	    $firstday = date("w",$begin)-1;
	    $dayspermonth = date("t",$begin);
	    if ($firstday < 0){
	        $firstday = 6;
	    }
	    
	    //echo $firstday;
	    
	    $dayname = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
	    
	    $showcalendar='<div id="calendar">';
	    $showcalendar.='<ul class="weekdays">';
	    
	    for ($i=0; $i<count($dayname); $i++){
	        $showcalendar .= '<li>'.$dayname[$i].'</li>';
	    }
	    $showcalendar .= "</ul>";
	    
	       
	    $showcalendar .= '<ul class="days">';
	    
	    for ($i=0; $i<$firstday; $i++){
	        $showcalendar .= '<li class="day other-month">&nbsp;</li>';
	        $sevendaysaweek++;
	    }
	    
	    for ($i=1; $i<=$dayspermonth; $i++){
	        
	        if ($i<10)
	        {
	            $tmp_dd ='0' . $i;
	        }else{
	            $tmp_dd = $i;
	        }
	        
	        if (((int)$mm + 1)<10)
	        {
	            $tmp_mm='0' . (string)($mm +1);
	        }else{
	            $tmp_mm = (string)($mm+1);
	        }
	        
	        // create id
	        $id	= $yy . '-' . $tmp_mm . '-' . $tmp_dd;
	        $id_lunar = $yy . '-' . $tmp_mm . '-' . $tmp_dd . '_lunar';
	        
	        if (count($dayname) == $sevendaysaweek){
	            if ($i==$date && $mm==$month && $yy==$year)
	            {
	                $showcalendar .= '</ul><ul class="days"><li class="day today"><div class="date" id="' . $id .'">';
	                $showcalendar .= '<div><div class="solar_div">' . $i . ' (Today)</div>';
	                $showcalendar .= '<div class="lunar_div" id="' . $id_lunar . '"></div></div></div></li>';
	                
	            }else{
	                $showcalendar .= '</ul><ul class="days"><li class="day"><div class="date" id="'. $id .'"><div><div class="solar_div">' . $i . '</div>';
	                $showcalendar .= '<div class="lunar_div" id="' . $id_lunar . '"></div></div></div></li>';
	                
	            }
	            $sevendaysaweek = 0;
	        }
	        else{
	            if ($i==$date && $mm==$month && $yy==$year)
	            {
	                $showcalendar .= '<li class="day today"><div class="date" id="' . $id . '"><div><div class="solar_div">' . $i . ' (Today)</div>';
	                $showcalendar .= '<div class="lunar_div" id="' . $id_lunar . '"></div></div></div></li>';
	            }else{
	                $showcalendar .= '<li class="day"><div class="date" id="' . $id . '"><div><div  class="solar_div">' . $i . '</div>';
	                $showcalendar .= '<div class="lunar_div" id="' . $id_lunar . '"></div></div></div></li>';
	            }
	            
	        }
	        $sevendaysaweek++;
	    }
	    
	    for ($i=$sevendaysaweek; $i<count($dayname); $i++){
	        $showcalendar .= '<li class="day other-month">&nbsp;</li>';
	    }
	    $showcalendar .= "</ul></div>";
	    
	    //echo $showcalendar;
	    return $showcalendar;
	}
}



?>