<?php
/***********************
 function to auto update the dat file
 *******************/
$options = get_option('wp_shabbat_update_settings');

$lastfiletime = filectime(plugin_dir_path( __FILE__ ).'/GeoLiteCity.dat');

// check if week passed since last update
if ( ($options['nextUpdate'] <  current_time('timestamp')) || ( $options['lastUpdate'] == 0 ) ){
		
		$upload_dir = wp_upload_dir();
		$remotefilesize = strlen(file_get_contents('http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz'));
		
		$file = download_url('http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz');
		$localfilesize = strlen(file_get_contents($file));
		
		if ( $localfilesize == $remotefilesize) {
			// get contents of a gz-file into a string
			$sfp = gzopen($file, "rb");
			$fp = fopen(plugin_dir_path( __FILE__ ).'/GeoLiteCity.dat', "w");

			while ($string = gzread($sfp, 4096)) {
				fwrite($fp, $string, strlen($string));
			}
			gzclose($sfp);
			fclose($fp);
			
			
			
			
			if ( filemtime(plugin_dir_path( __FILE__ ).'/GeoLiteCity.dat') > $lastfiletime ) {
				$updatestatus = 'file extracted succefully';
				$today = current_time('timestamp'); 
				$nextUpdate = strtotime('+1 week',$today) ;
			} else {
				$updatestatus = 'problem with extraction';
			}
		}
		else 
		{
		 $updatestatus = 'problem with gzip file download';
		}
	
	// delete the gz file
	unlink($file);
	
	// check if first time update
	if ( ($options['lastUpdate'] == 0 ) ){ 
			$today = current_time('timestamp'); 
			$nextUpdate = strtotime('+1 week',$today) ;
		} 	
	
	/*	
	echo $nextUpdate;
	echo 'DEBUG: <pre>';
	print_r($options);
	echo '</pre>' ;*/
	
	$new_settings = array(
		'updatestatus' => $updatestatus,
		'lastUpdate' =>  $today,
		'nextUpdate' =>  $nextUpdate,
		
		
	);
	/*
	echo 'DEBUG: <pre>';
	print_r($new_settings);
	echo '</pre>' ;
	*/
		
	update_option('wp_shabbat_update_settings', $new_settings);
		
}
?>