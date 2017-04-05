<?php

/* ************************************************************************* */
/* ************************************************************************* */
/* ************************************************************************* */

function mysql_prep( $value ) {
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
	if ( $new_enough_php ) { // PHP v4.3.0 or higher
		// undo any magic quote effects so mysql_real_escape_string can do the work
		if ( $magic_quotes_active ) { $value = stripslashes( $value ); }
		$value = mysql_real_escape_string( $value );
	} else { // before PHP v4.3.0
		// if magic quotes aren't already on then add slashes manually
		if ( !$magic_quotes_active ) { $value = addslashes( $value ); }
		// if magic quotes are active, then the slashes already exist
	}
	return $value;
}

function redirect_to( $location = NULL ) {
	if ($location != NULL) {
		header("Location: {$location}");
		exit;
	}
}

function confirm_query($result_set) {
	if (!$result_set) {
		die("Database Query Failed: " . mysql_error());
	}
}

function strip_zeros_from_date( $marked_string="" ) {
	// first remove the marked zeros
	$no_zeros = str_replace('*0', '', $marked_string);
	// then remove any remaining marks
	$cleaned_string = str_replace('*', '', $no_zeros);
	return $cleaned_string;
}

function output_message($message="") {
	if (!empty($message)) {
		return "<p class=\"message\">{$message}</p>";
	} else {
		return "";
	}
}

function __autoload($class_name) {
	$class_name = strtolower($class_name);
	$path = LIB_PATH.DS."{$class_name}.php";
	if (file_exists($path)) {
		require_once($path);
	} else {
		die("The file {$class_name}.php could not be found.");
	}
}

function include_layout_template($template="") {
	include(SITE_ROOT.DS.'public'.DS.$template);
}

// Sanitize strings for use in image urls
function sanitize($string = '', $is_filename = FALSE) {
 // Replace all weird characters with dashes
 $string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '', $string);

 // Only allow one dash separator at a time (and make string lowercase)
 return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
}

/* ************************************************************************* */
/* ************************************************************************* */
/* ************************************************************************* */

function log_action($action, $user_id, $message="") {
	$tracking = new Tracking;
	
	$tracking->action = $action;
	/* $tracking->user_id = $user_id; */
	$tracking->text = $message;
	$tracking->entry_op = $user_id;
	$tracking->entry_dt = strftime("%Y-%m-%d %H:%M:%S", time());
	if ( !$tracking->save() ) {
		$session->message("Error creating tracking record!");
	}
}

/*
function log_action($action, $message="") {
	// Make sure file exists or else create a new file
	// Remember:  SITE_ROOT and DS
	$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
	$new = file_exists($logfile) ? false : true;
	// Make sure file is writable or else output an error
	if ($handle = fopen($logfile, 'a')) { // append
		
		// Entries look like:  2009-01-01 13:10:03 | Login: dpenttila logged in
		$timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
		// Consider how to handle new lines (double quotes matter)
		$content = "{$timestamp} | {$action}: {$message}\n";
		// Append new entries to the end of the file
		fwrite($handle, $content);
		fclose($handle);
		if ($new) { chmod($logfile, 0755); }
	} else {
		$session->message("Could not open log file for writing.");
	}
}
*/

function datetime_to_text($datetime="") {
	$unixdatetime = strtotime($datetime);
	return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function format_phone($number) {
	if ( !empty($number) ) {
		return "(" . substr($number, 0, 3) . ") " . substr($number, 3, 3) . "-" . substr($number, 6);
	} else {
		return "";
	}
}

function time_since($since) {
	$chunks = array(
			array(60 * 60 * 24 * 365 , 'year'),
			array(60 * 60 * 24 * 30 , 'month'),
			array(60 * 60 * 24 * 7, 'week'),
			array(60 * 60 * 24 , 'day'),
			array(60 * 60 , 'hour'),
			array(60 , 'minute'),
			array(1 , 'second')
	);
	for ($i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		if (($count = floor($since / $seconds)) != 0) {
			break;
		}
	}
	$print = ($count == 1) ? '1 '.$name : "$count {$name}s";
	return $print;
}

function print_states($sel_state) {
	$states = array(
		array("AL", "Alabama"),
		array("AK", "Alaska"),
		array("AZ", "Arizona"),
		array("AR", "Arkansas"),
		array("CA", "California"),
		array("CO", "Colorado"),
		array("CT", "Connecticut"),
		array("DE", "Delaware"),
		array("DC", "District of Columbia"),
		array("FL", "Florida"),
		array("GA", "Georgia"),
		array("HI", "Hawaii"),
		array("ID", "Idaho"),
		array("IL", "Illinois"),
		array("IN", "Indiana"),
		array("IA", "Iowa"),
		array("KS", "Kansas"),
		array("KY", "Kentucky"),
		array("LA", "Louisiana"),
		array("ME", "Maine"),
		array("MD", "Maryland"),
		array("MA", "Massachusetts"),
		array("MI", "Michigan"),
		array("MN", "Minnesota"),
		array("MS", "Mississippi"),
		array("MO", "Missouri"),
		array("MT", "Montana"),
		array("NE", "Nebraska"),
		array("NV", "Nevada"),
		array("NH", "New Hampshire"),
		array("NJ", "New Jersey"),
		array("NM", "New Mexico"),
		array("NY", "New York"),
		array("NC", "North Carolina"),
		array("ND", "North Dakota"),
		array("OH", "Ohio"),
		array("OK", "Oklahoma"),
		array("OR", "Oregon"),
		array("PA", "Pennsylvania"),
		array("RI", "Rhode Island"),
		array("SC", "South Carolina"),
		array("SD", "South Dakota"),
		array("TN", "Tennessee"),
		array("TX", "Texas"),
		array("UT", "Utah"),
		array("VT", "Vermont"),
		array("VA", "Virginia"),
		array("WA", "Washington"),
		array("WV", "West Virginia"),
		array("WI", "Wisconsin"),
		array("WY", "Wyoming")
  );
	
	print '<select name="state" id="state">'; // Open the select box.
	
	// Uncomment the below line to make the first option "--SELECT--".
	// print '<option value="XX">--SELECT--</option>\n";
	 
	// Cycle through the array, printing each option.
	if ( empty($sel_state) ) { $sel_state = ""; }
	foreach ($states as $s) {
		print '<option value="' . $s[0] . '"' .($sel_state == $s[0] ? ' selected="selected"' : ''). '>' . $s[1] . "</option>\n";
	}
	/*
	foreach ($states as $s) {
		print '<option value="' . $s[0] . '">' . $s[1] . "</option>\n";
	}
	*/
	print "</select>"; // Close the select box.
}


/*
	getimg function pulls image from specified url into the /public/uploads folder
*/
/*
function getimg($url) {         
	$headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';              
	$headers[] = 'Connection: Keep-Alive';         
	$headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';         
	$user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';         
	$process = curl_init($url);         
	curl_setopt($process, CURLOPT_HTTPHEADER, $headers);         
	curl_setopt($process, CURLOPT_HEADER, 0);         
	curl_setopt($process, CURLOPT_USERAGENT, $user_agent);         
	curl_setopt($process, CURLOPT_TIMEOUT, 30);         
	curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);         
	curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);         
	$return = curl_exec($process);         
	curl_close($process);         
	return $return;     
} 
*/

/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function haversineGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}

/**
 * Calculates the great-circle distance between two points, with
 * the Vincenty formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function vincentyGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  $angle = atan2(sqrt($a), $b);
  return $angle * $earthRadius;
}

?>