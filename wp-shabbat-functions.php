<?php
 
// get all user details
$user_time = wp_shabbat_user_time();
$user_localday = wp_shabbat_user_localday();
$user_timezone_offset = wp_shabbat_user_timezone_offset();
$record = wp_shabbat_user_record();



/****************

	candle light time

	*************/
	
function candleTime($user_time,$user_timezone_offset,$record) {
	
	$options = get_option('wp_shabbat_settings');
	
	$sunsetLocalTime = date_sunset($user_time, SUNFUNCS_RET_TIMESTAMP, $record->latitude, $record->longitude, ini_get("date.sunset_zenith"), $user_timezone_offset) ;
	
	$userCandleMin = $options['Candle'];
	
	$candleLightTime = $sunsetLocalTime - (60*$userCandleMin);// 60*20 reduce 20 minutes in timstamp
	
	return ($candleLightTime+$user_timezone_offset); 
}



/**************

 calculate havdala time

 ********************/
  
function havdala($user_time,$user_timezone_offset,$record) {
	
	$options = get_option('wp_shabbat_settings');
	
	$userHavdalaMin = $options['Havdala'];
	
	$sunrise = date_sunrise($user_time, SUNFUNCS_RET_TIMESTAMP, $record->latitude, $record->longitude, ini_get("date.sunrise_zenith"), ($user_timezone_offset/3600));

	$sunset = date_sunset($user_time, SUNFUNCS_RET_TIMESTAMP, $record->latitude, $record->longitude, ini_get("date.sunset_zenith"), ($user_timezone_offset/3600));

	$minutes = round(abs($sunset - $sunrise) / 60,2);

	$jewishHour = $minutes /12;


	//$havdala = $sunset + $jewishHour/4*60 ;
	if ($userHavdalaMin == 60) {
			$havdala = $sunset + $jewishHour*60  ;
			
	} else {	
		if ($userHavdalaMin > 60) {
			$havdala = $sunset + $jewishHour*60 + $jewishHour/(60/($userHavdalaMin-60))*60 ;
			
		} else {
	
			$havdala = $sunset + $jewishHour/(60/$userHavdalaMin)*60 ;
			
		
			}
	}


	return round(abs($havdala+$user_timezone_offset));   // in timestamp
}


/************************

	hebrew date

**********************/

function isHoliday(){



$hebrewDate = jdtojewish(gregoriantojd( date('m'), date('d'), date('Y')), true, CAL_JEWISH_ADD_GERESHAYIM); // for today

$hebrewDate = iconv ('WINDOWS-1255', 'UTF-8', $hebrewDate); // convert to utf-8



    switch($hebrewDate) {
	// בדיקת יום כניסת חג ערב חג
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/כ"ט אלול/'), $hebrewDate) == 1:// ראש השנה א
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/ט' תשרי/"), $hebrewDate) == 1:// יום כיפור
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/י"ד תשרי/'), $hebrewDate) == 1:// סוכות
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/כ"א תשרי/'), $hebrewDate) == 1:// שמיני עצרת
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/י"ד ניסן/'), $hebrewDate) == 1:// פסח
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/כ' ניסן/"), $hebrewDate) == 1:// שביעי של פסח
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/ה' סיון/"), $hebrewDate) == 1://שבועות
	
	  $eveHoliday = 'eveHoliday';
	  return $eveHoliday;
	  break;
	
	// בדיקת חג ראשון
    
    case preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/א' תשרי/"), $hebrewDate) == 1:// ראש השנה א
    case preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/ב' תשרי/"), $hebrewDate) == 1:// ראש השנה ב
    case preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/י' תשרי/"), $hebrewDate) == 1:// יום כיפור
    case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/ט"ו תשרי/'), $hebrewDate) == 1:// סוכות
    case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/כ"ב תשרי/'), $hebrewDate) == 1:// שמיני עצרת
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/ט"ו ניסן/'), $hebrewDate) == 1:// פסח
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/כ"א ניסן/'), $hebrewDate) == 1:// שביעי של פסח
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/ו' סיון/"), $hebrewDate) == 1://שבועות
	
	
	
      $firstHoliday = 'first';
	  return $firstHoliday;
	  break;
	  
    // בדיקת חג שני
    
    case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/ט"ז תשרי/'), $hebrewDate) == 1:// סוכות
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/כ"ג תשרי/'), $hebrewDate) == 1:// שמיני עצרת
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/ט"ז ניסן/'), $hebrewDate) == 1:// פסח
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', '/כ"ב ניסן/'), $hebrewDate) == 1:// שביעי של פסח
	case preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/ז' ניסן/"), $hebrewDate) == 1://שבועות
	
	
     $secondHoliday = 'second';
     return $secondHoliday = 'second';
    
      break;
    
	  }
}

