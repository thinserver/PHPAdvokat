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
echo "<TITLE>PHPAdvocat - Offene Rechnungen</TITLE>";
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
      array('&nbsp;&nbsp;<b>offene Rechnungen</b>'), 4);
  $phpa_menue->draw_menue();


echo "</TD><TD>\n";

/* display title */
echo "<CENTER><H1>Offene Rechnungen</H1></CENTER>\n";


echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT>".$changecheck ."</TD>";
print "</tr></table>\n";

print "<hr><center>";

$querystring = 
  "select i.number as inumber, ".
  "p.processregister as processregister, " .
  "p.subject as subject, " .
  "i.createdate as createdate, ".
  "sum(ip.amount)+sum(ip.vat) as sum ".
  "from phpa_pfiles p, phpa_invoices i, phpa_invoicepos ip " .
  "where p.number=i.pfile and ip.invoice=i.number ".
  "and i.paydate is null ".
  "group by i.number, p.processregister, p.subject, i.createdate ";
  

/* Sort by table header */
switch ($_GET["fsort"]) {

  case "number"  :$querystring .= "order by p.processregister";
                  break;
  case "subject" :$querystring .= "order by p.subject";
                  break;
  case "cdate"   :$querystring .= "order by i.createdate";
                  break;

  default        :$querystring .= "order by sum desc";
}               




 // echo "<hr>" . $querystring . "<hr>"; 

 $db->query($querystring);

printf("<table  class=listtable>\n");

/* table header */
printf("<th><a href=$PHP_SELF?fsort=number>Akte</a></th>");
printf("<th><a href=$PHP_SELF?fsort=subject>Bezeichnung</a></th>");
printf("<th><a href=$PHP_SELF?fsort=cdate>Datum</a></th>");
printf("<th><a href=$PHP_SELF?fsort=amount>Betrag</a></th>");

while($db->next_record()) {
   printf("<tr>");
	printf("<td><a href=\"invoiceedit.php?number=%s\">%s (%s)</a></td>",
		 $db->record["inumber"], $db->record["processregister"], 
		 $db->record["inumber"]);
	printf("<td>%s</td>", $db->record["subject"]);
	printf("<td>%s</td>", tolocaldate($db->record["createdate"], $LOCALE));
	printf("<td align=right>%.2f</td>", $db->record["sum"]);
	
	printf("</tr>\n");
}

printf("</table>\n"); 

printf("</table></form></center>");
$db->close();

/* end of page */
echo "<hr></TD></TR></TABLE></BODY></HTML>";

?>
