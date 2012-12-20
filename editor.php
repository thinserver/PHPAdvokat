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
if($_POST["pfile"]) {
  $pfile = $_POST["pfile"];
  $savebutton = $_POST["savebutton"];
  $pdfbutton = $_POST["pdfbutton"];
  $filename = $_POST["filename"];
  $filecontent = $_POST["filecontent"];
} elseif($_GET["pfile"]) {
  $pfile = $_GET["pfile"];
  $filename = $_GET["filename"];
}

  $changecheck='';

/* check file name to prevent unauthorized acces on system files */
  $filebase = './files';
  $querystring = sprintf("select * from phpa_config where number=%s",1);
  if(!$db->query($querystring) && $db->next_record() && $db->record["filebase"] != '') {
     $filebase = trim($db->record["filebase"]);
     $filebaselen = strlen($filebase);
  }

/*
* work with files only if name begins with filebase and there  
* are no '..' in the path 
*/
if((0 == strncmp($filename, $filebase, $filebaselen)) && (!strstr($filename, '..'))) {
/* filename is OK */

   /* Test in die Datei schreiben */
   if($filename && $savebutton) {
        if($fd=fopen($filename, "w")) {
          /* we strip the DOS-like ^M */
          $filecontent = str_ireplace(chr(13), '', $filecontent);
   
          fwrite($fd, stripslashes($filecontent));
          fclose($fd);
          $changecheck='Datei gesichert';
        }
   }
   
   /* Datei in den Editor laden */
   if(file_exists($filename)){
        if($fd=fopen($filename, "r")) {
          $filecontent=fread($fd, filesize($filename));
          fclose($fd);
          // $filecontent=stripslashes($filecontent);
        } else {
               $changecheck='Datei geschuetzt';
        }
   
   }
} /* if filename is OK */

  echo "<HTML><HEAD><TITLE>PHPAdvocat - Schriftverkehr</TITLE>";
  echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";

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
echo "</TD><TD><CENTER><H1>Schriftverkehr</H1></CENTER>";

echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
/* display status at right side */
echo "<TD ALIGN=RIGHT><b>". $changecheck. "</b></A></TD>";
print "</tr></table>\n";
echo "<hr>";


echo "<table  class=inputtable>";
/* Dateiname festlegen */
echo "<form method=post action=$PHP_SELF>";
echo "<tr><td>";


// echo "<input type=hidden name=filecontent value=\"". addslashes($filecontent) . "\">";
echo "<input type=hidden name=pfile value=\"". $pfile . "\">";
echo "<input type=hidden name=filename value=\"". $filename . "\">" .basename($filename);
echo "</td></tr>\n";

// $filecontent=stripslashes($filecontent);

echo "<tr><td>\n";

/* Dateiname festlegen */
printf("<TEXTAREA NAME=filecontent WRAP=VIRTUAL " .
        "COLS=80 ROWS=25>%s</TEXTAREA>\n", $filecontent);
echo "</td></tr>\n";

echo "<tr><td>\n";
echo "<table width=100%><tr>";
echo "<td align=left><input type=submit name=savebutton value=Speichern></td>\n";
echo "<td align=right><a href=\"letterpdf.php?latexfile=".$filename .
     "\" target=_BLANK>PDF-Ausgabe</a></td>\n";
echo "</tr></table>\n";
echo "</td></tr>\n";

echo "</form>";
echo "</table>\n";


$db->close();


/* End HTML PAGE */
echo "</td></tr></table></BODY></HTML>";
?>