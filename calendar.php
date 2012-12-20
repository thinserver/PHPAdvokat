<?php
  /**************************************************************************\
  * PHPAdvocat                                                               *
  * http://phpadvocat.sourceforge.net                                        *
  * By Burkhard Obergoeker <phpadvocat@obergoeker.de>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

require("./include/phpadvocat.inc.php");
require("./include/calendrier.php");
/* require("./include/phpcalendar.inc.php"); display_month(date("Y"), date("n"));*/

/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

$changecheck="";

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);

/****************** add an event -begin- ***************************/
/* add an event */
if($_POST["eventaddbutton"]) {
      /* import POST-VARS */
      $date = $_POST["date"];
      $pnumber = $_POST["pnumber"];
      $eventnumber = $_POST["eventnumber"];
      $start_day = $_POST["start_day"]; /*date=20050219*/
      $start_month = $_POST["start_month"];
      $start_year = $_POST["start_year"];
      $start_hour = $_POST["start_hour"];
      $start_minute = $_POST["start_minute"];
      $description = $_POST["description"];
      $location = $_POST["location"];
      $eventdel = $_POST["eventdel"];

      /* set detail for display */
      $detail=1;
      /* check parameters */
      if(checkdate($start_month, $start_day, $start_year) &&
         ($start_minute < 60) && (start_hour < 24)) {
          
         /* new date variable */
         $date=sprintf("%04.0f%02.0f%02.0f",$start_year, $start_month, $start_day);
          
         /* generate ISO-Format timestamp 1997-12-17 07:37:16-08 */
         $eventstart = sprintf("%s-%s-%s %s:%s:00-00",
          $start_year, $start_month, $start_day, $start_hour, $start_minute);
	   
         /* if eventnumer has not been set, then insert new event, */
         if($eventnumber == 0) {
           $querystring = sprintf("insert into phpa_events ".
             "(pfile, eventstart, description, location) " .
             "values(%s, '%s', '%s', '%s')", 
             $pnumber, $eventstart, $description, $location);
           $changecheck="Termin erstellt";
         } else { /* if number has been set, update event */
           if ($eventdel == "on") {
              $querystring = sprintf("delete from phpa_events ".
                "where number=%s", $eventnumber);
              $changecheck="Termin geloescht";
           } else {
              $querystring = sprintf("update phpa_events set ".
                "pfile=%s, eventstart= '%s', ".
                "description='%s', location='%s' " .
                "where number=%s", 
                $pnumber, $eventstart, $description, $location,
                $eventnumber);
              $changecheck="Termin gespeichert";
           }
         }

         if (!$db->query($querystring)) {
              // $changecheck="Termin erstellt";
         } else {
              $changecheck="Datenbankfehler";
         }
      } else {
            $changecheck="Zeit/Datum nicht korrekt";
      }
}
/****************** add an event -end- ****************************/



echo "<HTML><HEAD><TITLE>PHPAdvocat - Kalender</TITLE>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";
echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

/* Begin framework: table with two colunms, */
/* menu on left with suze 200, rest on right */
echo "<TABLE width=100%><TR><TD width=200 valign=top>\n";

  $phpa_menue->account=$_SESSION["dbuser"];
  $phpa_menue->selected = 2;
  $phpa_menue->draw_menue();

echo "</TD><TD>\n"; /* end menue, start dialog */

/* display title */
echo "<CENTER><H1>Kalender</H1></CENTER>\n";

/* display headline */
echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT></TD>";
echo "</tr></table>\n";

print "<hr><center>";

/* month and day  */
echo "<table width=100% border=0>\n";
echo "<tr>\n";

/* display current month */

/* first evaluate the desired date to display */
// if ($hour == '') {$hour = $_GET['hour']; $date = substr($_GET['hour'],0,8);};
if ($date == '') {$date = $_POST['date'];};
if ($date == '') {$date = $_GET['date'];};
if ($date == '') {$date = date('Ymd',time());};

/* split date string into parts */
$current_month = substr($date, 4 ,2);
$current_day = substr($date, 6, 2);
$current_year = substr($date, 0 ,4);

/* evaluate date for previous and next month */
$previous_month = date("Ymd",     
     mktime( 12,0,0,($current_month - 1),
          01,$current_year));
$next_month = date("Ymd",     
     mktime( 12,0,0,($current_month + 1),
          01,$current_year));


  $querystring = sprintf("select eventstart ".
       "from phpa_events ".
       "order by eventstart");

  // echo "<hr>" . $querystring . "<hr>";
  $db->query($querystring);

   /* Display all events today */
   $eventarray=array();
   $temparray=array();
   while($db->next_record()) {
       $eventstarttime=
            substr($db->record["eventstart"], 0,4).
            substr($db->record["eventstart"], 5,2).
            substr($db->record["eventstart"], 8,2);
       $eventarray[$eventstarttime] = 1;
   } /* end while */


/* set parameters for previous month */
$params['calendar_id'] = 1;
$params['nav_link'] = 1;
$params['short_day_name'] = 0;
$params['lang'] = "german";
$params['use_img']= 0;
$params['link_before_date'] = 1;
$params['link_after_date'] = 1;
$params['font_size'] =12;
//$params['today_bg_color']='white';
$params['highlight'] = $eventarray;

/* display current month */
echo "<td valign=top width=100%><center>\n";
echo calendar($date);
echo "</center></td>\n";

echo "</tr><tr><td><center><a href=$PHP_SELF>Heute</a></center></td></tr>";
/* next line displays all events for the displayed day */
echo "</tr><tr><td valign=top colspan=3><center>\n";


  $querystring = sprintf("select phpa_events.eventstart, ".
       "phpa_events.description, phpa_events.location, ".
       "phpa_events.pfile, phpa_pfiles.processregister ".
       "from phpa_events, phpa_pfiles ".
       "where phpa_events.pfile = phpa_pfiles.number ".
       "order by eventstart");

  // echo "<hr>" . $querystring . "<hr>";
  $db->query($querystring);

   /* Display all events today */
   $eventarray=array();
   $temparray=array();
   while($db->next_record()) {
       $eventstarttime=
            substr($db->record["eventstart"], 0,4).
            substr($db->record["eventstart"], 5,2).
            substr($db->record["eventstart"], 8,2).
            substr($db->record["eventstart"],11,2).
            substr($db->record["eventstart"],14,2);
       $eventtext=
           $db->record["description"].'@'.
           $db->record["location"].' ('.
           $db->record["processregister"].')';
       $eventarray[$eventstarttime] = $eventtext;
   } /* end while */
  
	/* calendar table for current day */
	echo "<hr><table><tr><td valign=top width=90%><center>\n";
	
	/* set parameters for current month */
	$params['calendar_id'] = 2;
	$params['nav_link'] = 1;
	$params['show_day'] = 1;
	$params['show_month'] = 1;
	$params['lang'] = "german";
	$params['use_img']= 0;
	$params['link_before_date'] = 1;
	$params['link_after_date'] = 1;
	$params['today_bg_color']='';
	$params['day_mode'] = 1;
	$params['cell_width'] = '90';
	$params['time_start'] = '8:00';
	$params['time_stop'] = '18:00';
	$params['highlight'] = $eventarray;
	$params['link_on_hour'] = 'calendaredit.php?hour=%%hh%%';
	$params['highlight_type'] = 'text';
	
	/* display current month */
	echo calendar($date);
	echo "</center></td></tr></table>\n";
	/* end calendar table for current day */

   echo "</center></TD></TR></TABLE>\n"; /* end of table framework */

echo "</BODY></HTML>\n"; /* end of page */
?>
