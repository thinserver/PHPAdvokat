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
require("./include/dialog.php");

/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

$changecheck="";

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);


/********** handle updates *** begin ********************************/
if($_POST["admineditbutton"]) {
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
  $bank=$_POST["bank"];
  $bank_id=$_POST["bank_id"];
  $account=$_POST["account"];
  $vat_id=$_POST["vat_id"];
  $vat_percent=toisonum($_POST["vat_percent"],$LOCALE);
  $filebase=$_POST["filebase"];
  $language=$_POST["language"];

  /* this could be used for multiple clients */
  $number=1;
  
   $querystring = sprintf("update phpa_config set " .
      "title='%s', ".
      "name='%s', ".
      "prename='%s', ".
      "organization='%s', ".
      "street='%s', ".
      "zip='%s', ".
      "city='%s', ".
      "phone='%s', ".
      "fax='%s', ".
      "email='%s', ".
      "bank='%s', ".
      "bank_id='%s', ".
      "account='%s', ".
      "vat_id='%s', ".
      "vat_percent=%s, ".
      "filebase='%s', ".
      "language='%s' ".
      "where number=%s",
       $title,
       $name, 
       $prename, 
       $organization,
       $street,
       $zip,
       $city,
       $phone,
       $fax,
       $email,
       $bank,
       $bank_id,
       $account,
       $vat_id,
       nullcorr($vat_percent),
       $filebase,
       $language,
       $number);
      //echo "<hr>".$querystring ."<hr>";
   if (!$db->query($querystring)) {
        $changecheck="Eintrag ge&auml;ndert";
   }
}
/********** handle updates *** end *********************************/





echo "<HTML><HEAD><TITLE>PHPAdvocat - Administration</TITLE>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";

echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

/* Begin framework: table with two colunms, */
/* menu on left with suze 200, rest on right */
echo "<TABLE width=100%><TR><TD width=200 valign=top>\n";

  $phpa_menue->account=$_SESSION["dbuser"];
  $phpa_menue->selected = 5;
  $phpa_menue->draw_menue();

echo "</TD><TD>\n"; /* end menue, start dialog */

/* display title */
echo "<CENTER><H1>Administration</H1></CENTER>\n";

/* display headline */
echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT>". $changecheck ."</TD>";
echo "</tr></table>\n";

print "<hr><center>";

echo "<table width=100% border=0>";
echo "<tr><td valign=top><center>";


$querystring = 
  sprintf("select * from phpa_config");
$db->query($querystring);
$db->next_record();

/* database connection for drop down list */
$dblist = new www_db;
$dblist->connect($user, $passwd);

 /* beginning frame of dialog */
$admindialog = new htmldialog;
 
/* only show client-nr, hidden detail nr */
$admindialog->addinput("Nummer:", 
   sprintf("<input name=number type=hidden value=\"%s\">%s".
           "<input name=detail type=hidden value=\"%s\">",
           $db->record["number"], $db->record["number"],$detail));

/* display title */
$admindialog->addinput("Anrede/Titel:", 
   sprintf("<input name=title type=text size=10 value=\"%s\">\n",
        $db->record["title"]));

/* display processregister */
$admindialog->addinput("Name, Vorname:", 
   sprintf("<input name=name type=text size=30 value=\"%s\">\n".
           "<input name=prename type=text size=30 value=\"%s\">\n",
        $db->record["name"],$db->record["prename"]));

/* display organization */
$admindialog->addinput("Organisation:", 
   sprintf("<input name=organization type=text size=50 value=\"%s\">\n",
        $db->record["organization"]));

/* display street */
$admindialog->addinput("Stra&szlig;e/Nr.:", 
   sprintf("<input name=street type=text size=50 value=\"%s\">\n",
        $db->record["street"]));

/* display zip/city */
$admindialog->addinput("PLZ/Stadt:", 
   sprintf("<input name=zip type=text size=6 value=\"%s\">\n".
           "<input name=city type=text size=30 value=\"%s\">\n",
        $db->record["zip"],$db->record["city"]));

/* display phone/fax */
$admindialog->addinput("Telefon/Fax:", 
   sprintf("<input name=phone type=text size=20 value=\"%s\">\n".
           "<input name=fax type=text size=20 value=\"%s\">\n",
        $db->record["phone"],$db->record["fax"]));

/* display email */
$admindialog->addinput("Email:", 
   sprintf("<input name=email type=text size=30 value=\"%s\">\n",
        $db->record["email"]));

/* display Bank */
$admindialog->addinput("Bank:", 
   sprintf("<input name=bank type=text size=80 value=\"%s\">\n",
        $db->record["bank"]));

/* display Bank-ID */
$admindialog->addinput("BLZ:", 
   sprintf("<input name=bank_id type=text size=15 value=\"%s\">\n",
        $db->record["bank_id"]));

/* display account */
$admindialog->addinput("Konto:", 
   sprintf("<input name=account type=text size=20 value=\"%s\">\n",
        $db->record["account"]));

/* display VAT-ID */
$admindialog->addinput("Umsatzsteuer-Nr.:", 
   sprintf("<input name=vat_id type=text size=40 value=\"%s\">\n",
        $db->record["vat_id"]));

/* display VAT-ID */
$admindialog->addinput("Mehrwertsteuer-Satz:", 
   sprintf("<input name=vat_percent type=text size=10 value=\"%s\">%%\n",
        tolocalnum($db->record["vat_percent"], $LOCALE)));

/* display directory for files */
$admindialog->addinput("Verzeichnis f&uuml;r Dateien:", 
   sprintf("<input name=filebase type=text size=60 value=\"%s\">\n",
        $db->record["filebase"]));

/* display language no longer needed */
$admindialog->addinput("Sprache:", 
   sprintf("<input name=language type=text size=10 disabled value=\"%s\">\n", $LOCALE));
//   sprintf("<input name=language type=hidden value=\"%s\">\n",
//        $db->record["language"]));


/* button for saving data */
$admindialog->addinput("<input name=admineditbutton type=submit value=Sichern>","");

$admindialog->out(); /* End of display framework */


echo "</TD></TR></TABLE>\n"; /* end of table framework */

echo "</BODY></HTML>\n"; /* end of page */
?>
