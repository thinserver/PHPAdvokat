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
echo "<TITLE>PHPAdvocat - Liste Umsatzsteuer</TITLE>";
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
      array('&nbsp;&nbsp;<b>Umsatzsteuer</b>'), 4);
  $phpa_menue->draw_menue();
  
echo "<hr><a href=bookkeepingrequest.php>&Uuml;bersicht Konten</a>";
echo "</TD><TD>\n";

/* display title */
echo "<CENTER><H1>Liste Kategorien</H1></CENTER>\n";


echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT>".$changecheck ."</TD>";
print "</tr></table>\n";

print "<hr><center>";

$querystring = 
  "select c.number, c.description, ".
    "(select sum(a.incomingamount)-".
    "sum(a.outgoingamount) ".
    "from phpa_amounts as a ".
    "where a.exp_category = c.number) as sum ".
  "from phpa_exp_categories as c ".
  "where c.number >= 3800 and c.number < 3900 ";

/* Sort by table header */
switch ($_GET["fsort"]) {

  case "number"  :$querystring .= "order by c.number";
                  break;
  case "description"    :$querystring .= "order by c.description";
                  break;

  default        :$querystring .= "order by c.number";
}               

//  echo "<hr>" . $querystring . "<hr>"; 

$db->query($querystring);

printf("<table class=listtable>\n");

/* table header */
printf("<th><a href=$PHP_SELF?fsort=number>Nummer</a></th>");
printf("<th><a href=$PHP_SELF?fsort=description>Beschreibung</a></th>");
printf("<th><a href=$PHP_SELF?fsort=value>Wert</a></th>");


while($db->next_record()) {
   if("" <> $db->record["sum"]){ /* dont display row if sum is empty */ 
      if(0 <= $db->record["sum"] ) {
        printf("<tr>");
      } else {
        /* make row red if account is negative */
        printf("<tr bgcolor=red>");
      }
   	/* printf("<td>%s</td>", $db->row); */
   	printf("<td><a href=\"categoryedit.php?number=%s\">%04.0f</a></td>",
   		 $db->record["number"], $db->record["number"]);
   	printf("<td>%s</td>", $db->record["description"]);
   	printf("<td align=right>%s</td>", tolocalnum($db->record["sum"],$LOCALE));
   	
   	printf("</tr>\n");
	} /* "" == $db->record["sum"] */
}



printf("</table>\n"); 

printf("</table></form></center>");



$db->close();

/* end of page */
echo "<hr></TD></TR></TABLE></BODY></HTML>";

?>
