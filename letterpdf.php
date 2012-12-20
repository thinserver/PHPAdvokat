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
// require("./fpdf/fpdf.php");

/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

$changecheck="";

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);
if($_POST["latexfile"]) {
  $latexfile = $_POST["latexfile"];
} elseif($_GET["latexfile"]) {
  $latexfile = $_GET["latexfile"];
}


/* generate pdf file with latex system */
if(file_exists($latexfile) ) {
   $fd=fopen($latexfile, "r");
   $filecontent=fread($fd, filesize($latexfile));
   fclose($fd);

   $tempfilename = tempnam('', 'phpa-');
    // echo "<hr>".$tempfilename."<hr>";
    if($fd=fopen($tempfilename, "w")) {
       fwrite($fd, $filecontent);
       fclose($fd);
       if($OSTYPE == 'UNIX')
         system('./generatepdf.sh '.basename($tempfilename).' '.dirname($tempfilename).'>/dev/null');
       if($OSTYPE == 'WINDOWS')
         system('./generatepdf.sh '.basename($tempfilename).' '.dirname($tempfilename).'>/dev/null');
       // printf('<hr> ./generatepdf.sh '.basename($tempfilename).' '.dirname($tempfilename).'<hr>');
       if(file_exists($tempfilename.'.pdf')){
          if($fd=fopen($tempfilename.'.pdf', "r")) {
           $pdfcontent=fread($fd, filesize($tempfilename.'.pdf'));
           fclose($fd);
           $changecheck='PDF-Datei generiert';

				//We send to a browser
				header('Pragma: public');
				header('Content-Type: application/pdf');
				header('Content-Length: '.strlen($pdfcontent));
				// header('Content-disposition: inline; filename="'.$tmpfilename.'.pdf"');
				header('Content-disposition: inline; filename="letter.pdf"');
   			echo $pdfcontent;
            system('/bin/rm '.$tempfilename.'*');
   			exit;

          } /* if open tempfilename.pdf */
       } else { /* PDF file isn't there */
           $changecheck='LaTeX Quelle fehlerhaft';
           $fd=fopen($tempfilename.'.log', "r");
           $logcontent=fread($fd, filesize($tempfilename.'.log'));
           fclose($fd);
           system('/bin/rm '.$tempfilename.'*');
       } /* if exists tempfilename.pdf */
       
    } else { /* Sourcefile not available */
       $changecheck='Temporaere Datei konnte nicht erzeugt werden!';
    } /* if open tempfilename.tex */
} else { /* latexfile don't exist */
     $changecheck='LaTeX Quelle ('.$latexfile.') fehlt';
  
} /* endif file exists latexfile */

  echo "<HEAD><TITLE>PHPAdvocat - PDF-Ausgabe</TITLE>";
  echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\"></HEAD>\n";
  echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

  echo "<TABLE width=100%><TR><TD>";
  /* display Title */
  echo "<CENTER><H1>Bearbeitung Rechnung</H1></CENTER><hr>\n";
  echo "Die PDF-Erzeugung schlug fehl! Grund:<br>".$changecheck."<hr><b>Log:</b><br>";
  echo str_ireplace(chr(10), '<br>', $logcontent);
  /* End HTML PAGE */
  echo "</td></tr></table></BODY></HTML>";

?>