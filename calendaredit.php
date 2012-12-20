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
require("./include/dialog.php");
/* require("./include/phpcalendar.inc.php"); display_month(date("Y"), date("n"));*/

/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

$changecheck="";

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);


echo "<HTML><HEAD><TITLE>PHPAdvocat - Termin</TITLE>\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";

echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

/* Begin framework: table with two colunms, */
/* menu on left with suze 200, rest on right */
echo "<TABLE width=100%><TR><TD width=200 valign=top>\n";

  $phpa_menue->account=$_SESSION["dbuser"];
  $phpa_menue->selected = 3;
   array_insert($phpa_menue->contents,
      array('&nbsp;&nbsp;<b>Termin</b>'), 3);

  $phpa_menue->draw_menue();
echo "</TD><TD>\n"; /* end menue, start dialog */

/* first evaluate the desired date to display */
if ($hour == '') {$hour = $_GET['hour']; $date = substr($_GET['hour'],0,8);};
if ($hour == '') {$hour = date('YmdH',time()).'00';};
 $date = substr($hour,0,8);

  // echo '<hr>'.$hour.'|'.$date.'<hr>';

  /* split date string into parts */
      $startday = substr($hour,6,2); /*hour=200502192345*/
      $startmonth = substr($hour,4,2);
      $startyear = substr($hour,0,4);
      $starthour = substr($hour,8,2);
      $startmin = substr($hour,10,2);

  $eventstart = sprintf("%s-%s-%s %s:%s:00-00",
  $startyear, $startmonth, $startday, $starthour, $startmin);


/* display title */
echo "<CENTER><H1>Termin</H1></CENTER>\n";

/* display headline */
echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT>";
echo "<a href=calendar.php?date=".$date.">Tagesansicht</a></TD>";
echo "</tr></table>\n";

print "<hr><center>";

/* display current event */

  if (isset($hour)) {
    $querystring = sprintf("select number, eventstart, ".
       "description, location, pfile ".
       "from phpa_events ".
       "where phpa_events.eventstart = '%s' ",
       $eventstart);
  }
   // echo "<hr>" . $querystring . "<hr>";
  $db->query($querystring);
  
  /* in order to find out if a event has been found, set numer to 0 */
  $eventnummer=0;

   /* Try to find a matching event and put in in variables */
   while($db->next_record()) {
       $eventnumber=
           $db->record["number"];
       $starthour=
            substr($db->record["eventstart"],11,2);
       $startmin=
            substr($db->record["eventstart"],14,2);

       $eventdescription=
           $db->record["description"];
       $eventlocation=
           $db->record["location"];
       $eventpfile=
           $db->record["pfile"];
   } /* end while */


   /* enter an event for the current day */
  $calendardialog = new htmldialog;

  /* normally set to PHP_SELF ist has to be modified to link to calendar.php */
  $calendardialog->stringbegin = sprintf("<center><table  class=inputtable><tr><td>\n".
                  "<FORM METHOD=POST ACTION=calendar.php><table>\n");

   /* start with date */
   $optionlist = sprintf("<select name=start_day>\n");
    for($oday=1;$oday<=31;$oday++){
       if ($startday == $oday){
         $optionlist .= sprintf("<option selected>%02.0f\n</option>",$oday);
       } else {
         $optionlist .= sprintf("<option>%02.0f\n</option>",$oday);
       }
    }
    $optionlist .= sprintf("</select>\n");
			
    $optionlist .= sprintf("<select name=start_month>\n");
    for($omonth=1;$omonth<=12;$omonth++){
       if ($startmonth == $omonth){
         $optionlist .= sprintf("<option selected>%02.0f\n</option>",$omonth);
       } else {
         $optionlist .= sprintf("<option>%02.0f\n</option>",$omonth);
       }
    }
    $optionlist .= sprintf("</select>\n");

    $optionlist .= sprintf("<select name=start_year>\n");
    $minyear = $startyear-10;
    $maxyear = $startyear+10;
    for($oyear=$minyear;$oyear<=$maxyear;$oyear++){
       if ($startyear == $oyear){
         $optionlist .= sprintf("<option selected>%.0f\n</option>",$oyear);
       } else {
         $optionlist .= sprintf("<option>%.0f\n</option>",$oyear);
       }
    }
    $optionlist .= sprintf("</select>\n");
  
    /* some hidden data and the beginning */                       
    $calendardialog->addinput("Datum:",
       sprintf("<input name=date type=hidden value=%s>", $date).
       sprintf("<input name=eventnumber type=hidden value=%s>", $eventnumber).
       $optionlist);
 
 
  /* make list for hours */
  $optionlist = sprintf("<select name=start_hour>\n");
  for($ohour=0;$ohour<=23;$ohour++){
     if ($starthour == $ohour){
       $optionlist .= sprintf("<option selected>%2.0f\n</option>",$ohour);
     } else {
       $optionlist .= sprintf("<option>%02.0f\n</option>",$ohour);
     }
  }
  $optionlist .= sprintf("</select>\n");

  /* make list for minutes */
  $optionlist .= sprintf("<select name=start_minute>\n");
  for($ominute=0;$ominute<=30;$ominute+=30){
     if ($startmin == $ominute){
       $optionlist .= sprintf("<option selected>%02.0f\n</option>",$ominute);
     } else {
       $optionlist .= sprintf("<option>%02.0f\n</option>",$ominute);
     }
  }
  $optionlist .= sprintf("</select>\n");

  $calendardialog->addinput("Zeit:",  $optionlist);



  /* Description of event */
  $calendardialog->addinput("Betreff",
    sprintf("<input name=description type=text size=30 value=\"%s\">\n",
         $eventdescription));
  $calendardialog->addinput("Ort",
    sprintf("<input name=location type=text size=15 value=\"%s\">\n",
         $eventlocation));
  
  /* select one file for the new event */
  $querystring = sprintf("select number, processregister from phpa_pfiles ".
    "order by processregister desc");
  $db->query($querystring);

  $optionlist = sprintf("<select name=pnumber>\n");
  // printf("<option value=NULL>- keine -</option>\n");

  while($db->next_record()) {
    if($db->record["number"] == $eventpfile){
      $optionlist .= sprintf("<option selected value=%s>%s (%s)</option>\n",
        $db->record["number"],$db->record["processregister"],
        $db->record["number"]);
    } else {
      $optionlist .= sprintf("<option value=%s>%s (%s)</option>\n",
        $db->record["number"],$db->record["processregister"],
        $db->record["number"]);
    }
  }
  printf("</select>\n");

  $calendardialog->addinput("Akte:",  $optionlist);


  $calendardialog->addinput("L&ouml;schen:",
       sprintf("<input type=checkbox name=eventdel>"));

  $calendardialog->addinput("<input name=eventaddbutton type=submit value=Speichern>",
     "<input name=eventdiscardbutton type=submit value=Abbruch>");

  $calendardialog->out();
   /* End of enter a new event for the current day */

  echo "</BODY></HTML>\n"; /* end of page */
?>
