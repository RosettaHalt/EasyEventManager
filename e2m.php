<?php
/*
Plugin Name: Easy Event Manager
Version: 0.3
Plugin URI: http://web.lugn-design.com
Author: Rosetta
Author URI: http://web.lugn-design.com
Description: Easy to manage events.
*/

//!< データベースの接頭語
$plugin_db = "_easy_manage_event_plugin_";

//!< 管理メニューのアクションフック
add_action('admin_menu', 'admin_menu_easy_manage_event');

//!< アクションフックのコールバッック関数
function admin_menu_easy_manage_event () {
    // 設定メニュー下にサブメニューを追加
    add_options_page('イベント管理', 'イベント管理', manage_options, __FILE__, 'easy_manage_event');
}

//!< CSSの読み込み
function wp_custom_admin_Lib() {
	?>
	<style type="text/css">
		.num_input{
			width: 80px;
		}
		.text_input{
			width: 240px;
		}
		table td span{
			display: none;
		}
		.dataTables_filter{
			display: none;
		}
	</style>
	<?php
		$plugin_url = (is_ssl()) ? str_replace('http://','https://', WP_PLUGIN_URL) : WP_PLUGIN_URL;
		$plugin_url .= "/EasyEventManager/";
	?>
	<script type="text/javascript" language="javascript" src="<?php echo $plugin_url; ?>js/script.js"></script>
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
        	undercard();
        	addEvent();
        	changeEvent();
        	deleteEvent();

        	showDebugData();	// デバッグ用
        ?>
    </div>
<?php
}
?>

