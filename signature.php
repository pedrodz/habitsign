<?php

# input values from URL
$nbHabitId = $_GET['habitId'];
$nbUniqueId = $_GET['id'];

$urlApiJson = "https://www.coach.me/api/v3/users/".$nbUniqueId."/activity";
//$urlApiJson = "https://www.coach.me/api/v3/users/".$nbUniqueId."/stats"; // using the stats API/JSON
//$urlApiJson = 'http://www.pedrodz.com/pedro.json'; //test purpose

$stringApi = file_get_contents($urlApiJson);
$obj = json_decode($stringApi, true);

// using the stats API/JSON:
// extract particular elements:
//$plan = $obj['plans'][$nbPlan]['name'];
//$checking = $obj['plans'][$nbPlan]['stats']['total_checkin_count'];
// text to output
//$output = 'Coach.me | ' . $plan . ' for ' . $checking . ' days'; //. " days checked in since " . $date;  // Date is broken in JSON
//$habit = $obj['plans'][$nbPlan]['name'];
//$checking = $obj['plans'][$nbPlan]['stats']['total_checkin_count'];

foreach($obj['activity_items'] as $activity){
    	$habit = $activity['habit_id'];
    	if ($nbHabitId == $habit) {
    		$activityRichTitle = $activity['activity_rich_title'];
    		break;
}}

// text to output
$name = $obj['refs']['users'][0]['name'];
$nameExplode = explode(' ',trim($name));
$first_name = $nameExplode[0]; // will print Pedro
$activityRichTitle = strip_tags($activityRichTitle); // remove html tags from string
$output = ' Coach.me | '.$first_name.': ' . $activityRichTitle;
//$output = ' Coach.me | ' . $activityRichTitle;

// image processing
$h = 6*strlen($output); // change size of pic depending in nb characters

$my_img = imagecreate( $h, 20 );
$background = imagecolorallocate( $my_img, 255, 255, 255 );
$text_colour = imagecolorallocate( $my_img, 128, 128, 128 );
#$line_colour = imagecolorallocate( $my_img, 128, 255, 0 );
imagestring( $my_img, 2, 0, 0, $output , $text_colour );
#imagesetthickness ( $my_img, 5 );
#imageline( $my_img, 30, 45, 165, 45, $line_colour );

header( "Content-type: image/png" );
imagepng( $my_img ); # Ideally to change to png, but it should be PHP http://www.thesitewizard.com/php/create-image.shtml
#imagecolordeallocate( $line_color );
imagecolordeallocate( $text_color );
imagecolordeallocate( $background );
imagedestroy( $my_img );

// delete variables (not necessary)
unset($output);
unset($activityRichTitle);

?>
