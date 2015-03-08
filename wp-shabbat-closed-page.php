<?php

function wp_shabbat_status_header() {
	
	

	wp_load_translations_early();

	$protocol = $_SERVER["SERVER_PROTOCOL"];
	if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
		$protocol = 'HTTP/1.0';
	header( "$protocol 503 Service Unavailable", true, 503 );
	header( 'Content-Type: text/html; charset=utf-8' );
	header('Retry-After: '.date('D d M G:i:s e',$_GET['exitTime']).'');
	
	get_header();
	 
		// hide navigetion menu via css
		echo '<style>
		.nav-menu{display:none}
		.menu-item{display:none}
		.menu{display:none}
		.page_item{display:none}
		</style>';

	$options = get_option('wp_shabbat_settings');
?>
	
		
		<?php echo '<center><h1>'.$options['user_title'].'</h1></center>
					 <center><h3>'.__('Today is : ','WP-Shabbat'). $_GET['redirectReason'] .'</h3></center><br/>
					 <center><h3>'.__('Please come back ','WP-Shabbat'). $_GET['opentime'] .'</h3><center><br/>'; ?>
	</body>
	</html>
<?php
get_footer();
	die();
	
		
	
}
function wp_shabbat_closed_template() {
	
     
	if   ('' != (locate_template( 'page.php' )))  {
		include(TEMPLATEPATH.'/page.php');
	}else {
		if  ('' != (locate_template( 'index.php' )))  {
			include(TEMPLATEPATH.'/index.php');
		}else {
			if  ('' != (locate_template( 'single.php' )))  {
				include(TEMPLATEPATH.'/single.php');
			}else {
				if  ('' != (locate_template( 'home.php' )))  {
					include(TEMPLATEPATH.'/home.php');
					}
				}
			}	
		}
  exit;
}
if (!empty ( $_GET['WP-Shabbat'] ) && ("Shabbat-Closed-Page" === $_GET['WP-Shabbat']) ) {
  add_filter('send_headers','wp_shabbat_status_header');
}
// test page for popup message
if (!empty ( $_GET['WP_Shabbat_popupTest']) && ("on" === $_GET['WP_Shabbat_popupTest']) )  { 
  include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
}
?>