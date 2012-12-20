<?php
  /**************************************************************************\
  * PHPAdvocat                                                               *
  * http://phpadvocat.sourceforge.net                                        *
  * By Burkhard Obergoeker <phpadvocat@obergoeker.de>                       *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

require("./include/phpadvocat.inc.php");

/* Check login info and put it into session Vars */
session_start();
$loginuser = $_POST["loginuser"];
$loginpasswd = $_POST["loginpasswd"];

if ($loginuser && $loginpasswd) {
   /* test connection */
   $db = new www_db;
   $db->connect($loginuser, $loginpasswd);
   $db->close();
   
   /* if session still available register variables */
   $_SESSION["dbuser"]=$loginuser;
   $_SESSION["dbpasswd"]=$loginpasswd;
   /* the old form to register was */
   /* session_register('dbuser', 'dbpasswd'); */
} else {
   header ("Location: login.php");
   exit;
}
 
/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);




echo "<HTML><HEAD><TITLE>PHPAdvocat - Welcome</TITLE>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";
echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

/* Begin framework: table with two colunms, */
/* menu on left with size 200, rest on right */
echo "<TABLE width=100%><TR><TD width=200 valign=top>\n";

  $phpa_menue->account=$_SESSION["dbuser"];
  $phpa_menue->draw_menue();

echo "</TD><TD>\n"; /* end menue, start dialog */

/* display title */
echo "<CENTER><H1>Willkommen</H1></CENTER>\n";

echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT></TD>";
print "</tr></table>\n";

print "<hr><center>";
print ("<h2>Heutige Termine</h2>\n");

     $today = date("d.m.Y", time());
     if ($DBSERVER=='POSTGRESQL'){
     $querystring = sprintf("select phpa_events.eventstart, ".
          "phpa_events.description, phpa_events.location, ".
          "phpa_events.pfile, phpa_pfiles.processregister ".
          "from phpa_events, phpa_pfiles ".
          "where date(eventstart) = (date 'today') ".
          "and phpa_events.pfile = phpa_pfiles.number ".
          "order by eventstart");
     } elseif ($DBSERVER=='MYSQL'){     
     $querystring = sprintf("select phpa_events.eventstart, ".
          "phpa_events.description, phpa_events.location, ".
          "phpa_events.pfile, phpa_pfiles.processregister ".
          "from phpa_events, phpa_pfiles ".
          "where DATE_FORMAT(eventstart,'%%Y-%%m-%%d') = CURDATE() ".
          "and phpa_events.pfile = phpa_pfiles.number ".
          "order by eventstart");
     }
      // echo "<hr>" . $querystring . "<hr>";
      $db->query($querystring);

      printf("<table class=listtable>\n");
      /* Display header */
      printf("<tr><th>Zeit</th>".
             "<th>Beschreibung</th><th>Ort</th>".
             "<th>Akte</th></tr>");
                       
      /* Display all events today */
      while($db->next_record()) {
           printf("<tr><td>%s</td>", 
               substr($db->record["eventstart"],11,5));
           printf("<td>%s</td>", $db->record["description"]);
           printf("<td>%s</td>", $db->record["location"]);
           printf("<td><a href=\"pfileedit.php?pnumber=%s&detail=1#detail\" " .
              ">%s (%s)</a></td></tr>\n",
              $db->record["pfile"], $db->record["processregister"],
              $db->record["pfile"]);
       } /* end while */
       echo "</table>";


$demand = 0.0;
$querystring=sprintf("select sum(incomingamount)-sum(outgoingamount) as sum ".
                     "from phpa_amounts");
$db->query($querystring);

/* fill $demand only if database query is not null */
if($db->next_record() && ($db->record["sum"] != '')) {
   $demand = $db->record["sum"];
} /* end query */

/* mark positive amount as green */
if($demand < 0) {
   $color="red";
} else {
   $color="green";
}
/* display it */
printf("<hr><table><tr><td bgcolor=%s>\n", $color);
printf("<b>Stand Gewinn/Verlust: %s &euro;</b>", 
    tolocalnum(sprintf('%.2f',$demand),$LOCALE));
printf("</tr><tr><td><center><a href=filesrequest.php>Zu den Akten</a></center></td>");
printf("</td></tr></table>\n");

echo "</TD></TR></TABLE>\n"; /* end of table framework */

echo "</BODY></HTML>\n"; /* end of page */
?>
