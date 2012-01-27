<?php
/*
Plugin Name: Easy Event Manager
Version: 0.8.1
Plugin URI: http://web.lugn-design.com
Author: Halt
Author URI: http://web.lugn-design.com
Description: Easy to manage for event calendar.
*/

//!< データベースの接頭語
$plugin_db = "_easy_manage_event_plugin_";

//!< 管理メニューのアクションフック
add_action('admin_menu', 'admin_menu_easy_manage_event');

//!< アクションフックのコールバッック関数
function admin_menu_easy_manage_event () {
    // 設定メニュー下にサブメニューを追加
	add_options_page('Event Manager', 'Event Manager', 'level_8', __FILE__, 'easy_manage_event');
}

//!< CSSの読み込み
function wp_custom_admin_Lib() {
		$plugin_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL) : WP_PLUGIN_URL;
		$plugin_url .= "/EasyEventManager/";
	?>
	<link type="text/css" href="<?php echo $plugin_url; ?>css/style.css" rel="stylesheet" />
	<link type="text/css" href="<?php echo $plugin_url; ?>css/ui-lightness/jquery-ui-1.8.17.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo $plugin_url; ?>js/jquery-ui-1.8.17.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo $plugin_url; ?>js/script.js"></script>
	<script type="text/javascript" src="<?php echo $plugin_url; ?>js/sort/jquery.tablesorter.js"></script>
	<script type="text/javascript">
		var j = jQuery.noConflict();
		j(function() {
	    <?php
			$total_event = e2m_getTotalEvent();

    		for($i = 0; $i < $total_event; $i++){
    			echo "j(\"#datepicker".$i."\").datepicker({dateFormat: 'yy/mm/dd'});\n";
    		}
		?>
			j("#changetable").tablesorter({
			});
			j('#tabs').tabs({
				selected: 0,
				fx: { opacity: 'toggle',duration: 300 }
			});
		});
	</script>
	<?php
}
add_action('admin_head', 'wp_custom_admin_Lib', 100);

add_action('wp_head', 'header_css_style');

function header_css_style(){ 
	$plugin_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL) : WP_PLUGIN_URL;
	$plugin_url .= "/EasyEventManager/";
		?>
	<link type="text/css" href="<?php echo $plugin_url; ?>css/calendar.css" rel="stylesheet" />
	<?php
}

//!< プラグインページのコンテンツを表示
function easy_manage_event () {
	global $plugin_db;
	$plugin_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL) : WP_PLUGIN_URL;
	$plugin_url .= "/EasyEventManager/";
?>
    <div class="wrap">
        <h2>Easy Event Manager</h2>

        <?php
	        if ( isset($_POST['delete_event']) && isset($_POST['delete']) ) {
	        	confirmDeleteEvent();
		    }
		    else{ 
	        	undercard();
		    ?>
		    	<div id="tabs">
					<ul>
						<li><a href="#tabs-1">Event Manager</a></li>
						<li><a href="<?php echo $plugin_url; ?>document.html">Documentation</a></li>
					</ul>
					<div id="tabs-1">
						<?php 
				        	addEvent();
				        	changeEvent();
				        	deleteEvent(); 
	        				//e2m_showDebugData();	// デバッグ用
				        ?>
					</div>
				</div>
	        <?php } ?>
    </div>
<?php
}

