<?php
include("/home/pdespouy/php/simple_html_dom.php"); # so you can use file_get_html()

if (isset($_POST['submit'])) {

if ($_POST['submit'] == 'Get Habits!') { // add case where user does not add url : && 

	if ($_POST['userCoachmeURL']=='') {
	
		echo "<span>Please enter your URL.</span>";
	
	} else {

// users input their public coach.me URL
//$url = "https://www.coach.me/users/4c31cbb1193ac3e17d05//activity";
$preUrl = $_POST['userCoachmeURL'];
$url = trim($preUrl);  // to trim extra spaces
$html = file_get_html($url);

// function to detect JSON
function isJson($string) {
 return is_string($string) && is_object(json_decode($string)) ? true : false;
}

// this part is hard-coded and boogie. Crawls until it finds the JSON in the public profile
$script = $html->find('script[data-set-variable]', 0);
foreach($script as $elements) {
    foreach($elements as $element) {
    	$string = $element->plaintext;
    	if (isJson($string)) {
    		$jsonWithId = $string;
}}}

// extract "id" from JSON, used in the API's URL
$objA = json_decode($jsonWithId, true);
$uniqueId = $objA['id'];

$urlApiJson = "https://www.coach.me/api/v3/users/".$uniqueId."//activity";
//$urlApiJson = "https://www.coach.me/api/v3/users/".$uniqueId."//stats"; // for using the stats API/JSON
//$urlApiJson = 'http://www.pedrodz.com/pedro.json';

$stringApi = file_get_contents($urlApiJson);
$obj = json_decode($stringApi, true); // 'if' is after declaring $obj so it can be used later

/* using the stats API/JSON (in case of custom made signature):
// extract particular elements:
$plan = $obj['plans'][$nbPlan]['name'];
$checking = $obj['plans'][$nbPlan]['stats']['total_checkin_count'];
$x = 0;
foreach($obj['plans'] as $plans){
	echo "<input type='radio' name='plan' value=".$x.">Coach.me | " . $plans['name'] . " for " . 
		$plans['stats']['total_checkin_count'] . " days<br>";
	$x++;
}
*/

// "activity rich title" as signature (from 'activity' API/JSON)
$allHabits = [];
$x = 0;
$y = 0;
// retrieve all habits in list of activity. Activity JSON has ~50 elements, with duplicate of habits
foreach($obj['activity_items'] as $activity){
	$allHabits[$y] = $activity['habit_id'];
	$y++;
}
$uniqueHabits = array_unique($allHabits);  // remove duplicates

// warmth welcome:
$name = $obj['refs']['users'][0]['name'];
echo "Hi " .$name. "! Choose the habit would you like to show in your email signature:<br>";

// return options as list of radio buttons
foreach($obj['activity_items'] as $activity){
	$habit = $activity['habit_id'];
    	if (in_array($habit, $uniqueHabits)) {
    		$habitId = $uniqueHabits[$x];
    		$activityRichTitle = $activity['activity_rich_title'];
    		$activityRichTitle = strip_tags($activityRichTitle);  // strip html tags from string
  		echo "<input type='radio' name='habitIdRadio' value=".$habitId.">".$activityRichTitle."<br>";
  	  	$key = array_search($habit, $uniqueHabits);
    		unset($uniqueHabits[$key]);  // remove habits from $uniqueHabits array
	}	
	$x++;
}
echo "<input type='hidden' name='uniqueIdHidden' value=".$uniqueId.">"; // to send values to the next if
echo "<br><input type='submit' name='submit' value='Send'>";

}} else if ($_POST['submit'] == 'Send') {

# input value of plan
$nbHabitId = $_POST['habitIdRadio'];
$nbUniqueId = $_POST['uniqueIdHidden']; // example: 911327

if ($nbHabitId=='') {
echo "<br><span>Please select your habit.</span>"; 
} else {

// outputs URL
echo "<br>Copy the following URL to your email signature:<br>";
echo "http://pedrodz.com/signature.png?id=".$nbUniqueId."&habitId=".$nbHabitId;
echo "<br><br>In Gmail:<br>";
echo "1) Go to settings<br>";
echo "2) Go to signature and click on Insert Image icon<br>";
echo "<img src='image_icon.png' alt='image icon' style='width:750px;height:200px'><br>";
echo "3) Click on Web Address (URL) tab and paste the copied URL in the bar<br>";
echo "<img src='web_address.png' alt='web address' style='width:750px;height:200px'><br>";
echo "4) Save your changes<br>";
echo "5) And Voila! When you send emails your activity will show up in your signature";

// delete variables (not necessary)
unset($nbHabitId);
unset($nbUniqueId);

/*
// in case you would like to show the activity with the URL
echo $nbHabitId.'<br>';
echo $nbUniqueId.'<br>';
$urlApiJson = "https://www.coach.me/api/v3/users/".$nbUniqueId."/activity";
$stringApi = file_get_contents($urlApiJson);
$obj = json_decode($stringApi, true);
foreach($obj['activity_items'] as $activity){
    	$habit = $activity['habit_id'];
    	if ($nbHabitId == $habit) {
    		$activityRichTitle = $activity['activity_rich_title'];
    		break;
}}
$activityRichTitle = strip_tags($activityRichTitle);
$output = '<br>Coach.me | ' . $activityRichTitle;
echo $output;
*/
}}

}
else{ echo "<span>Please enter your URL.</span>";
}

?>
