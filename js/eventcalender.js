var j = jQuery.noConflict();
var cal_flag = false;

//!< カレンダー
function e2m_cal(year,month,day) {
	 today=new Date();
	 var calendar = "<table id=\"e2m_event_cal\">";
	 if (!year) var year=today.getFullYear();
	 if (!month) var month=today.getMonth();
	 else month--;
	 if (!day) var day=today.getDate();
	 var leap_year=false;
	 if ((year%4 == 0 && year%100 != 0) || (year%400 == 0)) leap_year=true;
	 lom=new Array(31,28+leap_year,31,30,31,30,31,31,30,31,30,31);
	 dow=new Array("SUN","MON","TUE","WED","THU","FRI","SAT");
	 var days=0;
	 for (var i=0; i < month; i++) days+=lom[i];
	 var week=Math.floor((year*365.2425+days)%7);
	 var j=0;
	 var when=year+"年 "+(month+1)+"月";
	 aki = 0;
	 var cnt = 0;
	 for (i=0; i < week; i++,j++) {
	 	aki++;
	 	if(i == 0){
	 		calendar+="<td class=\"holiday\"></td>\n";
	 	}
	 	else{
	 		calendar+="<td class=\"no\"></td>\n";
	 	}
	 }
	 for (i=1; i <= lom[month]; i++) {
		if(cnt == 0){
			if(dow[j] == "SUN" || dow[j] == "SAT"){
				calendar+="<td class=\"yes fst holiday\"><span class=\"cf "+i+"\">"+i+"</span></td>\n";
			}
			else{
				calendar+="<td class=\"yes fst\"><span class=\"cf "+i+"\">"+i+"</span></td>\n";
			}	
		}
		else{
			if(dow[j] == "SUN" || dow[j] == "SAT"){
				calendar+="<td class=\"yes holiday\"><span class=\"cf "+i+"\">"+i+"</span></td>\n";
			}
			else{
				calendar+="<td class=\"yes\"><span class=\"cf "+i+"\">"+i+"</span></td>\n";
			}
		}
		j++;
		cnt++;
		if (j > 6) {
			j=0;
		}
	 }
	 //alert(aki + ":" + cnt + ":" + j);
	 for (i=0; i < 42-(aki+cnt); i++) {
	 	if(dow[j] == "SUN" || dow[j] == "SAT"){
	 		calendar+="<td class=\"no holiday\"></td>\n";
		}
		else{
	 		calendar+="<td class=\"no\"></td>\n";
		}
		j++;
		if (j > 6) {
			j=0;
		}
	 }
	 calendar+="</table>";
	 return calendar;
}


j(function() {
	today=new Date();
	year=today.getFullYear();
 	month=today.getMonth()+1;
 	
	j("#e2m_event_cal").replaceWith(function(i) {
	    return e2m_cal(year,month,1);
	});
	cal_flag = true;
	e2m_setEvent(year,month);
	adjustmentMonth();

	//!< 前後の月をクリックした時の動作
	j(".e2m_calender .nextmonth").click(function(){
		month = plusMonth(month);
		adjustmentMonth();
		cal_flag = true;
     	j("#e2m_event_cal").replaceWith(function() {
	    	return e2m_cal(year,month,1);
		});
		e2m_setEvent(year,month);
		return false;
     })
     j(".e2m_calender .prevmonth").click(function(){
		month = minusMonth(month);
		adjustmentMonth();
		cal_flag = true;
     	j("#e2m_event_cal").replaceWith(function() {
	    	return e2m_cal(year,month,1);
		});
		e2m_setEvent(year,month);
		return false;
     })
});
function plusMonth(value){
	value++;
	if(value > 12){
		value = 1;
		year++;
	}
	return value;
}
function minusMonth(value){
	value--;
	if(value < 1){
		value = 12;
		year--;
	}
	return value;
}
//!< 月の変更時に表示を置き換える
function adjustmentMonth(){
	var prev_month = month-1;
	var next_month = month+1;
	if(prev_month < 1){
		prev_month = 12;
	}
	if(next_month > 12){
		next_month = 1;
	}
	j(".e2m_calender .month .prevmonth").html("<a href=\"\">&lt;&lt;" + prev_month + "月</a>");
	j(".e2m_calender .month .currentmonth").html(month + "月");
	j(".e2m_calender .month .nextmonth").html("<a href=\"\">" + next_month + "月&gt;&gt;</a>");
	j(".e2m_calender .cal_year").html("<p class=\"cal_year\">"+year+"年</p>");
}