//!< 日付変更用の関数
function changeEvent(){
	global $plugin_db; ?>
	<div class="update_event" id="update_event">
	<h3>内容の変更</h3>
	<a href="" id="prev">←</a>
	<span id="current_page">1</span> / <span id="total_page">0</span>
	<a href="" id="next">→</a>
	<select id="foo" name="foo">
		<option value="10" selected="selected">10</option>
		<option value="25">25</option>
		<option value="50">50</option>
		<option value="100">100</option>
	</select>件表示
	<form method="post" action="options.php">
	    <?php
	    	wp_nonce_field('update-options');
			$total_event = e2m_getTotalEvent();
			$sort_data = e2m_getEventData();
	    ?>
	    <table class="widefat" id="changetable">
	    <thead>
	        <tr class="thead" valign="top">
	            <th scope="row">No.</th>
	            <th scope="row">日付</th>
	            <th scope="row">タイトル</th>
	            <th scope="row">URL</th>
	            <th scope="row">その他</th>
	        </tr>
	    </thead>
	    <tbody>
	    <?php
		    for($i = $total_event-1; $i >= 0; $i--){
		    	$data = $sort_data[$i];
			    $title_value = $data["title"];
			    $url_value = $data["url"];
			    $other_value = $data["other"];
			    $date_value = $data["date"];
		    ?>
	        <tr class="thead" valign="top">
	            <td scope="row"><?php echo addZero($i); ?> : </th>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>date<?php echo $i; ?>" value="<?php echo $date_value; ?>" id="datepicker<?php echo $i; ?>" /><span><?php echo $date_value; ?></span></td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>title<?php echo $i; ?>" value="<?php echo $title_value; ?>" /><span><?php echo $title_value; ?></span></td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>url<?php echo $i; ?>" value="<?php echo $url_value; ?>" /><span><?php echo $url_value; ?></span></td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>other<?php echo $i; ?>" value="<?php echo $other_value; ?>" /><span><?php echo $other_value; ?></span></td>
	        </tr>
	    <?php } ?>
	    </tbody>
	    </table>
	    <input type="hidden" name="action" value="update" />
	    <input type="hidden" name="<?php echo $plugin_db; ?>total_event" value="<?php echo $total_event; ?>" />
	    <?php
	    	echo "<input type=\"hidden\" name=\"page_options\" value=\"";
		    for($i = 0; $i < $total_event; $i++){
		    	if($i!=0){
		    		$fst=",";
		    	}
		    	echo $fst.$plugin_db."title".$i.",".$plugin_db."url".$i.",".$plugin_db."other".$i.",".$plugin_db."date".$i;
		    	if($i <= $total_event){
		    		echo ",".$plugin_db."total_event";
		    	}
			}
			echo "\" />";
		?>
	    <p class="submit">
	        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	    </p>
	</form>
	</div>
<?php
}

//!< 日付追加用の関数
function addEvent(){
	global $plugin_db; ?>
	<div class="add_event">
	<form method="post" action="options.php">
	 	<?php
	    	wp_nonce_field('update-options');
			$total_event = e2m_getTotalEvent();
	    ?>
	    <h3>新規追加</h3>
	    <table class="widefat">
	    <thead>
	        <tr class="thead" valign="top">
	            <th scope="row">日付</th>
	            <th scope="row">タイトル</th>
	            <th scope="row">URL</th>
	            <th scope="row">その他</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr valign="top">
	            <td><input id="datepicker" class="text_input" type="text" name="<?php echo $plugin_db; ?>date<?php echo $total_event; ?>" /></td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>title<?php echo $total_event; ?>" /></td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>url<?php echo $total_event; ?>" /></td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>other<?php echo $total_event; ?>" /></td>
	        </tr>
	    <tbody>
	    </table>
	    <?php
	    	$total_event++;
	    	$add_num = $total_event-1;
    	?>
	    <p class="submit">
	    	<input type="hidden" name="action" value="update" />
	    	<input type="hidden" name="<?php echo $plugin_db; ?>total_event" value="<?php echo $total_event; ?>" />
	    	<input type="hidden" name="page_options" value="<?php echo $plugin_db; ?>total_event,<?php echo $plugin_db ?>title<?php echo $add_num ?>,<?php echo $plugin_db ?>url<?php echo $add_num ?>,<?php echo $plugin_db ?>other<?php echo $add_num ?>,<?php echo $plugin_db ?>date<?php echo $add_num ?>" />
	        <input type="submit" class="button-primary" value="<?php _e('イベントを追加') ?>" />
	    </p>
	</form>
	</div>
<?php
}

