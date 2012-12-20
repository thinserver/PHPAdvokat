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

/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

$changecheck="";

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);




echo "<HTML><HEAD><TITLE>PHPAdvocat - Statistiken</TITLE>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";
echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

/* Begin framework: table with two colunms, */
/* menu on left with size 200, rest on right */
echo "<TABLE width=100%><TR><TD width=200 valign=top>\n";

  $phpa_menue->account=$_SESSION["dbuser"];
  $phpa_menue->selected = 3;
  $phpa_menue->draw_menue();

echo "</TD><TD>\n"; /* end menue, start dialog */

/* display title */
echo "<CENTER><H1>Statistiken</H1></CENTER>\n";

echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT></TD>";
print "</tr></table><hr>\n";

/* here comes the table with statistics */
print "<center>";
printf("<table class=listtable><tr>\n");


/* evaluate overall amount */
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
printf("<td><a href=bookkeepingrequest.php>Stand aller Konten:</a></td>");
printf("<td align=right bgcolor=%s>\n<b>%s &euro;</b></td>",
       $color, tolocalnum(sprintf('%.2f',$demand),$LOCALE));
printf("</td>");
printf("</tr><tr>\n");


/* evaluate amount of all expends of files */
$demand = 0.0;
$querystring=sprintf("select sum(am.incomingamount)-sum(am.outgoingamount) as sum ".
             "from phpa_amounts as am, phpa_expenditures as ex ".
             "where am.number=ex.amount or am.number=ex.vatamount");
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
printf("<td><a href=expendlist.php>Stand der Kostenblätter:</a></td>");
printf("<td align=right bgcolor=%s>\n<b>%s &euro;</b></td>",
       $color, tolocalnum(sprintf('%.2f',$demand),$LOCALE));
printf("</td>");
printf("</tr><tr>\n");


/* evaluate open files */
$querystring=sprintf("select count(number) as sum ".
                     "from phpa_pfiles where enddate is null");
$db->query($querystring);
$pfiles=0;
/* fill $demand only if database query is not null */
if($db->next_record() && ($db->record["sum"] != '')) {
   $pfiles = $db->record["sum"];
} /* end query */
printf("<td><a href=filesrequest.php>Offene Akten:</a></td>");
printf("<td align=right>\n<b>%s</b></td>",$pfiles);
printf("</td>");
printf("</tr><tr>\n");


/* evaluate open invoices */
$querystring=sprintf("select count(number) as sum ".
                     "from phpa_invoices where paydate is null");
$db->query($querystring);
$invoices=0;
/* fill $demand only if database query is not null */
if($db->next_record() && ($db->record["sum"] != '')) {
   $invoices = $db->record["sum"];
} /* end query */
printf("<td><a href=invoicelist.php>Offene Rechnungen:</a></td>");
printf("<td align=right>\n<b>%s</b></td>",$invoices);
printf("</td>");
printf("</tr><tr>\n");


/* evaluate taxes */
$querystring=sprintf("select sum(incomingamount-outgoingamount) as sum ".
                     "from phpa_amounts where exp_category >= 3800 and exp_category < 3900");
$db->query($querystring);
$invoices=0;
/* fill $demand only if database query is not null */
if($db->next_record() && ($db->record["sum"] != '')) {
   $taxes = $db->record["sum"];
} /* end query */

printf("<td><a href=vatlist.php>Umsatz-Steuer:</a></td>");
printf("<td align=right>\n<b>%s</b></td>",$taxes);
printf("</td>");


printf("</tr></table>\n");


echo "</TD></TR></TABLE>\n"; /* end of table framework */
echo "</BODY></HTML>\n"; /* end of page */
?>
