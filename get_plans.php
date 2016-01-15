<?php
if (isset($_POST['submit'])) {

if ($_POST['submit'] == 'Fetch habits') { // add case where user does not add url : && 

    if ($_POST['userCoachmeURL']=='') {
    
        echo "<span>Please enter your URL.</span>";
    
    } else {

// users input their public coach.me URL
//$url = "https://www.coach.me/users/4c31cbb1193ac3e17d05/activity";
$preUrl = $_POST['userCoachmeURL'];
$url = trim($preUrl);  // to trim extra spaces
 
// functions as to trim after "users/" and before "/":
function after ($this, $inthat)
    {
        if (!is_bool(strpos($inthat, $this)))
        return substr($inthat, strpos($inthat,$this)+strlen($this));
    };
function before ($this, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $this));
    };
function between ($this, $that, $inthat)
    {
        return before ($that, after($this, $inthat));
    };

$profileHash = between ('users/', '/', $url);


// extract "id" from JSON, used in the API's URL
$urlApiJsonU = "https://www.coach.me/api/v3/users/".$profileHash; // get API URL for user
$stringApiU = file_get_contents($urlApiJsonU); // get contents
$objU = json_decode($stringApiU, true); // decode JSON in user
$uniqueId = $objU['id']; // extract uniqueId (profile id) to use in activities and stats

// extract JSON from activities
$urlApiJsonA = "https://www.coach.me/api/v3/users/".$uniqueId."//activity"; // get API URL for activity
$stringApiA = file_get_contents($urlApiJsonA); // get contents
$objA = json_decode($stringApiA, true); // decode json in activity

// "activity rich title" as signature (from 'activity' API/JSON)
$allHabits = [];
$x = 0;
$y = 0;
// retrieve all habits in list of activity. Activity JSON has ~50 elements, with duplicate of habits
foreach($objA['activity_items'] as $activity){
    $allHabits[$y] = $activity['habit_id'];
    $y++;
}
$uniqueHabits = array_unique($allHabits);  // remove duplicates

// warmth welcome:
$name = $objA['refs']['users'][0]['name'];
echo "Hi <b>" .$name. "</b>! Choose the habit would you like to show in your email signature:<br>";

// return options as list of radio buttons
foreach($objA['activity_items'] as $activity){
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
echo "<input type='submit' name='submit' value='Submit'>";

}} else if ($_POST['submit'] == 'Submit') {

# input value of plan
$nbHabitId = $_POST['habitIdRadio'];
$nbUniqueId = $_POST['uniqueIdHidden']; // example: 911327

if ($nbHabitId=='') {
echo "<br><span>Please select your habit.</span>"; 
} else {

// jQuery slide example: http://www.w3schools.com/jquery/jquery_slide.asp
echo "<div id='flip'><b>Gmail on a Computer:</b></div>";
echo "<div id='panel'>1) Open Gmail.<br>";
echo "2) Click the <img src='//www.google.com/help/hc/images/mail/mail_gear.png'> gear in the top right.<br>";
echo "3) Select <b>Settings</b>.<br>";
echo "4) Scroll down to the “Signature” section and click the <img src='http://signhabits.com/insert_image.png'> icon.<br>";
//echo "<img src='image_icon.png' alt='image icon' style='width:750px;height:200px'><br>";
echo "5) Click the <b>Web Address (URL)</b> tab and copy the following in the bar: <b>http://signhabits.com/signature.png?id=".$nbUniqueId."&habitId=".$nbHabitId."</b><br>";
//echo "<img src='web_address.png' alt='web address' style='width:750px;height:200px'><br>";
echo "6) Click <b>Save Changes</b> at the bottom of the page.<br></div>";

//<img url="http://pedrodz.com/habitsign/signature.png?id=716082&habitId=281185" alt="" width="400" height="20">

// delete variables (not necessary)
unset($nbHabitId);
unset($nbUniqueId);

}}

}
else{ 

// by default the form shows this:

echo "<p>Had problems finding people to track my habit changes<br>
So passively I made everyone that receives my emails an accountant<br>
I am not associated with Coach.me. Feel free to use <br><br>
<img src='//www.signhabits.com/example.png' style='border:1px dashed'></p>";

echo "<p>Log in to Coach.me, go to your profile, and copy-paste URL:<br>";
echo "<input type='text' name='userCoachmeURL' size='60' placeholder='https://www.coach.me/users/xxxxxxxxxxxxxxxxxxxx//activity'><br>";
echo "<input type='submit' name='submit' value='Fetch habits'></p><br>";

//echo "<span>https://www.coach.me/users/4c31cbb1193ac3e17d05//activity</span>";
}

?>