//!< 日付削除用の関数
function deleteEvent(){
	global $plugin_db;
	?>
	<div class="delete_event">
	<h3>削除</h3><label><input type="checkbox" id="all" />全選択</label>
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" style="width:200px;" >
     	<?php
        	wp_nonce_field('update-options');
			$total_event = e2m_getTotalEvent();
			$event_data = e2m_getEventData2();
			$sort_data = e2m_sortData();																																									
			$trnum = 20;
        ?>
	    	<div id="check">
	    <table class="widefat">
	    <thead>
	        <tr class="thead">
	        	<th scope="row" colspan="<?php echo $trnum; ?>">削除</th>
	        </tr>
	    </thead>
	    <tbody>
    		<?php
	    	for($i = 0; $i < $total_event; $i++){ 
	    		if($i % $trnum == 0){ echo "<tr>"; }
	    	?>
	        		<td>No. <?php echo addZero($i); ?><input type="checkbox" name="delete[]" value="<?php echo $i ?>" /></td>
	        <?php if($i % $trnum == ($trnum-1) || $i == $total_event-1){ echo "</tr>"; } ?>
    		<?php } ?>
       	</tbody>
        </table>
        	</div>
        <p class="submit">
        	<input type="hidden" name="action" value="update" />
	    	<input type="hidden" name="<?php echo $plugin_db; ?>total_event" value="<?php echo $total_event; ?>" />
	    	<input type="hidden" name="delete_event" value="delete_event" />
            <input type="submit" class="button-primary" value="<?php _e('Delete') ?>" />
        </p>
    </form>
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" >
    	<h3>期間が過ぎたイベントを全て削除</h3>
        <p class="submit">
        	<input type="hidden" name="action" value="update" />
	    	<input type="hidden" name="delete_event" value="delete_event" />
	    	<input type="hidden" name="delete" value="all_delete" />
            <input type="submit" class="button-primary" value="<?php _e('過去のイベントを削除') ?>" />
        </p>
    </form>
    </div>
<?php
}

function confirmDeleteEvent(){
	global $plugin_db;
	?>
	<div class="delete_event">
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" >
		<h3>削除</h3>
     	<?php
        	wp_nonce_field('update-options');
        	if ( $_POST['delete'] == "all_delete" ) {
        		echo "<p>以下のイベントを削除します。</p>";
        		$now = date('Y/m/d');
				$total_event = e2m_getTotalEvent();
				$data = e2m_getEventData();
	    		for($i = 0; $i < $total_event; $i++){
		    		$tmp = $data[$i];
	    			if($now > $tmp["date"]){
		        		$delete_item .= "No.".$i. " - " .$tmp["date"]. " - " .$tmp["title"]. " - " .$tmp["url"]. " - " .$tmp["other"]. "<br />";
		        		echo "<input type=\"hidden\" name=\"delete[]\" value=\"".$i."\" />";
	    			}
	    		}
		        echo "<p>".$delete_item."<br />この操作は取り消せません。<br />削除してもよろしいですか。</p>";
        	}
        	else if (isset($_POST['delete'])) {
		        $delIds = $_POST['delete'];
        		echo "<p>以下のイベントを削除します。</p>";
		        foreach ($delIds as $delId) { 
		        	$delete_item .= "No.".$delId. " - " .get_option( $plugin_db.'date'.$delId ). " - " .get_option( $plugin_db.'title'.$delId ). " - " .get_option( $plugin_db.'url'.$delId ). " - " .get_option( $plugin_db.'other'.$delId ). "<br />";
		        ?>
		        	<input type="hidden" name="delete[]" value="<?php echo $delId; ?>" />
		        <?php }
		        echo "<p>".$delete_item."<br />この操作は取り消せません。<br />削除してもよろしいですか。</p>";
		    }
        ?>
        <p class="submit">
        	<input type="hidden" name="action" value="update" />
	    	<input type="hidden" name="<?php echo $plugin_db; ?>total_event" value="<?php echo $total_event; ?>" />
            <input type="submit" class="button-primary" value="<?php _e('Delete') ?>" />
        </p>
    </form>
    </div>
<?php
}

//!< イベント更新や削除などの処理が発生した場合の処理
function undercard(){
	global $plugin_db;
	$delete_cnt = 0;
	if (isset($_POST['delete'])) {
        $delIds = $_POST['delete'];
    }
    if (isset($delIds)) {
        foreach ($delIds as $delId) {
        	$delete_item .= "No.".$delId. " - " .get_option( $plugin_db.'date'.$delId ). " - " .get_option( $plugin_db.'title'.$delId ). " - " .get_option( $plugin_db.'url'.$delId ). " - " .get_option( $plugin_db.'other'.$delId ). "<br />";
            delete_option($plugin_db.'title'.$delId);
            delete_option($plugin_db.'url'.$delId);
            delete_option($plugin_db.'other'.$delId);
            delete_option($plugin_db.'date'.$delId);
            $delete_cnt++;
        }
	    ?>
	    <div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php echo $delete_item . "を削除しました。"; ?></strong></p>
		</div>
	    <?php
    }

    //!< 削除したイベントを詰める。
    arrangementEvent();
}

