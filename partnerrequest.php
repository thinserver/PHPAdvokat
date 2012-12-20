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

/* database connection for drop down list */
$dblist = new www_db;
$dblist->connect($user, $passwd);


/*++++++++++++ handle changed data from GET or POST +++++++++*/

/* add new record from last line form */
if($_POST["partneraddbutton"])
{
  $name = $_POST["name"];
  $prename = $_POST["prename"];
  $organization=$_POST["organization"];
  $type=$_POST["type"];
  $title=$_POST["title"];
  $querystring = sprintf("insert into phpa_partner (type, title, name, prename, organization) ".
    "values ('%s', '%s', '%s', '%s', '%s')", $type, $title, $name, $prename, $organization);
  // echo "<hr>". $querystring . "<hr>";
  if (!$db->query($querystring)) {
      $changecheck="Neue Adresse";
  }

}

/* delete record from clicked link in table*/
if($_GET["partnerdel"])
{
   $number=$_GET["number"];
   /* since all usable databases are able to keep the referntial*/
   /* integrity, there is no need to delete the details */
	$querystring = sprintf("delete from phpa_partner where number=%s", 
	  $number);
    // echo "<hr>". $querystring . "<hr>";
  if (!$db->query($querystring)) {
      $changecheck="Adresse gel&ouml;scht";
  }
}

/*++++++++++++ end of Data handling +++++++++++++++++++++++++*/


/* begin html page */
echo "<HTML><HEAD><TITLE>PHPAdvocat - Liste Akten</TITLE>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";
echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

echo "<TABLE width=100%><TR><TD width=200 valign=top>";

  /* here comes the menue */
  $phpa_menue->account=$user;
  $phpa_menue->selected=1;
  $phpa_menue->draw_menue();


echo "</TD><TD><CENTER><H1>Adressen</H1></CENTER>";

/* begin table framework */
echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT>".$changecheck."</TD>";
print "</tr></table>\n";

print "<hr><center>";

$querystring = 
  "select * from phpa_partner ";


/* Sort by table header */
switch ($_GET["fsort"]) {

  case "number"  :$querystring .= "order by number";
                  break;
  case "name"    :$querystring .= "order by name";
                  break;
  case "type"    :$querystring .= "order by type, name";
                  break;
  case "title"    :$querystring .= "order by title, name";
                  break;
  case "prename" :$querystring .= "order by prename";
                  break;
  case "organization" :$querystring .= "order by organization";
                  break;

  default        :$querystring .= "order by name";
}               




 /* echo "<hr>" . $querystring . "<hr>"; */ 

 $db->query($querystring);

printf("<table class=listtable><tbody class=listtable>\n");

/* table header */
echo "<th><a href=$PHP_SELF?fsort=number>Nummer</a></th>";
echo "<th><a href=$PHP_SELF?fsort=type>Typ</a></th>";
echo "<th><a href=$PHP_SELF?fsort=title>Titel</a></th>";
echo "<th><a href=$PHP_SELF?fsort=name>Name</a></th>";
echo "<th><a href=$PHP_SELF?fsort=prename>Vorname</a></th>";
echo "<th><a href=$PHP_SELF?fsort=organization>Organisation</a></th>";
echo "<th></th>";


while($db->next_record()) {
	printf("<tr>");
	/* printf("<td>%s</td>", $db->row); */
	printf("<td><a href=\"partneredit.php?number=%s\">%05.0f</a></td>",
		 $db->record["number"], $db->record["number"]);
	printf("<td>%s</td>", $db->record["type"]);
	printf("<td>%s</td>", $db->record["title"]);
	printf("<td>%s</td>", $db->record["name"]);
	printf("<td>%s</td>", $db->record["prename"]);
	printf("<td>%s</td>", $db->record["organization"]);
   /* delete this row */
   printf("<td><a href=\"$PHP_SELF?number=%s&partnerdel=1\" " .
         "onClick=\"return confirm('Eintrag loeschen?')\">" .
         "<img alt=Del src=\"images/trash-x.png\" border=0>".
         "</a></td></tr>\n", $db->record["number"]);

	printf("</tr>\n");
}

/* last row is an input for new partner */
printf("<tr class=input><FORM METHOD=POST ACTION=\"$PHP_SELF\">");
printf("<td>Neu</td>");

/* get a list of types from database */
$querystring="select * from phpa_partnertypes";
$dblist->query($querystring);

printf("<td><select name=type>\n");
 while($dblist->next_record()) {
   printf("<option>%s\n", $dblist->record["type"]); 
 }
printf("</select></td>\n");

printf("<td><select name=title>\n");
  printf("<option value ''></option>");
  printf("<option>Herr</option>");
  printf("<option>Frau</option>");
printf("</select></td>\n");

printf("<td><input name=name type=text size=20></td>\n");
printf("<td><input name=prename type=text size=20></td>\n");
printf("<td><input name=organization type=text size=20></td>\n");
printf("<td><input name=partneraddbutton type=submit value=Neu></td>\n");
printf("</FORM></tr>");
/* end of input row */

/* end of detail table */
printf("</tbody></table><hr>\n"); 

$db->close();

/* end of table framework */
echo "</TD></TR></TABLE>";

/* end of HTML page */
echo "</BODY></HTML>\n"

?>
