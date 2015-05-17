<?php
/*
Plugin Name: wp-shabbat
Plugin URI: www.dossihost.net
Description: Site closing on Saturday and Holidays by identifying the address of the user IP and close to 40 km
Version: 1.7
Author: DrMosko
Author URI: www.dossihost.net

Text Domain: WP-Shabbat
Domain Path: /lang
*/
/* This script uses GeoLite Country from MaxMind (http://www.maxmind.com) which is available under terms of GPL/LGPL */


 // add the admin settings and such
add_action('admin_init', 'wp_shabbat_admin_init');
function wp_shabbat_admin_init(){
	register_setting( 'wp_shabbat_settings', 'wp_shabbat_settings', 'wp_shabbat_settings_validate');
	add_settings_section('wp_shabbat_main',__('WP Shabbat Plugin Settings','WP-Shabbat'), 'wp_shabbat_main_section', 'wp_shabbat');
	add_settings_field('wp_shabbat_announcement', __('Display popup announcement message','WP-Shabbat'), 'wp_shabbat_input_announcement', 'wp_shabbat', 'wp_shabbat_main');
	add_settings_field('wp_shabbat_candle', __('Candle light Minutes','WP-Shabbat'), 'wp_shabbat_settings_input_candle', 'wp_shabbat', 'wp_shabbat_main');
	add_settings_field('wp_shabbat_havdala', __('Havdala Minutes','WP-Shabbat'), 'wp_shabbat_settings_input_havdala', 'wp_shabbat', 'wp_shabbat_main');
	add_settings_field('wp_shabbat_user_title', __('Enter your message','WP-Shabbat'), 'wp_shabbat_user_title', 'wp_shabbat', 'wp_shabbat_main');
	add_settings_field('wp_shabbat_link', __('Disable support link','WP-Shabbat'), 'wp_shabbat_input_link', 'wp_shabbat', 'wp_shabbat_main');
}
function wp_shabbat_main_section() {
	echo __('the website will close to user based on his location, when it is shabbat in user location the site will be closed but for user from other location where its not shabbat the site will be open','WP-Shabbat').'<br/>';
	echo __('WP-Shabbat correctly using a free database that need to be updated every month , the plugin will download it from our servers automatically every month','WP-Shabbat').'<br/>';
	echo __('We recommend that every site owner will consult with a rabbi for the time to close his site','WP-Shabbat').'<br/><br/>';
	echo __('WP-Shabbat is SEO friendly and as requested by Google, search engine bots get http header 503','WP-Shabbat').'<br/>';
	echo '<h3>'.__('WP-Shabbat is wordpress plugin that close website in shabbat and holidays base on user location,','WP-Shabbat').'</h3><br/>';
	
}
function wp_shabbat_input_announcement() { 
	$options = get_option('wp_shabbat_settings');
	$html = '<input type="checkbox" id="wp_shabbat_announcement" name="wp_shabbat_settings[announcement]" value="1"' . checked( 1, $options['announcement'], false ) . '/>
	<label>'.__('click to display popup message and not close the site - for some Poskim opinion its enough','WP-Shabbat').'</label>';
    	
    echo $html;
	
}
function wp_shabbat_settings_input_candle() { 
	$options = get_option('wp_shabbat_settings');
	echo __('insert minutes for candle light time, number of minutes before sunset minimum - 20 min ,max - 600 min.','WP-Shabbat').'<br/>';
	echo "<input id='wp_shabbat_candle' name='wp_shabbat_settings[Candle]' size='40' type='text' value='{$options['Candle']}' />";
}
function wp_shabbat_settings_input_havdala() {
	$options = get_option('wp_shabbat_settings');
	echo __('insert minutes for havdala time, number of jewish minutes after sunset minimum - 18 min ,max - 72 min.','WP-Shabbat').'<br/>'. __('(jewish hour is calculated from sunrise to sunset divided by 12)','WP-Shabbat').'<br/> ';
	echo "<input id='wp_shabbat_havdala' name='wp_shabbat_settings[Havdala]' size='40' type='text' value='{$options['Havdala']}' />";
}
function wp_shabbat_user_title() {
	$options = get_option('wp_shabbat_settings');
	echo __('please enter your message for the user or leave blank for default','WP-Shabbat').'<br/>';
	echo "<input id='wp_shabbat_havdala' name='wp_shabbat_settings[user_title]' size='80' type='text' value='{$options['user_title']}' />";
}
function wp_shabbat_input_link() { 
	$options = get_option('wp_shabbat_settings');
	$html = '<input type="checkbox" id="wp_shabbat_link" name="wp_shabbat_settings[link]" value="1"' . checked( 1, $options['link'], false ) . '/>
	<label>'.__('click to disable Site Observe Shabbat BY WP-Shabbat link ','WP-Shabbat').'</label>';
    	
    echo $html;
}