//!< イベント削除後の順整理
function arrangementEvent(){
	global $plugin_db;
	$event_data = e2m_getEventData2();
	$ary = array_merge($event_data);

    $event_cnt = 0;
    $total_event = count($ary['year']);

    for($i = 0; $i < $total_event; $i++){
   		update_option($plugin_db.'title'.$i, $event_data['title'][$i]);
   		update_option($plugin_db.'url'.$i, $event_data['url'][$i]);
   		update_option($plugin_db.'other'.$i, $event_data['other'][$i]);
   		update_option($plugin_db.'date'.$i, $event_data['date'][$i]);
   		$event_cnt++;
    }
    update_option( $plugin_db.'total_event', $event_cnt );
}

//!< プラグインを削除する際に行うオプションの削除
if ( function_exists('register_uninstall_hook') ) {
    register_uninstall_hook(__FILE__, 'uninstall_hook_easy_manage_event');
}
function uninstall_hook_easy_manage_event () {
	global $plugin_db;
    $total_event = e2m_getTotalEvent();

    for($i = 0; $i < $total_event; $i++){
   		delete_option($plugin_db.'title'.$i);
   		delete_option($plugin_db.'url'.$i);
   		delete_option($plugin_db.'other'.$i);
   		delete_option($plugin_db.'date'.$i);
    }
}

//!< 表示用関数
function e2m_showDebugData(){
	global $plugin_db;
	$total_event = e2m_getTotalEvent();

	echo "<div class=\"show_event\">";

	echo "<h3>データの表示</h3>";

	echo "合計イベント数 : ".$total_event;
	$event_data = e2m_getEventData2();

	echo "<pre>";
	print_r($event_data);
	echo "</pre>";

	echo "sorted <br />";
	$sort_data = e2m_sortData();

	foreach ($sort_data as $key => $row) {
	    echo $row['year'] . " : ";
	    echo $row['month'] . " : ";
	    echo $row['days'] . " : ";
	    echo $row['week'] . " : ";
	    echo $row['title'] . " : ";
	    echo $row['url'] . " : ";
	    echo $row['other'] . " : ";
	    echo $row['date'] . "<br />";
	}

    echo "</div>";
}

//!< イベントの総数を返す
function e2m_getTotalEvent(){
	global $plugin_db;

	if(get_option($plugin_db.'total_event') != 0){
		return get_option($plugin_db.'total_event');
	}

	return 0;
}

//!< イベントの情報を配列で返す
function e2m_getEventData2(){
	global $plugin_db;
	$total_event = e2m_getTotalEvent();
	$weekjp_array = array('日', '月', '火', '水', '木', '金', '土');
    $event_cnt = 0;

    for($i = 0; $i < $total_event; $i++){
    	if( get_option( $plugin_db.'title'.$i ) != '' || get_option( $plugin_db.'url'.$i ) != '' || get_option( $plugin_db.'other'.$i ) != '' || get_option( $plugin_db.'date'.$i ) != '' || get_option( $plugin_db.'week'.$i ) != ''){
    		$event_title[$i] = get_option( $plugin_db.'title'.$i );
    		$event_url[$i] = get_option( $plugin_db.'url'.$i );
    		$event_other[$i] = get_option( $plugin_db.'other'.$i );
    		$event_date[$i] = get_option( $plugin_db.'date'.$i );


			$pieces = explode("/", $event_date[$i]);
    		$event_year[$i] = $pieces[0];
    		$event_month[$i] = $pieces[1];
    		$event_day[$i] = $pieces[2];
    		$event_week[$i] = $weekjp_array[date('w', mktime(0, 0, 0, $event_month[$i], $event_day[$i], $event_year[$i]))];
    		$event_cnt++;
    	}
    }

    if($event_cnt == 0){
    	$event_year[0] = 0;
    	$event_month[0] = 0;
    	$event_day[0] = 0;
    	$event_title[0] = 0;
    	$event_url[0] = 0;
    	$event_other[0] = 0;
    	$event_date[0] = 0;
    	$event_week[0] = 0;
    }

    $event_data["year"] = array_merge($event_year);
    $event_data["month"] = array_merge($event_month);
    $event_data["days"] = array_merge($event_day);
    $event_data["week"] = array_merge($event_week);
    $event_data["title"] = array_merge($event_title);
    $event_data["url"] = array_merge($event_url);
    $event_data["other"] = array_merge($event_other);
    $event_data["date"] = array_merge($event_date);

    return $event_data;
}

