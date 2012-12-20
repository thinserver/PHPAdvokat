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

/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

$changecheck="";

/* initiate database */
$db = new www_db;
$db->connect($user, $passwd);



/* Begin HTML page */
echo "<HTML><HEAD>";
echo "<TITLE>PHPAdvocat - Stand Kostenbl&auml;tter</TITLE>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";
echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

/* create frame; left side for menu */
echo "<TABLE width=100%><TR><TD width=200 valign=\"top\">\n";

/* here comes the navigation menue */

  $phpa_menue->account=$user;
  $phpa_menue->selected=4;
   array_insert($phpa_menue->contents,
      array('&nbsp;&nbsp;<b>Kostenbl&auml;tter</b>'), 4);
  $phpa_menue->draw_menue();


echo "</TD><TD>\n";

/* display title */
echo "<CENTER><H1>Stand Kostenbl&auml;tter</H1></CENTER>\n";


echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT>".$changecheck ."</TD>";
print "</tr></table>\n";

print "<hr><center>";

$querystring = 
  "select p.number as pnumber, ".
  "p.processregister as processregister, " .
  "p.subject as subject, " .
  "sum(am.incomingamount - am.outgoingamount) as sum ".
  "from phpa_pfiles p, phpa_expenditures as ex, phpa_amounts as am " .
  "where p.number=ex.pfile and p.enddate is null ".
  "and (ex.amount = am.number or ex.vatamount = am.number) ".
  "group by pnumber, processregister, subject ";
  

/* Sort by table header */
switch ($_GET["fsort"]) {

  case "number"  :$querystring .= "order by processregister";
                  break;
  case "subject" :$querystring .= "order by subject";
                  break;

  default        :$querystring .= "order by sum desc";
}               




//  echo "<hr>" . $querystring . "<hr>"; 

 $db->query($querystring);

printf("<table  class=listtable>\n");

/* table header */
printf("<th><a href=$PHP_SELF?fsort=number>Register</a></th>");
printf("<th><a href=$PHP_SELF?fsort=subject>Bezeichnung</a></th>");
printf("<th><a href=$PHP_SELF?fsort=expend>Kostenblatt</a></th>");

while($db->next_record()) {
   printf("<tr>");
	printf("<td><a href=\"pfileedit.php?pnumber=%s&detail=2#detail\">%s (%s)</a></td>",
		 $db->record["pnumber"], $db->record["processregister"], 
		 $db->record["pnumber"]);
	printf("<td>%s</td>", $db->record["subject"]);
	printf("<td align=right>%s</td>", tolocalnum(sprintf('%.2f',$db->record["sum"]),$LOCALE));
	
	printf("</tr>\n");
}

printf("</table>\n"); 

printf("</table></form></center>");
$db->close();

/* end of page */
echo "<hr></TD></TR></TABLE></BODY></HTML>";

?>