// validate  
function wp_shabbat_settings_validate($input) {
	$options = get_option('wp_shabbat_settings');
	$announcement = $input['announcement'];
	$options['Havdala'] = intval(wp_filter_nohtml_kses( trim( $input['Havdala']) ));
	$options['Candle'] = intval(wp_filter_nohtml_kses( trim( $input['Candle']) ));
	$havdala = $options['Havdala'];
	$candle = $options['Candle'];
	$link = $input['link'];

if ( $announcement == 1 ){
	$options['announcement'] = 1;
} else {
	$options['announcement'] = 0;
}


if(  ( $candle < 20) || !is_numeric( $candle ) || preg_match("/^0/", $candle) || ( $candle > 600  ) )  { 
	$options['Candle'] = 20;
	add_settings_error(
				'wp_shabbat_validate_fail',           // setting title
				'wp_shabbat_validate_fail',            // error ID
				__('Invalid Candle light time, please enter valid input, default was loaded.','WP-Shabbat'),   // error message
				'error'                        // type of message
			);		
}

if(  ( $havdala < 18 ) || !is_numeric( $havdala ) || preg_match("/^0/", $havdala) || ( $havdala > 72  ) )  { 
	$options['Havdala'] = 18;
	add_settings_error(
				'wp_shabbat_validate_fail',           // setting title
				'wp_shabbat_validate_fail',            // error ID
				__('Invalid Havdalah light time, please enter valid input, default was loaded.','WP-Shabbat'),   // error message
				'error'                        // type of message
			);		
}

if ( empty($input['user_title']) ){
	$options['user_title'] =  __('We Dont Work Today','WP-Shabbat');
}else{
	$options['user_title'] = sanitize_text_field( $input['user_title'] );
}

if ( $link == 1 ){
	$options['link'] = 1;
} else {
	$options['link'] = 0;
}
return $options;
}
// admin page
function wp_shabbat_admin() {   
    add_options_page( 'WP-Shabbat', 'WP-Shabbat','manage_options','wp_shabbat_admin', 'wp_shabbat_admin_options' );
	
}  
add_action('admin_menu', 'wp_shabbat_admin');  

function wp_shabbat_admin_options() {  // display the admin options page
?>
	<div>
	<?php screen_icon();
	$options = get_option('wp_shabbat_settings');	?>

	<h2><?php echo __('WP-Shabbat Plugin is Working','WP-Shabbat')?></h2>
	<form action="options.php" method="post">
	<?php settings_fields( 'wp_shabbat_settings' ); ?>
	<?php do_settings_sections( 'wp_shabbat'  ); ?>
	 
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
	</div>
	<div>
	<h3><?php echo __('How hermetically closed page look like?','WP-Shabbat') ?> </h3>
	<a  target="_blank" href="<?php echo site_url().'/?WP-Shabbat=Shabbat-Closed-Page&redirectReason=' .__('Holiday','WP-Shabbat').'&opentime='.__('tommorow at ','WP-Shabbat').date('H:i',current_time('timestamp')); ?>" ><?php echo __('Test Page','WP-Shabbat') ; ?></a> 
	<h3><?php echo __('How popup message look like?','WP-Shabbat') ?> </h3>
	<a  target="_blank" href="<?php echo site_url().'/?WP_Shabbat_popupTest=on' ?>" ><?php echo __('Test Page','WP-Shabbat') ; ?></a> 
	
	</div>
	<div id="WPShabbat-Donation">
	<?php
		/*echo 'DEBUG: wp_shabbat_settings<pre>';
		print_r($options);
		echo '</pre>' ;*/
		$updateOptions = get_option('wp_shabbat_update_settings');
		/*echo 'DEBUG: wp_shabbat_update_settings <pre>';
		print_r($updateOptions);
		echo '</pre>' ;*/
		echo '<br/><br/>'.__('Ip DataBase last updated at : ','WP-Shabbat').'<code>'. date('d.m.y',$updateOptions['lastUpdate']) .'</code><br/>';
		echo '<br/><br/>'.__('Update Status : ','WP-Shabbat').'<code>'. $updateOptions['updatestatus'] .'</code><br/>';
		echo '<br/><br/>'.__('Next Update : ','WP-Shabbat').'<code>'. date('d.m.y',$updateOptions['nextUpdate']) .'</code><br/>';
		echo '<br/><h2>' . __( '5$ for this plugin is not much, pls make a donation','WP-Shabbat' ) . '</h2><br/>';
		
	?>	
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="53KUKZ9KN2YBN">
	<input type="image" src="https://www.paypalobjects.com/en_US/IL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
	</div>
	<div>
	<br/><br/>
	 <?php echo __( 'This product includes GeoLite data created by MaxMind, available from','WP-Shabbat' )?>
	<a href="http://www.maxmind.com">http://www.maxmind.com</a>
	</div>
 
<?php  
}  
ob_start();
// create globals : $user_time , $user_localtime , $user_timezone_offset
include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-user.php');