//!< イベントの情報を配列で返す
function e2m_getEventData(){
	global $plugin_db;
	$total_event = e2m_getTotalEvent();
	$data = e2m_getEventData2();

	for($i = 0; $i < $total_event; $i++){
    		$event_data[$i]["year"] = $data["year"][$i];
    		$event_data[$i]["month"] = $data["month"][$i];
    		$event_data[$i]["days"] = $data["days"][$i];
    		$event_data[$i]["week"] = $data["week"][$i];
    		$event_data[$i]["title"] = $data["title"][$i];
    		$event_data[$i]["url"] = $data["url"][$i];
    		$event_data[$i]["other"] = $data["other"][$i];
    		$event_data[$i]["date"] = $data["date"][$i];
	}

    return $event_data;
}

//!< 表示用関数
//!< リスト形式
function e2m_showList($num, $same_month) {
	$data = e2m_sortData();
	$total_event = e2m_getTotalEvent();
	
	$year = date("Y");
	$month = date("m");
	$day = date("d");
	
	$loop = $total_event;
	if($num > 0){
		$loop = $num;
	}
	
	for ($i = 0,$itr = 0; $itr < $loop; $i++) {
		$view = 1;
		$row = $data[$i];
		if($same_month == true){
			if($year == $row["year"]){
				if($month == $row["month"]){
					$itr++;
					$view = 0;
				}
			}
		}
		else{
			$view = 0;
			$itr++;
		}
		if($view == 0){
			echo "<li><span class=\"e2m_date\">". $row['date'] ."/". $row['week'] ."</span> <a class=\"e2m_link\" href=\"".$row['url']."\">".$row['title']."</a></li>";
		}
		
			//!< 最大数よりもループ数が多くなったらbreak
		if($total_event <= $i+1){
			break;
		}
	}
}

//!< カレンダー形式
function e2m_showCalendar() {
	$plugin_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL) : WP_PLUGIN_URL;
	$plugin_url .= "/EasyEventManager/";
	$sort_data = e2m_sortData();
	?>
	<script type="text/javascript" src="<?php echo $plugin_url; ?>js/eventcalender.js"></script>
	<?php e2m_cal(); ?>
    <script type="text/javascript">
    var year = 200;
    var month = 10;
    var aki = 0;
    var e2m_data = [
    	<?php 
    	$js_data = array();
    	foreach ($sort_data as $key => $value) {
    		$js_data[] = "['".$value["title"]."', ".$value["year"].", ".deleteZero($value["month"]).", ".deleteZero($value["days"]).", '".$value["date"]."', '".$value["url"]."'],"; 
    	}
    	$js_num = count($js_data)-1;
    	$js_data[$js_num] = rtrim($js_data[$js_num], ",");
    	foreach ($js_data as $key => $value) {
    		echo $value;
    	}
    	?>
    ];
    //!< 登録した日付をカレンダーに表示していく
    function e2m_setEvent(year,month){
    	if(cal_flag == true){
    		var data_num = <?php echo e2m_getTotalEvent(); ?>;
    		for (var i = 0; i < data_num; i++) {
    			var tgt_title = e2m_data[i][0];
				var tgt_date = e2m_data[i][4];
				var tgt_url = e2m_data[i][5];
				var tgt_year = e2m_data[i][1];
				var tgt_month = e2m_data[i][2];
				var tgt_day = e2m_data[i][3];
				//j("#e2m_event_cal td").removeClass("event_day");
    			if(year == tgt_year){
					if(month == tgt_month){
						 j("#e2m_event_cal span").eq(tgt_day-1).html(function() {
						 	if(tgt_url == ""){
						 		return "<string>"+tgt_day+"</string>"
						 	}
						 	else{
						 		return "<a href=\""+tgt_url+"\" title=\""+tgt_title+"\">"+tgt_day+"</a>"
						 	}
						 });
						 j("#e2m_event_cal span").eq(tgt_day-1).parent("td").addClass("event_day");
					}
				}
    		}
    	}
    	cal_flag = false;
    }
    </script>
	<?php
}