<?php
//!< 日付変更用の関数
function changeEvent(){
	global $plugin_db; ?>
	<div class="update_event" id="update_event">
	<h3>内容の変更</h3>
	<span id="prev">←</span>　<span id="page"></span>　<span id="next">→</span>
	<form method="post" action="options.php">
	    <?php
	    	wp_nonce_field('update-options');
			$total_event = getTotalEvent();
			$event_data = getEventData();
	    ?>
	    <table class="widefat" id="changetable">
	    <thead>
	        <tr class="thead" valign="top">
	            <th scope="row">No.</th>
	            <th scope="row">年</th>
	            <th scope="row">月</th>
	            <th scope="row">日</th>
	            <th scope="row">タイトル</th>
	            <th scope="row">URL</th>
	            <th scope="row">その他</th>
	        </tr>
	    </thead>
	    <tbody>
	    <?php
		    for($i = $total_event-1; $i >= 0; $i--){
			    $year_value = $event_data["year"][$i];
			    $month_value = $event_data["month"][$i];
			    $days_value = $event_data["days"][$i];
			    $title_value = $event_data["title"][$i];
			    $url_value = $event_data["url"][$i];
			    $other_value = $event_data["other"][$i];
		    ?>
	        <tr class="thead" valign="top">
	            <td scope="row"><?php echo addZero($i); ?> : </th>
	            <td><input class="num_input" type="text" name="<?php echo $plugin_db; ?>year<?php echo $i; ?>" value="<?php echo $year_value; ?>" /><span><?php echo $year_value; ?></span>年</td>
	            <td><input class="num_input" type="text" name="<?php echo $plugin_db; ?>month<?php echo $i; ?>" value="<?php echo $month_value; ?>" /><span><?php echo $month_value; ?></span>月</td>
	            <td><input class="num_input" type="text" name="<?php echo $plugin_db; ?>days<?php echo $i; ?>" value="<?php echo $days_value; ?>" /><span><?php echo $days_value; ?></span>日</td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>title<?php echo $i; ?>" value="<?php echo $title_value; ?>" /><span><?php echo $title_value; ?></span></td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>url<?php echo $i; ?>" value="<?php echo $url_value; ?>" /><span><?php echo $url_value; ?></span></td>
	            <td><input class="text_input" type="text" name="<?php echo $plugin_db; ?>other<?php echo $i; ?>" value="<?php echo $other_value; ?>" /><span><?php echo $other_value; ?></span></td>
	        </tr>
	    <?php } ?>
	    </tbody>
	    </table>
		<select id="foo" name="foo">
			<option value="10">10</option>
			<option value="25">25</option>
			<option value="50">50</option>
		</select>
	    
	    <input type="hidden" name="action" value="update" />
	    <input type="hidden" name="<?php echo $plugin_db; ?>total_event" value="<?php echo $total_event; ?>" />
	    <?php
	    	echo "<input type=\"hidden\" name=\"page_options\" value=\"";
		    for($i = 0; $i < $total_event; $i++){
		    	if($i!=0){
		    		$fst=",";
		    	}
		    	echo $fst.$plugin_db."year".$i.",".$plugin_db."month".$i.",".$plugin_db."days".$i.",".$plugin_db."title".$i.",".$plugin_db."url".$i.",".$plugin_db."other".$i;
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
<?php }
?>

<?php
//!< 日付追加用の関数
function addEvent(){
	global $plugin_db; ?>
	<div class="add_event">
	<form method="post" action="options.php">
	 	<?php
	    	wp_nonce_field('update-options');
			$total_event = getTotalEvent();
			$event_data = getEventData();
	    ?>
	    <h3>新規追加</h3>
	    <table class="widefat">
	    <thead>
	        <tr class="thead" valign="top">
	            <th scope="row">年</th>
	            <th scope="row">月</th>
	            <th scope="row">日</th>
	            <th scope="row">タイトル</th>
	            <th scope="row">URL</th>
	            <th scope="row">その他</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr valign="top">
	            <td><input class="num_input" type="text" name="<?php echo $plugin_db; ?>year<?php echo $total_event; ?>" />年</td>
	            <td><input class="num_input" type="text" name="<?php echo $plugin_db; ?>month<?php echo $total_event; ?>" />月</td>
	            <td><input class="num_input" type="text" name="<?php echo $plugin_db; ?>days<?php echo $total_event; ?>" />日</td>
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
	    	<input type="hidden" name="page_options" value="<?php echo $plugin_db; ?>total_event,<?php echo $plugin_db ?>year<?php echo $add_num ?>,<?php echo $plugin_db ?>month<?php echo $add_num ?>,<?php echo $plugin_db ?>days<?php echo $add_num ?>,<?php echo $plugin_db ?>title<?php echo $add_num ?>,<?php echo $plugin_db ?>url<?php echo $add_num ?>,<?php echo $plugin_db ?>other<?php echo $add_num ?>" />
	        <input type="submit" class="button-primary" value="<?php _e('Add Event') ?>" />
	    </p>
	</form>
	</div>
<?php }
?>

<?php
//!< 日付削除用の関数
function deleteEvent(){
	global $plugin_db;
	?>
	<div class="delete_event">
	<h3>削除</h3>
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" style="width:200px;" >
     	<?php
        	wp_nonce_field('update-options');
			$total_event = getTotalEvent();
			$event_data = getEventData();
        ?>
	    <table class="widefat">
	    <thead>
	        <tr class="thead">
	        	<th></th>
	        	<th scope="row">削除番号</th>
	        </tr>
	    </thead>
	    <tbody>
        		<?php
        		for($i = 0; $i < $total_event; $i++){
			    	$year_value = $event_data["year"][$i];
			    	$month_value = $event_data["month"][$i];
			    	$days_value = $event_data["days"][$i];
			    	$title_value = $event_data["title"][$i];
			    	$url_value = $event_data["url"][$i];
			    	$other_value = $event_data["other"][$i];
		    	?>
        	<tr>
        		<td><input type="checkbox" name="delete[]" value="<?php echo $i ?>" /></td>
		    	<td>No. <?php echo $i; ?></td>
        	</tr>
        		<?php } ?>
       	</tbody>
        </table>
        <p class="submit">
        	<input type="hidden" name="action" value="update" />
	    	<input type="hidden" name="<?php echo $plugin_db; ?>total_event" value="<?php echo $total_event; ?>" />
            <input type="submit" class="button-primary" value="<?php _e('Delete Event') ?>" />
        </p>
    </form>
    </div>
<?php }
?>

<?php
//!< イベント更新や削除などの処理が発生した場合の処理
function undercard(){
	global $plugin_db;
	$delete_cnt = 0;
	if (isset($_POST['delete'])) {
        $delIds = $_POST['delete'];
    }
    if (isset($delIds)) {
        foreach ($delIds as $delId) {
            $delete_item .= "イベントNo.".$delId."<br />";
            delete_option($plugin_db.'year'.$delId);
            delete_option($plugin_db.'month'.$delId);
            delete_option($plugin_db.'days'.$delId);
            delete_option($plugin_db.'title'.$delId);
            delete_option($plugin_db.'url'.$delId);
            delete_option($plugin_db.'other'.$delId);
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
    	update_option($plugin_db.'year'.$i, $event_data['year'][$i]);
    	update_option($plugin_db.'month'.$i, $event_data['month'][$i]);
   		update_option($plugin_db.'days'.$i, $event_data['days'][$i]);
   		update_option($plugin_db.'title'.$i, $event_data['title'][$i]);
   		update_option($plugin_db.'url'.$i, $event_data['url'][$i]);
   		update_option($plugin_db.'other'.$i, $event_data['other'][$i]);
   		$event_cnt++;
    }
    update_option( $plugin_db.'total_event', $event_cnt );
}
?>

<?php
//!< プラグインを削除する際に行うオプションの削除
if ( function_exists('register_uninstall_hook') ) {
    register_uninstall_hook(__FILE__, 'uninstall_hook_easy_manage_event');
}
function uninstall_hook_easy_manage_event () {
	global $plugin_db;
    $total_event = getTotalEvent();

    for($i = 0; $i < $total_event; $i++){
    	delete_option($plugin_db.'year'.$i);
    	delete_option($plugin_db.'month'.$i);
   		delete_option($plugin_db.'days'.$i);
   		delete_option($plugin_db.'title'.$i);
   		delete_option($plugin_db.'url'.$i);
   		delete_option($plugin_db.'other'.$i);
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

    $event_cnt = 0;
    
    for($i = 0; $i < $total_event; $i++){
    	if( get_option( $plugin_db.'year'.$i ) != '' || get_option( $plugin_db.'month'.$i ) != '' || get_option( $plugin_db.'days'.$i ) != '' || get_option( $plugin_db.'title'.$i ) != '' || get_option( $plugin_db.'url'.$i ) != '' || get_option( $plugin_db.'other'.$i ) != ''){
    		$event_year[$i] = get_option( $plugin_db.'year'.$i );
    		$event_month[$i] = get_option( $plugin_db.'month'.$i );
    		$event_day[$i] = get_option( $plugin_db.'days'.$i );
    		$event_title[$i] = get_option( $plugin_db.'title'.$i );
    		$event_url[$i] = get_option( $plugin_db.'url'.$i );
    		$event_other[$i] = get_option( $plugin_db.'other'.$i );
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
    }

    $event_data["year"] = array_merge($event_year);
    $event_data["month"] = array_merge($event_month);
    $event_data["days"] = array_merge($event_day);
    $event_data["title"] = array_merge($event_title);
    $event_data["url"] = array_merge($event_url);
    $event_data["other"] = array_merge($event_other);
    
    return $event_data;
}
function addZero($value){
	if($value < 10){
		$value = "0".$value;
	}
	return $value;
}
?>