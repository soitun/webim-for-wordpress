<?php
function webim_config() {
	global $wpdb;
	$notice = "";
	if ( $_POST['_method'] == 'put' ) {
		update_option( "webim_domain", $wpdb->escape( $_POST['domain'] ) );
		update_option( "webim_apikey", $wpdb->escape( $_POST['apikey'] ) );
		update_option( "webim_host", $wpdb->escape( $_POST['host'] ) );
		update_option( "webim_port", $wpdb->escape( $_POST['port'] ) );
		$notice = '<div id="message" class="updated fade"><p>设置已保存</p></div>';
	}
	$config_domain = get_option( "webim_domain" );
	$config_apikey = get_option( "webim_apikey" );
	$config_host = get_option( "webim_host" );
	$config_port = get_option( "webim_port" );
?>
<div class="wrap">
	<h2>设置</h2>
	<?php echo $notice; ?>
	<form method="post" action="admin.php?page=webim/webim.php">
		<input type="hidden" name="_method" value="put" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="domain">注册域名</label></th>
				<td>
				<input name="domain" type="text" id="domain" value="<?php echo $config_domain;?>" class="regular-text" />
				<span class="description"></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="apikey">注册apikey</label></th>
				<td>
				<input name="apikey" type="text" id="apikey"  value="<?php echo $config_apikey;?>" class="regular-text" />
				<span class="description">域名对应的apikey</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="host">im服务器</label></th>
				<td>
				<input name="host" type="text" id="host"  value="<?php echo $config_host;?>" class="regular-text" />
				<span class="description"></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="port">端口</label></th>
				<td>
				<input name="port" type="text" id="port"  value="<?php echo $config_port;?>" class="regular-text" />
				<span class="description">im服务器端口</span>
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="保存更改" />
		</p>
	</form>
</div>

<?php
}

function webim_themes() {
	global $wpdb;
	require_once( dirname(__FILE__) . "/lib/util.php" );
	$notice = "";
	if ( $_GET['theme'] ){
		$theme = $wpdb->escape( $_GET['theme'] );
		if ( get_option ("webim_theme") != $theme )
			$notice = '<div id="message" class="updated fade"><p>主题已修改</p></div>';
		update_option( "webim_theme", $theme );
	}
	$theme = get_option( "webim_theme" );
	$path = dirname(__FILE__).DIRECTORY_SEPARATOR."static".DIRECTORY_SEPARATOR."themes";
	$files = scan_subdir($path);
	$html = '<ul id="themes">';
	foreach ($files as $k => $v){
		$t_path = $path.DIRECTORY_SEPARATOR.$v;
		if(is_dir($t_path) && is_file($t_path.DIRECTORY_SEPARATOR."jquery.ui.theme.css")){
			$cur = $v == $theme ? " class='current'" : "";
			$url = 'admin.php?page=webim_themes&theme='.$v;
			$img = webim_url_for( "static/themes/images/$v.png" );
			$html .= "<li$cur><h4><a href='$url'>$v</a></h4><p><a href='$url'><img width=100 height=134 src='$img' alt='$v' title='$v'/></a></p></li>";
		}
	}
	$html .= '</ul>';
?>
	<style type="text/css">
	#notice{
		margin-top: 5px;
		padding: 10px;
		text-align: center;
		background: #FFFAF0;
		border: 1px solid #FFD700;
	}
	#themes{
		overflow: hidden;
		list-style: none;
		padding: 0;
		margin: 0;
		margin-top: 20px;
	}
	#themes li{
		float: left;
		padding: 10px;
	}
	#themes li h4{
		margin: 0 0 5px 0;
	}
	#themes li.current{
		background: #FFFFE0;
	}
	</style>
	<div class="wrap">
		<h2>主题选择</h2>
		<?php echo $notice; ?>
		<?php echo $html;?>
	</div>
<?php
}
?>
