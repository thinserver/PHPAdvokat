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

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);

/* import invoice number if transmitted by GET or POST */
if($_POST["number"] !=0) {
  $number = $_POST["number"];
  $pfile = $_POST["pfile"];
  $knopf = $_POST["knopf"];
  $filename = $_POST["filename"];
  $filecontent = $_POST["filecontent"];
} elseif($_GET["number"] !=0) {
  $number = $_GET["number"];
  $pfile = $_GET["pfile"];
  $path = $_GET["path"];
}



  echo "<HTML><HEAD><TITLE>PHPAdvocat - Korrespondenz</TITLE>";
  echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\"></HEAD>\n";
  echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

  echo "<TABLE width=100%><TR><TD width=200 valign=top>";


  /* here comes the menue */
  $phpa_menue->account=$user;
  $phpa_menue->selected = 2;
   array_insert($phpa_menue->contents,
      array( sprintf("&nbsp;&nbsp;<b><a href=pfileedit.php?pnumber=%s&detail=4>".
                     "Akte bearbeiten</a></b>",$pfile)), 1);
   array_insert($phpa_menue->contents,
      array('&nbsp;&nbsp;&nbsp;&nbsp;<b>Schriftverkehr</b>'), 2);

  $phpa_menue->draw_menue();


/* display Title */
echo "</TD><TD><CENTER><H1>Korrespondenz</H1></CENTER>";

/* if nothing is in config table ./files is default */        
$filebase = 'files';
$querystring = sprintf("select * from config where number=%s",1);
if(!$db->query($querystring) && $db->next_record())
  $filebase = $db->record["filebase"];


/* Test in die Datei schreiben */
if($filename && $knopf=="Speichern") {
     $fullpath = $filebase .'/'. $filename;
     $fd=fopen($fullpath, "w");
     fwrite($fd, stripslashes($filecontent));
     fclose($fd);
}

/* Datei in den Editor laden */
if($path != '') $fullpath = $filebase .'/'. $path;

if(file_exists($fullpath)){
     $fd=fopen($fullpath, "r");
     $filecontent=fread($fd, filesize($fullpath));
     fclose($fd);
     $filename = $path;
} elseif(file_exists($filebase .'/'. $filename) && ($knopf=="Laden" || $knopf=="Speichern")){
     $fullpath = $filebase .'/'. $filename;
          echo "<hr>". $fullpath ."<hr>"; $filename;
     $fd=fopen($fullpath, "r");
     $filecontent=fread($fd, filesize($fullpath));
     fclose($fd);
}

$filecontent=stripslashes($filecontent);

echo "<table>";
/* Dateiname festlegen */
echo "<form method=post action=$PHP_SELF>";
echo "<tr><td>";

echo "<select name=filename>\n";

$handle=opendir ($filebase);
while (false !== ($file = readdir ($handle))) {
   if(($file) == $filename){
      printf("<option selected>%s\n", $file);
   } else {
      printf("<option>%s\n", $file);
   }
}
closedir($handle);
echo "</select>\n";


// echo "<input type=hidden name=filecontent value=\"". addslashes($filecontent) . "\">";
echo "<input type=hidden name=pfile value=\"". $pfile . "\">";
echo "<input type=hidden name=number value=\"". $number . "\">";
echo "<input type=submit name=knopf value=Laden>";
echo "<input type=submit name=knopf value=Speichern>";
echo "</td></tr>\n<tr><td>";

// $filecontent=stripslashes($filecontent);

/* Dateiname festlegen */
printf("<TEXTAREA NAME=filecontent WRAP=VIRTUAL " .
        "COLS=60 ROWS=10>%s</TEXTAREA>\n", $filecontent);
echo "</form>";
echo "</td></tr></table>\n";


$db->close();


/* End HTML PAGE */
echo "</BODY></HTML>";
?>