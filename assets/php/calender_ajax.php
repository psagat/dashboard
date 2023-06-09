<?php
 
/* Base code provided by Sarah Bailey.
Case Western Reserve University, Cleveland OH.
Please do not email me for support. Post a comment instead.
Current v 1.1
Props to commenter Matt for pointing out the maxResults parameter.
*/
 
//TO DEBUG UNCOMMENT THESE LINES
error_reporting(E_ALL);
ini_set("display_errors", 1);
 
//INCLUDE THE GOOGLE API PHP CLIENT LIBRARY FOUND HERE
//https://github.com/google/google-api-php-client
//DOWNLOAD IT AND PUT IT ON YOUR WEBSERVER IN THE ROOT FOLDER.
require_once('/var/www/html/assets/php/vendor/autoload.php'); 
 


 
//TELL GOOGLE WHAT WE'RE DOING
$client = new Google_Client();
$client->setApplicationName("My Project"); //DON'T THINK THIS MATTERS
$client->setDeveloperKey('AIzaSyAgPUBK-c6gKxg2GNEb54NVzwz3UBr7JF8'); //GET AT AT DEVELOPERS.GOOGLE.COM
$cal = new Google_Service_Calendar($client);
//THE CALENDAR ID, FOUND IN CALENDAR SETTINGS. IF YOUR CALENDAR IS THROUGH GOOGLE APPS
//YOU MAY NEED TO CHANGE THE CENTRAL SHARING SETTINGS. THE CALENDAR FOR THIS SCRIPT
//MUST HAVE ALL EVENTS VIEWABLE IN SHARING SETTINGS.
$calendarId = 'lindsaysagat@gmail.com';
//TELL GOOGLE HOW WE WANT THE EVENTS
$params = array(
//CAN'T USE TIME MIN WITHOUT SINGLEEVENTS TURNED ON,
//IT SAYS TO TREAT RECURRING EVENTS AS SINGLE EVENTS
    'singleEvents' => true,
    'orderBy' => 'startTime',
    'timeMin' => date(DateTime::ATOM),//ONLY PULL EVENTS STARTING TODAY
'maxResults' => 5 //ONLY USE THIS IF YOU WANT TO LIMIT THE NUMBER
                  //OF EVENTS DISPLAYED
 
);
//THIS IS WHERE WE ACTUALLY PUT THE RESULTS INTO A VAR
$events = $cal->events->listEvents($calendarId, $params); 
$calTimeZone = $events->timeZone; //GET THE TZ OF THE CALENDAR

//SET THE DEFAULT TIMEZONE SO PHP DOESN'T COMPLAIN. SET TO YOUR LOCAL TIME ZONE.
 date_default_timezone_set($calTimeZone);
 
 //START THE LOOP TO LIST EVENTS
    foreach ($events->getItems() as $event) {
 
        //Convert date to month and day
 
         $eventDateStr = $event->start->dateTime;
         if(empty($eventDateStr))
         {
             // it's an all day event
             $eventDateStr = $event->start->date;
         }
 
         $temp_timezone = $event->start->timeZone;
 //THIS OVERRIDES THE CALENDAR TIMEZONE IF THE EVENT HAS A SPECIAL TZ
         if (!empty($temp_timezone)) {
         $timezone = new DateTimeZone($temp_timezone); //GET THE TIME ZONE
                 //Set your default timezone in case your events don't have one
     } else { $timezone = new DateTimeZone($calTimeZone);
         }
 
         $eventdate = new DateTime($eventDateStr,$timezone);
 		 $link = $event->htmlLink;
                 $TZlink = $link . "&ctz=" . $calTimeZone; //ADD TZ TO EVENT LINK
				 							//PREVENTS GOOGLE FROM DISPLAYING EVERYTHING IN GMT
         $newmonth = $eventdate->format("M");//CONVERT REGULAR EVENT DATE TO LEGIBLE MONTH
	 $newtime = $eventdate->format("h:i a");
         $newday = $eventdate->format("j");//CONVERT REGULAR EVENT DATE TO LEGIBLE DAY
 
        ?>
		<div class="container">
			<div class="row">
			<div class="col-md-2" style="font-size:14px; box-shadow: inset 0px 1px 1px rgba(0,0,0,0.6); background: #428bca; border-radius:4px;">
        		<?php
 
  		        echo "<font color='white'>$newmonth</font>"."<br>";
        		echo "<font color='white'>$newday</font>";
	                ?>
    			</div>
			<div class="col-md-8" style="font-size:14px">
        		<a href="<?php echo $TZlink;
                	//ECHO DIRECT LINK TO EVENT
			?>">
 
	        	<?php echo"<font color='black' font face='exolight'> $event->summary</font>"."<br>"; //SUMMARY = TITLE
			echo"<font color='black' font face='exolight'> $newtime</font>";
 
        		?>
        		</a>
    			</div>
			</div>



</div>
                        <br />


 <?php
  }
 
?>
