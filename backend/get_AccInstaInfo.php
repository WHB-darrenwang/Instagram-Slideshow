<?php
/***********************
 * @author Darren Wang *
 * @date 04.03.2019    *
 ***********************/

/* @summary uses http requests and regular expression to return URLs for the first 12 photos/video snapshot 
	that are ON his/her/its/their timeline. */

#Instagram account username
$username = $_GET['user'];

#Set true if you want image recognition information on
$image_rec = true;

#Set to true if you are testing with the same account. Therefore, there are no repeated html requests and instead, the other iterations will be read from a file
$file_efficiency = false;

#Route path 
define("IG_INFO", "ig_info.txt");

$info = NULL;

#developer tool for URL request efficiency
$resume = true;
if($file_efficiency){
	$resume = (filesize(IG_INFO) == 0);
} 

#GET JSON from http 
if($resume){
	$info = file_get_contents('https://www.instagram.com/' . $username);
	#Extract data from JSON and write into 'ig_info.txt'. This use of RE eliminates photos from stories and keeps ONLY photos on the timeline
	if (preg_match('#timeline_media":(.*),"felix_onboarding#', $info, $matches)){
		file_put_contents(IG_INFO, $matches[1]);
		$info = $matches[1];
	} else{
		die('Account does not have enough photos for IG to release url for the photos.');
	}
	#If 'ig_info.txt' has text, GET JSON from file
} else{
	$info = file_get_contents(IG_INFO);
}

#Initialize the array for the urls of IG photos
$urls = [];
$objects_in_pic = [];
$urlnames = "";

#Extract picture URLs from $info
$pattern = '#thumbnail_src":"(.*?)","thu#';
if($image_rec) $pattern = '#_src":"(.*?)","thu[^|]*?ain: (.*?)"#';
if(preg_match_all($pattern, $info, $match)){
	foreach ($match[1] as $url) {
		$urls[] = $url;
		$urlnames .= '<a>' . $url . '</a> <br><br><br>';
	}
	if($image_rec){
		foreach ($match[2] as $obj){
			$objects_in_pic[] = $obj;
		}
	}
}

#If there were no URLs extracted, return a message
if($urlnames == ""){
	$urlnames = '<a>Account does not have enough photos for IG to release url for the photos OR the account is private.</a>';
}
$final_arr = array();
$final_arr['url'] = $urls;
if($image_rec) $final_arr['obj'] = $objects_in_pic;

#Returns a JSON string of URLs and if image_rec set, image recognition text will return too
echo json_encode($final_arr);
?>