// add function page
function wp_shabbat() {
	
	if ( (!$_GET['WP-Shabbat'] == "Shabbat-Closed-Page") ){ // if not shabbat fly page presented 
		$options = get_option('wp_shabbat_update_settings');
		
		if ( ($updateOptions['lastUpdate'] != '0') ){
		include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-functions.php');
		}
    }else{
	
	}
}
add_action( 'get_header', 'wp_shabbat' );

// create default values when user activate plugin
function wp_shabbat_activate() {
			
		delete_option('wp_shabbat_settings');				
		delete_option('wp_shabbat_update_settings');
		load_plugin_textdomain('WP-Shabbat', false, dirname(plugin_basename(__FILE__)) . '/lang'); 
		$settings = array(
		'announcement' => 0,
		'CandleDefault' => 20,
		'HavdalaDefault' => 18,
		'Candle' => 20,
		'Havdala' => 18,
		'link' => 0,
		'user_title' => __('We Dont Work Today','WP-Shabbat'),
		);

		add_option('wp_shabbat_settings', $settings);
		
		$settings = array(
		'updatestatus' => 'Plugin DataBase need to be updated',
		'lastUpdate' => intval(0),
		);

		add_option('wp_shabbat_update_settings', $settings);
		
		include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-update.php');

}
register_activation_hook( __FILE__, 'wp_shabbat_activate' );

// delete default values when user activate plugin
function wp_shabbat_deactivate() {

	delete_option('wp_shabbat_settings');
	delete_option('wp_shabbat_update_settings');
	
}
register_deactivation_hook( __FILE__, 'wp_shabbat_deactivate' );

// add update check 
function wp_shabbat_update() {
		include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-update.php');
}
add_action( 'admin_init', 'wp_shabbat_update' );

// load language translation

function wp_shabbat_lang()  
{  
    // Localization  
    load_plugin_textdomain('WP-Shabbat', false, dirname(plugin_basename(__FILE__)) . '/lang');  
}  
  
// Add actions  
add_action('init', 'wp_shabbat_lang');  

// add on fly wp-shabbat-closed-page
include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-closed-page.php');

// add link to DossiHost
function wp_shabbat_add_footer_link($footer) {
	$options = get_option('wp_shabbat_settings');
		if ( $options['link'] == 0 ){
		echo  '<div align="center" id="wp-shabbat-link"><a href="http://www.dossihost.net/%D7%AA%D7%95%D7%A1%D7%A3-%D7%95%D7%95%D7%A8%D7%93%D7%A4%D7%A1-%D7%A1%D7%95%D7%92%D7%A8-%D7%90%D7%AA%D7%A8-%D7%91%D7%A9%D7%91%D7%AA%D7%95%D7%AA-%D7%95%D7%97%D7%92%D7%99%D7%9D/">'. __('Site Observe Shabbat BY WP-Shabbat','WP-Shabbat') .'</a></div>';
	} else {
		echo  '<div align="center" id="wp-shabbat-link" style="display:none"><a href="http://www.dossihost.net/%D7%AA%D7%95%D7%A1%D7%A3-%D7%95%D7%95%D7%A8%D7%93%D7%A4%D7%A1-%D7%A1%D7%95%D7%92%D7%A8-%D7%90%D7%AA%D7%A8-%D7%91%D7%A9%D7%91%D7%AA%D7%95%D7%AA-%D7%95%D7%97%D7%92%D7%99%D7%9D/">'. __('Site Observe Shabbat BY WP-Shabbat','WP-Shabbat') .'</a></div>';
	}
	

}   

add_action('wp_footer', 'wp_shabbat_add_footer_link');


?>