//!< カレンダーテンプレート
function e2m_cal(){
$data = <<< END
<div class="e2m_calender">
    <p class="cal_year">20xx年</p>
    <ul style="margin:0px 4px 0px 4px; padding:0px;" class="month cf">
        <li class="prevmonth"><a href=""> &lt;&lt;x月 </a></li>
        <li class="currentmonth">x月</li>
        <li class="nextmonth"><a href=""> x月 &gt;&gt; </a></li>
    </ul>
    <ul style="margin:0px 0px 0px 8px; padding:0px;" class="week cf">
        <li class="holiday">SUN</li>
        <li>MON</li>
        <li>TUE</li>
        <li>WED</li>
        <li>THU</li>
        <li>FRI</li>
        <li class="holiday">SAT</li>
    </ul>
    <div class="day cf">
    	<table id="e2m_event_cal">
		</table>
	</div>
</div>
END;
echo $data;
}

//!< 10未満の文字に0を追加する
function addZero($value){
	if($value < 10){
		$value = "0".$value;
	}
	return $value;
}
function deleteZero($value){
	if($value == "00" || $value == "01" || $value == "02" || $value == "03" || $value == "04" || $value == "05" || $value == "06" || $value == "07" || $value == "08" || $value == "09"){
		$value = str_replace("0", "", $value);
	}
	return $value;
}

//!< 日付順にソート
function e2m_sortData(){
	global $plugin_db;
	$data = e2m_getEventData();

	foreach ($data as $key => $row) {
	    $year[$key]  = $row['year'];
	    $month[$key] = $row['month'];
	    $days[$key]  = $row['days'];
	    $week[$key]  = $row['week'];
	    $title[$key] = $row['title'];
	    $url[$key]  = $row['url'];
	    $other[$key] = $row['other'];
	    $date[$key] = $row['date'];
	}

	array_multisort($date, SORT_ASC, $title, SORT_ASC, $data);
	return $data;
}

//!< カレンダー形式のウィジェット
class e2mCalendarWidget extends WP_Widget {
    /** constructor */
    function e2mCalendarWidget() {
        parent::WP_Widget(false, $name = 'e2mCalendarWidget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
                  <?php e2m_showCalendar(); ?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <?php 
    }
    
    function e2m_showcal(){
    }

} // class e2mCalendarWidget
add_action('widgets_init', create_function('', 'return register_widget("e2mCalendarWidget");'));

//!< リスト形式のウィジェット
class e2mListWidget extends WP_Widget {
    /** constructor */
    function e2mListWidget() {
        parent::WP_Widget(false, $name = 'e2mListWidget');	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $num = apply_filters('widget_title', $instance['num']);
        $same_month = apply_filters('widget_title', $instance['same_month']);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
                        <ul>
                  <?php e2m_showList($num, $same_month); ?>
                  		</ul>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        $num = esc_attr($instance['num']);
        $same_month = esc_attr($instance['same_month']);
        if($same_month == true){
        	$checked = "checked=\"checked\"";
        }
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('num'); ?>"><?php _e('表示数:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('num'); ?>" name="<?php echo $this->get_field_name('num'); ?>" type="text" value="<?php echo $num; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('same_month'); ?>"><?php _e('現在の月のみ:'); ?> <input id="<?php echo $this->get_field_id('same_month'); ?>" name="<?php echo $this->get_field_name('same_month'); ?>" type="checkbox" <?php echo $checked; ?> value="1" /></label></p>
        <?php 
    }
    
    function e2m_showcal(){
    }

} // class e2mListWidget
add_action('widgets_init', create_function('', 'return register_widget("e2mListWidget");'));
?>