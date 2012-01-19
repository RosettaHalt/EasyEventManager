<?php
/*
Plugin Name: Easy Event Manager
Version: 0.6
Plugin URI: http://web.lugn-design.com
Author: Rosetta
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
	add_options_page('イベント管理', 'イベント管理', 'level_8', __FILE__, 'easy_manage_event');
}

//!< CSSの読み込み
function wp_custom_admin_Lib() {
		$plugin_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL) : WP_PLUGIN_URL;
		$plugin_url .= "/EasyEventManager/";
	?>
	<link type="text/css" href="<?php echo $plugin_url; ?>css/style.css" rel="stylesheet" />
	<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" rel="stylesheet" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo $plugin_url; ?>js/script.js"></script>
	<script type="text/javascript" src="<?php echo $plugin_url; ?>js/sort/jquery.tablesorter.js"></script>
	<script type="text/javascript">
		var j = jQuery.noConflict();
		j(function() {
	    <?php
			$total_event = getTotalEvent();

    		for($i = 0; $i < $total_event; $i++){
    			echo "j(\"#datepicker".$i."\").datepicker({dateFormat: 'yy/mm/dd'});\n";
    		}
		?>
			j("#changetable").tablesorter({
			});
		});
	</script>
	<?php
}
add_action('admin_head', 'wp_custom_admin_Lib', 100);


//!< プラグインページのコンテンツを表示
function easy_manage_event () {
	global $plugin_db;
?>
    <div class="wrap">
        <h2>Easy Event Manager</h2>

        <?php
	        if ( isset($_POST['delete_event']) && isset($_POST['delete']) ) {
	        	confirmDeleteEvent();
		    }
		    else{
	        	undercard();
	        	addEvent();
	        	changeEvent();
	        	deleteEvent();
	
	        	//showDebugData();	// デバッグ用
        	}
        ?>
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
			$total_event = getTotalEvent();
			$sort_data = getEventData2();
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
			$total_event = getTotalEvent();
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
			$total_event = getTotalEvent();
			$event_data = getEventData();
			$sort_data = sortData();																																									
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
            <input type="submit" class="button-primary" value="<?php _e('選択したイベントを削除') ?>" />
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
				$total_event = getTotalEvent();
				$data = getEventData2();
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
	$event_data = getEventData();
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
    $total_event = getTotalEvent();

    for($i = 0; $i < $total_event; $i++){
   		delete_option($plugin_db.'title'.$i);
   		delete_option($plugin_db.'url'.$i);
   		delete_option($plugin_db.'other'.$i);
   		delete_option($plugin_db.'date'.$i);
    }
}

//!< 表示用関数
function showDebugData() {
	global $plugin_db;
	$total_event = getTotalEvent();

	echo "<div class=\"show_event\">";

	echo "<h3>データの表示</h3>";

	echo "合計イベント数 : ".$total_event;
	$event_data = getEventData();

	echo "<pre>";
	print_r($event_data);
	echo "</pre>";

	echo "sorted <br />";
	$sort_data = sortData();

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
function getTotalEvent(){
	global $plugin_db;

	if(get_option($plugin_db.'total_event') != 0){
		return get_option($plugin_db.'total_event');
	}

	return 0;
}

//!< イベントの情報を配列で返す
function getEventData(){
	global $plugin_db;
	$total_event = getTotalEvent();
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
function getEventData2(){
	global $plugin_db;
	$total_event = getTotalEvent();
	$data = getEventData();

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

//!< 10未満の文字に0を追加する
function addZero($value){
	if($value < 10){
		$value = "0".$value;
	}
	return $value;
}

//!< 日付順にソート
function sortData(){
	global $plugin_db;
	$data = getEventData2();

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
?>