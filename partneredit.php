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
require("./include/dialog.php");

/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

$changecheck="";

/* import pnumber if transmitted by GET */
if($_POST["number"] != 0) {
  $number = $_POST["number"];
} elseif($_GET["number"] != 0) {
  $number = $_GET["number"];
} else {
  $number=0;
}

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);

/* if partner number is 0 the create a new record with empty fields */
if($number==0) {
   $querystring = "insert into phpa_partner (type, title) ".
    "values ('Person', 'Frau')";
   // echo "<hr>". $querystring . "<hr>";
   if (!$db->query($querystring)) {
      $changecheck="Neue Adresse";
   }
   /* evaluate new number */
   $querystring = "select max(number) as maxnum from phpa_partner";
   if (!$db->query($querystring) && $db->next_record()) {
      $number = $db->record["maxnum"];
      /* if file is called due to file creation, use another submit button */
      $next2createfile=1;
   }
} /* end creation of new record */

/********** handle updates and inserts begin ********************************/
if($_POST["partnereditbutton"]) {
  $type=$_POST["type"];
  $title=$_POST["title"];
  $name=$_POST["name"];
  $prename=$_POST["prename"];
  $organization=$_POST["organization"];
  $number=$_POST["number"];
  $street=$_POST["street"];
  $zip=$_POST["zip"];
  $city=$_POST["city"];
  $phone=$_POST["phone"];
  $fax=$_POST["fax"];
  $email=$_POST["email"];

   $querystring = sprintf("update phpa_partner set " .
      "title='%s', ".
      "type='%s', ".
      "name='%s', ".
      "prename='%s', ".
      "organization='%s', ".
      "street='%s', ".
      "zip='%s', ".
      "city='%s', ".
      "phone='%s', ".
      "fax='%s', ".
      "email='%s' ".
      "where number=%s",
       $title,
       $type,
       $name, 
       $prename, 
       $organization,
       $street,
       $zip,
       $city,
       $phone,
       $fax,
       $email,
       $number);

   if (!$db->query($querystring)) {
        $changecheck="Eintrag ge&auml;ndert";
   }
}
/********** handle updates and inserts end *********************************/



?>
<HTML>
<script language="JavaScript">
<!--
        function heute()
        {
                jetzt = new Date();
                var tag = jetzt.getDate();
                var monat = jetzt.getMonth();
                var jahr = jetzt.getYear();
                if(jahr < 1000) jahr+=1900;
                monat+=1;
                var datum = tag + "." + monat + "." + jahr;
                return datum;
        }
//-->
</script>
<?php
 /* begin HTML page */
echo "<HEAD><TITLE>PHPAdvocat - Adressen</TITLE>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";
echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

/* begin table framework */
echo "<TABLE width=100%><TR><TD width=200 valign=top>\n";

  /* here comes the menue */
  $phpa_menue->account=$user;
  $phpa_menue->selected = 2;
   array_insert($phpa_menue->contents,
      array('&nbsp;&nbsp;&nbsp;&nbsp;<b>Adresse &auml;ndern</b>'), 2);

  $phpa_menue->draw_menue();

echo "</TD><TD>";

/* display Title */
echo "<CENTER><H1>Bearbeitung Adresse</H1></CENTER>";


$querystring = 
  sprintf("select * from phpa_partner where number=%s", $number);
$db->query($querystring);
$db->next_record();

/* database connection for drop down list */
$dblist = new www_db;
$dblist->connect($user, $passwd);


echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT>". $changecheck ."</A></TD>";
print "</tr></table>\n";

print "<hr><center>";


 /* beginning frame of dialog */
$partnerdialog = new htmldialog;


if(1==$next2createfile) { /* I'm coming to create a new pfile */
   $partnerdialog->stringbegin = 
      "<center><table width=90%% border=1><tr><td>\n".
      "<FORM METHOD=POST ACTION=filesrequest.php><table>\n";
}


/* only show file-nr, hidden detail nr */

$partnerdialog->addinput("Partnernummer:", 
   sprintf("<input name=number type=hidden value=\"%s\">%s".
           "<input name=detail type=hidden value=\"%s\">",
           $db->record["number"], $db->record["number"],$detail));


/* select type */
/* get a list of types from database */
$querystring="select * from phpa_partnertypes";
$dblist->query($querystring);
$optionlist ="<select name=type>\n";
 while($dblist->next_record()) {
    if($dblist->record["type"] == $db->record["type"])
       $optionlist .= sprintf("<option selected>%s\n", $dblist->record["type"]); 
    else
       $optionlist .= sprintf("<option>%s\n", $dblist->record["type"]); 
 }
$optionlist .="</select>\n";
$partnerdialog->addinput("Partnertyp:",$optionlist);



/* input title */
$partnerdialog->addinput("Titel:", 
   sprintf("<input name=title type=text size=10 value=\"%s\">",
           $db->record["title"]));



/* input prename */
$partnerdialog->addinput("Vorname:", 
   sprintf("<input name=prename type=text size=20 value=\"%s\">",
           $db->record["prename"]));

/* input name */
$partnerdialog->addinput("Name:", 
   sprintf("<input name=name type=text size=20 value=\"%s\">",
           $db->record["name"]));

/* input organization */
$partnerdialog->addinput("Organisation:", 
   sprintf("<input name=organization type=text size=20 value=\"%s\">",
           $db->record["organization"]));


/* input street */
$partnerdialog->addinput("Strasse/Nr.:", 
   sprintf("<input name=street type=text size=20 value=\"%s\">",
           $db->record["street"]));

/* input zip code and city */
$partnerdialog->addinput("PLZ/Ort:", 
   sprintf("<input name=zip type=text size=7 value=\"%s\">\n",
        $db->record["zip"]) .
   sprintf("<input name=city type=text size=20 value=\"%s\">\n",
        $db->record["city"]));

/* input phone number */
$partnerdialog->addinput("Telefon:", 
   sprintf("<input name=phone type=text size=20 value=\"%s\">\n",
        $db->record["phone"]));

/* input fax number */
$partnerdialog->addinput("Fax:", 
   sprintf("<input name=fax type=text size=20 value=\"%s\">\n",
        $db->record["fax"]));

/* input email address */
$partnerdialog->addinput("Email:", 
   sprintf("<input name=email type=text size=20 value=\"%s\">\n",
        $db->record["email"]));

/* display butten depending of form before */
if(1==$next2createfile) { /* I'm coming to create a new pfile */
   $partnerdialog->addinput(
     "<input name=partnereditbutton type=submit value=\"Zur Akte\">\n","");
} else { /* just save the record */
   $partnerdialog->addinput(
     "<input name=partnereditbutton type=submit value=\"Sichern\">\n","");
}

$partnerdialog->out(); /* End of display framework */

printf("</table></form></center><hr>");

$db->close();

/* end table framework */
echo "</TD></TR></TABLE>";

/* end HTML page */
echo "</BODY></HTML>";

?>