/************************

	the whole script check user and redirect

**********************/
$redirect = site_url().'/?WP-Shabbat=Shabbat-Closed-Page&redirectReason='; //redirect uri
$enterTime = candleTime($user_time,$user_timezone_offset,$record);
global $exitTime;
global $dayDetails; // day details for announcement popup
global $dayTimeComeBack; // day details for announcement popup
$exitTime = havdala($user_time,$user_timezone_offset,$record);

$diff = $enterTime - $user_time ;// get the diffrance between user time and enter time to display later countdown
intval($diff);

$options = get_option('wp_shabbat_settings');

	
/* test closed page
if ( 1 == 1  ){ 
					
		if ( $options['announcement'] == 1  ){ // check if announcement active
			$dayDetails = __('Holiday','WP-Shabbat');
			$dayTimeComeBack = __('tommorow at ','WP-Shabbat');
			include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
			
		}else{
			header('Location:'.$redirect.__('Holiday','WP-Shabbat').'&opentime='.__('tommorow at ','WP-Shabbat').date('H:i',$exitTime).'&exitTime='.$exitTime.'');    // redirect to url
		echo 'israel eveHoliday<br/>';
	}
					 
}		
*/
/* test countdown 
if ( 1 == 1  ){ 
					
		if ( $options['announcement'] == 1  ){ // check if announcement active
			$dayDetails = __('Holiday','WP-Shabbat');
			$dayTimeComeBack = __('tommorow at ','WP-Shabbat');
			include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
			
		}else{
			if ( ($diff <= 3600) && (0 < $diff) ) {
							$dayDetails = __('Holiday','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-countdown.php'); // add countdown 
	}
					 
}	
}	
*/


    if ($record->country_code3 == 'ISR' )  // if the user from israel
	{	
		if ( !is_null(isHoliday()) ) {
			if (isHoliday() == 'eveHoliday') {
				if ( $user_time > $enterTime  ){ // check time
					if ( $options['announcement'] == 1  ){ // check if announcement active
						$dayDetails = __('Holiday','WP-Shabbat');
						$dayTimeComeBack = __('tommorow at ','WP-Shabbat');
						include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
						
					}else{
						header('Location: '. $redirect .__('Holiday','WP-Shabbat').'&opentime='.__('tommorow at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
						//echo 'israel eveHoliday<br/>';
						}
				} else { // show user countdown until site closes one hour before closing
						if ( ($diff <= 3600) && (0 < $diff) ) {
							$dayDetails = __('Holiday','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-countdown.php'); // add countdown 
						}
				}
			}
		
			if (isHoliday() == 'first') {
				if ( $user_time < $exitTime  ){ // check time
					if ( preg_match(iconv ('WINDOWS-1255', 'UTF-8', "/א' תשרי/"), $hebrewDate) == 1  ){ // check if first day of rosh hashana which has second day
						if ( $options['announcement'] == 1  ){ // check if announcement active
							$dayDetails = __('Holiday','WP-Shabbat');
							$dayTimeComeBack = __('two day at ','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
						}else{
							header('Location: '. $redirect .__('Holiday','WP-Shabbat').'&opentime='.__('two day at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
							//echo 'israel first<br/>';
						}
					}else{ // not rosh hashana, all other holidays
							if ( $options['announcement'] == 1  ){ // check if announcement active
									$dayDetails = __('Holiday','WP-Shabbat');
									$dayTimeComeBack = __('at ','WP-Shabbat');
									include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
									
								}else{
									header('Location: '. $redirect .__('Holiday','WP-Shabbat').'&opentime='.__('at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
									//echo 'israel first<br/>';
								}
					}	
				}		
			}
			
		}
		else { // check shabas
			if ($user_localday == 'Fri') {
				if ( $user_time > $enterTime ){ // check time
					if ( $options['announcement'] == 1  ){ // check if announcement active
						$dayDetails = __('Shabbat','WP-Shabbat');
						$dayTimeComeBack = __('tommorow at ','WP-Shabbat');
						include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
						
					}else{
						header('Location: '. $redirect .__('Shabbat','WP-Shabbat').'&opentime='.__('tommorow at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
					}
					
				} else { // show user countdown until site closes one hour before closing
						if ( ($diff <= 3600) && (0 < $diff) ) {
							$dayDetails = __('Shabbat','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-countdown.php'); // add countdown 
						}
				}		
			}
			if ($user_localday == 'Sat') {
				if ( $user_time < $exitTime ){ // check time
					if ( $options['announcement'] == 1  ){ // check if announcement active
						$dayDetails = __('Shabbat','WP-Shabbat');
						$dayTimeComeBack = __('at ','WP-Shabbat');
						include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
						
					}else{
						header('Location: '. $redirect .__('Shabbat','WP-Shabbat').'&opentime='.__('at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
					}
					
				}	
			}
		}
	}
	else //  not israel
	{
	if ( !is_null(isHoliday()) ) {
			if (isHoliday() == 'eveHoliday') {
				if ($user_time > $enterTime){ // check time
					if ( $options['announcement'] == 1  ){ // check if announcement active
							$dayDetails = __('Holiday','WP-Shabbat');
							$dayTimeComeBack = __('two day at ','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
							
					}else{
					header('Location: '. $redirect .__('Holiday','WP-Shabbat').'&opentime='.__('two day at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
					//echo 'eveHoliday<br/>';
					}
				} else { // show user countdown until site closes one hour before closing
						if ( ($diff <= 3600) && (0 < $diff) ) {
							$dayDetails = __('Holiday','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-countdown.php'); // add countdown 
						}
				}		
			}
			if (isHoliday() == 'first') {
					if ( $options['announcement'] == 1  ){ // check if announcement active
							$dayDetails = __('Holiday','WP-Shabbat');
							$dayTimeComeBack = __('tommorow at ','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
							
					}else{
						header('Location: '. $redirect .__('Holiday','WP-Shabbat').'&opentime='.__('tommorow at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
						//echo 'first<br/>';
					}
						
			}
			if (isHoliday() == 'second') {
				if ($user_time < $exitTime){ // check time
					if ( $options['announcement'] == 1  ){ // check if announcement active
							$dayDetails = __('Holiday','WP-Shabbat');
							$dayTimeComeBack = __('at ','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
							
					}else{
					header('Location: '. $redirect .__('Holiday','WP-Shabbat').'&opentime='.__('at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
					//echo 'second<br/>';
					}
				}		
			}
		}
		else { // check shabas
			if ($user_localday == 'Fri') {
				if ($user_time > $enterTime){ // check time
					if ( $options['announcement'] == 1  ){ // check if announcement active
							$dayDetails = __('Shabbat','WP-Shabbat');
							$dayTimeComeBack = __('tommorow at ','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
							
					}else{
					header('Location: '. $redirect .__('Shabbat','WP-Shabbat').'&opentime='.__('tommorow at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
					}
					
				} else { // show user countdown until site closes one hour before closing
						if ( ($diff <= 3600) && (0 < $diff) ) {
							$dayDetails = __('Shabbat','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-countdown.php'); // add countdown 
						}
				}		
			}
			if ($user_localday == 'Sat') {
				if ($user_time < $exitTime){ // check time
					if ( $options['announcement'] == 1  ){ // check if announcement active
							$dayDetails = __('Shabbat','WP-Shabbat');
							$dayTimeComeBack = __('at ','WP-Shabbat');
							include( plugin_dir_path( __FILE__ ) . 'wp-shabbat-popup.php'); // add the popup page
							
					}else{
					header('Location: '. $redirect .__('Shabbat','WP-Shabbat').'&opentime='.__('at ','WP-Shabbat').date('H:i',$exitTime));    // redirect to url
					}
					
				}	
			}
		}	
	}
	




?>