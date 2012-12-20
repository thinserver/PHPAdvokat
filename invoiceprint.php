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
} elseif($_GET["number"] !=0) {
  $number = $_GET["number"];
}



/* get pfile number */
$querystring=sprintf("select pfile from phpa_invoices where number=%s", $number);
if((!$db->query($querystring)) && $db->next_record()) { 
  $pnumber=$db->record["pfile"];
}  

/* get partner number */
$querystring=sprintf("select * from phpa_pfiles where number=%s", $pnumber);
if((!$db->query($querystring)) && $db->next_record()) { 
  $partner=$db->record["partner"];
  $processregister=$db->record["processregister"];
}  

/* get address of partner */
$querystring=sprintf("select * from phpa_partner where number=%s", $partner);
if((!$db->query($querystring)) && $db->next_record()) { 
  $partnertype=$db->record["type"];
  $partnertitle=$db->record["title"];
  $partnername=$db->record["name"];
  $partnerprename=$db->record["prename"];
  $partnerorganization=$db->record["organization"];
  $partnerstreet=$db->record["street"];
  $partnerzip=$db->record["zip"];
  $partnercity=$db->record["city"];
}


$querystring=sprintf("select * from phpa_config where number=1");
if((!$db->query($querystring)) && $db->next_record()) { 
  $configtitle=$db->record["title"];
  $configname=$db->record["name"];
  $configprename=$db->record["prename"];
  $configorganization=$db->record["organization"];
  $configstreet=$db->record["street"];
  $configzip=$db->record["zip"];
  $configcity=$db->record["city"];
}


/* set letter phrases */
$subject = "Betreff: Rechnung zu Akte ".$processregister;
$closing = "Mit freundlichen Gr&uuml;&szlig;en <br><br>".
           $configname;


  /* Display Invoice Header */

  printf("<html><header><title>Rechnung zu Akte %s</title></header>", 
         $processregister);

  printf("<table width=100%%><tr><td align=left>%s<br>%s</td>".
    "<td align=right>%s<br>%s, %s</td></tr></table><hr>\n",
    $configorganization, $configname, 
    $configstreet, $configzip, $configcity);

  
  printf("<br><br><br>");
  printf("<font size=-4>%s, %s, %s, %s %s</font>\n",
    $configorganization, $configname, 
    $configstreet, $configzip, $configcity);
  printf("<table>");
  printf("<tr><td>%s</td></tr>", $partnertitle);
  printf("<tr><td>%s %s</td></tr>", $partnerprename, $partnername);
  printf("<tr><td>%s</td></tr>", $partnerorganization);
  printf("<tr><td>%s</td></tr>", $partnerstreet);
  printf("<tr><td></td></tr>");
  printf("<tr><td><b>%s %s</b></td></tr>", $partnerzip, $partnercity);
  printf("</table>");
  
  printf("<br><br><br>");
  printf($subject);
  printf("<br><br><br>");


 /* Display Invoice Positions */
    $querystring = sprintf("select ip.number, it.description, ".
      "ip.chargefactor, ip.amount ".
      "from phpa_invoicetypes it, phpa_invoicepos ip ".
      "where ip.invoice=%s and ip.invoicetype=it.number",
       $number);
    /* echo "<hr>" . $querystring . "<hr>"; */
    $db->query($querystring);

    printf("<table width=100%% border=0>\n");
           
    /* Display all invoice positions */
    $invoicesum = 0.0;
    $taxrate = 0.16;
    $posnum =1;

    while($db->next_record()) {
            printf("<tr><td>%s</td>", $posnum++);
            printf("<td>%s</td>", $db->record["description"]);
            printf("<td>");
            if($db->record["chargefactor"] != 0)
              printf("%.2f", $db->record["chargefactor"]);
            printf("</td>");
            printf("<td>%s</td>", $db->record["amount"]);
            $invoicesum += (float) $db->record["amount"];
    }



    /* Display invoice sum */
    printf("<tr><td colspan=3>Gesamt</td><td>%.2f</td></tr>",
            $invoicesum);
    $invoicesum += $invoicesum * $taxrate;
    printf("<tr><td colspan=3><b>Gesamt incl. %s%% MwSt.</b></td>".
           "<td><b>%.2f</b></td></tr>",$taxrate*100, $invoicesum);

    printf("</table></center>\n");
  
    printf("<br><br><br>");
    printf($closing);



   echo "</BODY></HTML>\n"; /* end of page */
?>
