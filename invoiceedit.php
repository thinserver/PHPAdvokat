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

/* default account for amounts */
$default_account=1;


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


/********** handle updates and inserts begin ********************************/

/****************** begin change data of invoice *************************/
if($_POST["invoiceeditbutton"]) {
      /* import POST-VARS */
      $number = $_POST["number"];
      $createdate = toisodate($_POST["createdate"],$LOCALE);
      $paydate = toisodate($_POST["paydate"],$LOCALE);
      $pfile = $_POST["pfile"];
      $pfilevalue = toisonum($_POST["pfilevalue"],$LOCALE);
      $charge = toisonum($_POST["charge"],$LOCALE);
      $address = $_POST["address"];
      $invoicetext = $_POST["invoicetext"];
      
        $querystring = sprintf("update phpa_invoices set " .
                "createdate=%s, paydate=%s, pfile=%s, address=%s, " .
                "pfilevalue=%s, charge=%s, invoicetext='%s' " .
                " where number=%s",
                nullcorr($createdate), nullcorr($paydate), 
		$pfile, $address,
                nullcorr($pfilevalue), nullcorr($charge), $invoicetext, $number);
	  // echo "<hr>".$querystring ."<hr>";
        if (!$db->query($querystring)) {
                $changecheck="Eintrag ge&auml;ndert";
		/* change pnumber for later display */
		$pnumber=$pfile;
        }
}

/**** Payed Invoice: Set paydata and insert a recort in expenditures ***/

if($_POST["invoicepaybutton"]) {
      /* import POST-VARS */
      $number = $_POST["number"];
      $querystring = sprintf("select * from phpa_invoices where number=%s",$number);
      if (!$db->query($querystring) && $db->next_record()) {      
         $pfile = $db->record["pfile"];
         $paydate = $db->record["paydate"];
         $createdate = $db->record["createdate"];
      }
      
      if($paydate == '') { /* make update only if invoice is not payed*/     

         /* generate paydate from today */
         $paydate=date("Y-m-d", time());

         /* first af all mark the invoice as payed by setting the pay date */
         $querystring = sprintf("update phpa_invoices set " .
                   "paydate='%s' " .
                   "where number=%s",
                   $paydate, $number);
//	   echo "<hr>".$querystring ."<hr>";

           if (!$db->query($querystring)) { /* if update successful */

              /* to enter each category of vat separately open a new connection */
              $dbinvoicepos = new www_db;
              $dbinvoicepos->connect($user, $passwd);
              $querystring = 
                 sprintf("select amount, vat, description, ".
                  "amount_category, vat_category, vat_percent ".
                  "from phpa_invoicepos as p, phpa_invoicetypes as t ".
                  "where p.invoicetype=t.number ".
                  "and p.invoice=%s", $number);
                         
//     echo "<hr>".$querystring ."<hr>";
               $dbinvoicepos->query($querystring);
               
               /* walk through all invoice positions */
               while($dbinvoicepos->next_record()) {

                 $description =sprintf("Re. Nr. %s, %s", 
                   $number, $dbinvoicepos->record["description"]);

                 $amount_id = 'NULL';
                 /* then  create the amount without taxes */
                 $querystring = sprintf("insert into phpa_amounts ".
                   "(createdate, exp_account, exp_category, description, incomingamount) " .
                   "values(%s, %s, %s, '%s', %s) ",
                    nullcorr($paydate),
                    $default_account,
                    $dbinvoicepos->record["amount_category"], 
                    $description,
                   $dbinvoicepos->record["amount"]);

//      echo "<hr>".$querystring ."<hr>";
               $returnval = $db->query($querystring);
              /* get new amount id if successful */
              if(!$returnval) {
                $querystring = "select max(number) as amountid from phpa_amounts";
                if(!$db->query($querystring) &&  $db->next_record()) {
                  $amount_id = $db->record["amountid"];
                }
              } /* returnval */

      
              $vat_id = 'NULL';
              /* then create the vat-records only if vatrate > 0 */
              if($dbinvoicepos->record["vat"] > 0){

                 $querystring = sprintf("insert into phpa_amounts ".
                   "(createdate, exp_account, exp_category, description, incomingamount) " .
                   "values(%s, %s, %s, '%s', %s) ",
                    nullcorr($paydate),
                    $default_account,
                    $dbinvoicepos->record["vat_category"], 
                    $description,
                    $dbinvoicepos->record["vat"]);


//       echo "<hr>".$querystring ."<hr>";
               $returnval = $db->query($querystring);
               /* get new amount id if successful */
              if(!$returnval) {
                $querystring = "select max(number) as amountid from phpa_amounts";
                if(!$db->query($querystring) &&  $db->next_record()) {
                   $vat_id = $db->record["amountid"];
                }
              } /* returnval */
            } /* if sumvat > 0 */
            /* last create the expend row */
            $querystring = sprintf("insert into phpa_expenditures ".
             "(pfile, createdate, description, expendituretype, ".
             "amount, vatamount) " .
             "values(%s, %s, '%s', 3, %s, %s)", 
             $pfile, nullcorr($paydate),
             $description, $amount_id, $vat_id);
//     echo "<hr>".$querystring ."<hr>";
             if (!$db->query($querystring)) {
                 $changecheck="Rechnung bezahlt";
             }
            /* change pnumber for later display */
            $pnumber=$pfile;
          } /* end while dbinvoicepos->next_record() */
          $dbinvoicepos->close();
       } /* if update successful */
     } else { /* make update only if invoice is not payed*/     
         $changecheck="Rechnung war bereits bezahlt";
     }
}



/* get file value from pfiles */ 
if($_POST["getvaluebutton"]) {
      $number = $_POST["number"];

      $querystring = sprintf("select * from phpa_invoices where number=%s",$number);
      if (!$db->query($querystring) && $db->next_record()) {      
         $pfile = $db->record["pfile"];
      }

      /* default; will be changed if successful */
      $changecheck="kein Gegenstandswert";

      $querystring= sprintf("select value from phpa_pfiles where number=%s", $pfile);
      // echo "<hr>".$querystring ."<hr>";
      $db->query($querystring);

      $value="'NULL'";
          
          /* get matching charge from table */
          if($db->next_record() && ($db->record["value"] !='')) {
             $value = $db->record["value"];
             $querystring= sprintf("update phpa_invoices set pfilevalue=%s where number=%s",
                           $value,$number);
             // echo "<hr>".$querystring ."<hr>";
             if (!$db->query($querystring))
                 $changecheck="Gegenstandswert ermittelt";
          }
} /* getvaluebutton */


if($_POST["getchargebutton"]) {
      $number = $_POST["number"];

      $querystring = sprintf("select * from phpa_invoices where number=%s",$number);
      if (!$db->query($querystring) && $db->next_record()) {      
         $pfile = $db->record["pfile"];
         $pfilevalue = $db->record["pfilevalue"];
      }

      /* default; will be changed if successful */
      $changecheck="keine Geb&uuml;hr";

        if($pfilevalue>0) {
          $querystring= sprintf("select max(rvgcharge) as maxcharge ".
             "from phpa_rvgcharges where rvgvalue <= %s", $pfilevalue);
          $db->query($querystring);

          $charge=0;
          
          /* get matching charge from table */
          if($db->next_record() && ($db->record["maxcharge"] !='')) {
             $charge = $db->record["maxcharge"];
             $querystring= sprintf("update phpa_invoices set charge=%s where number=%s",
                           $charge,$number);
             if (!$db->query($querystring))
                 $changecheck="Geb&uuml;hr ermittelt";
          }
        } /* endif calue >0 */
} /* getchargebutton */


/****************** end  change data of invoice *************************/

/****************** begin change data of details *************************/
/* add an invoice position*/
if($_POST["invposaddbutton"]) {
      /* import POST-VARS */
    $number       = $_POST["number"];
    $type         = $_POST["type"];
    $chargefactor = $_POST["chargefactor"];
    $amount       = toisonum($_POST["amount"],$LOCALE);
    $amountvat    = $_POST["amountvat"];
      
    /* check transmitted values */
    if(($chargefactor == 0) && ($amount == "")) {
      $amount = 0;
      $changecheck="Position ohne Wert";
    } else {  
      /* if chargefactor available, use it */
      if($chargefactor > 0) {
        /* get file value to calculate charges */
        $querystring=sprintf("select pfilevalue, charge ".
           "from phpa_invoices where number=%s",$number);
        $db->query($querystring);
        $charge=0;
        if($db->next_record())
          $charge=$db->record["charge"];
        
        if($charge>0) {
          $amount=$charge*$chargefactor;
        } /* if charge >0 */
      }  /* endif chargefactor available, use it */
      if($amount == 0) { /* Kein Wert vorhanden */
         /* do nothing but show that there's no value */
         $changecheck="Position ohne Wert";
      } else {
         /* evaluate vat */
         $invvat=0;

         $querystring=sprintf("select * ".
           "from phpa_invoicetypes ".
           "where number=%s",$type);
          $db->query($querystring);
         
          if($db->next_record())
            $amountvat=$db->record["vat_percent"];

         if($amountvat > 0) $invvat=$amount * $amountvat / 100;
         
         /* insert new invoice position */
         $querystring = sprintf("insert into phpa_invoicepos ".
           "(invoice, invoicetype, chargefactor, amount, vat) " .
           "values(%s, %s, %s, %s, %s)", 
           $number, $type, nullcorr($chargefactor), 
           nullcorr($amount), $invvat);
         if (!$db->query($querystring)) {
                $changecheck="Position gespeichert";
         }
      } /* endif amount == 0 */
    } /* endif check transmitted values */
} /* if invposaddbutton */

/********** delete an invoice position *****************/
elseif($_GET["invposdel"]) { /* use elseif to prevent double call */
      /* import POST-VARS */
      $ipnumber = $_GET["ipnumber"];
      $querystring=sprintf("delete from phpa_invoicepos where number=%s",$ipnumber);
      if (!$db->query($querystring)) {
              $changecheck="Position gel&ouml;scht";
      }
}
/****************** end  change data of details *************************/
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
  echo "<HEAD><TITLE>PHPAdvocat - Rechnung</TITLE>";
  echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";

  echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

  echo "<TABLE width=100%><TR><TD width=200 valign=top>";

  /* here comes the menue */

  $phpa_menue->account=$user;
  $phpa_menue->selected = 2;
   array_insert($phpa_menue->contents,
      array( sprintf("&nbsp;&nbsp;<b><a href=pfileedit.php?pnumber=%s&detail=3>".
                     "Akte bearbeiten</a></b>",$pnumber)), 1);
   array_insert($phpa_menue->contents,
      array('&nbsp;&nbsp;&nbsp;&nbsp;<b>Rechnung</b>'), 2);

  $phpa_menue->draw_menue();

/* display Title */
echo "</TD><TD><CENTER><H1>Bearbeitung Rechnung</H1></CENTER>";


/* get record from database */
$querystring = 
  sprintf("select * from phpa_invoices where number=%s", $number);

$db->query($querystring);
$db->next_record();

/* database connection for drop down lists */
$dblist = new www_db;
$dblist->connect($user, $passwd);


echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
/* display status at right side */
echo "<TD ALIGN=RIGHT><b>". $changecheck. "</b></A></TD>";

print "</tr></table>\n";

print "<hr><center>";


 /* beginning frame of dialog width=90% */
  /* beginning frame of dialog */
$invoicedialog = new htmldialog;


/* only show file-nr, hidden detail nr */

$invoicedialog->addinput("Rechnungsnummer:", 
sprintf("<input name=number type=hidden value=\"%s\">%s\n".
        "<input name=detail type=hidden value=\"%s\">\n",
        $db->record["number"], $db->record["number"], $detail));

/* edit value of pfile */
$invoicedialog->addinput("Gegenstandswert:", 
sprintf("<input name=pfilevalue type=text size=12 tabindex=1 value=%s align=right>\n".
       /* Button to get value from pfiles */
       "<input name=getvaluebutton type=submit tabindex=8 ".
       "value=\"von Akte &uuml;bernehmen\">\n",
         tolocalnum($db->record["pfilevalue"],$LOCALE)));


/* edit standard charge */
$invoicedialog->addinput("Geb&uuml;hr nach RVG:", 
sprintf("<input name=charge type=text size=12 tabindex=2 value=%s align=right>\n".
       /* Button to get value from pfiles */
       "<input name=getchargebutton type=submit tabindex=9 ".
       "value=\"aus Tabelle ermitteln\"></tr>\n", 
       tolocalnum($db->record["charge"],$LOCALE)));


/* input create date */
$invoicedialog->addinput("Datum:", 
sprintf("<input name=createdate type=text size=10 tabindex=3 value=\"%s\" ".
        "onDblClick=\"this.value=heute()\" >(TT.MM.JJJJ)\n",
        tolocaldate($db->record["createdate"],$LOCALE)));

/* input pay date */
$invoicedialog->addinput("Bezahlt am:", 
sprintf("<input name=paydate type=text size=10 tabindex=4 value=\"%s\" ".
        "onDblClick=\"this.value=heute()\" >(TT.MM.JJJJ)\n",
        tolocaldate($db->record["paydate"],$LOCALE)));


/* choose addressee */
/* data for drop down list */
$querystring="select * from phpa_partner order by name, organization";
$dblist->query($querystring);

$optionlist = "<select name=address tabindex=5>\n";
   while($dblist->next_record()) {
      if($dblist->record["number"] == $db->record["address"])
        $optionlist .= sprintf("<option selected value=\"%s\">%s,%s; %s\n",
          $dblist->record["number"],
          $dblist->record["name"],
          $dblist->record["prename"],
          $dblist->record["organization"]);
      else
        $optionlist .= sprintf("<option value=\"%s\">%s,%s; %s\n",
          $dblist->record["number"],
          $dblist->record["name"],
          $dblist->record["prename"],
          $dblist->record["organization"]);
   }
$optionlist .= "</select>\n";
$invoicedialog->addinput("Adresse:",$optionlist);


/* choose pfile */
/* data for drop down list */
$querystring="select * from phpa_pfiles";
$dblist->query($querystring);

$optionlist = "<select name=pfile tabindex=6>\n";
   while($dblist->next_record()) {
      if($dblist->record["number"] == $db->record["pfile"])
        $optionlist .= sprintf("<option selected value=\"%s\">%s(%s), %s\n",
          $dblist->record["number"],
          $dblist->record["processregister"],
          $dblist->record["number"],
          $dblist->record["subject"]);
      else
        $optionlist .= sprintf("<option value=\"%s\">%s(%s), %s\n",
          $dblist->record["number"],
          $dblist->record["processregister"],
          $dblist->record["number"],
          $dblist->record["subject"]);
   }
$optionlist .= "</select>\n";
$invoicedialog->addinput("Akte:",$optionlist);


/* enter free text for invoice */
$invoicedialog->addinput("Text:",
       sprintf("<textarea cols=40 rows=5 name=invoicetext>%s</textarea>",
       $db->record["invoicetext"]));


/* display demands */
/* first we assume to have no demands */
$demand = 0.0;

$querystring=sprintf("select sum(am.incomingamount)-sum(am.outgoingamount) as sum ".
                     "from phpa_expenditures as ex, phpa_amounts as am ". 
                     "where (ex.amount = am.number or ex.vatamount = am.number) ".
                     "and ex.pfile=%s", $pnumber);
$dblist->query($querystring);

/* fill $demand only if database query is not null */
if($dblist->next_record() && ($dblist->record["sum"] != '')) {
   $demand = $dblist->record["sum"];
} /* end query */

/* mark positive amount as green */
if($demand < 0) {
   $color="red";
} else {
   $color="green";
}

/* display it */
$invoicedialog->addinput('Stand Kostenblatt:', 
          tolocalnum(sprintf('%.2f',$demand),$LOCALE). ' &euro;', '', $color);


/* button for saving data and button for closing invoices */


$invoicedialog->addinput("<input name=invoiceeditbutton type=submit ".
   "tabindex=7 value=Sichern>",
   "<input name=invoicepaybutton type=submit ".
   "tabindex=7 value=\"Rechnung bezahlen\" ".
   "onClick=\"return confirm('Rechnung schliessen (Status: Bezahlt)?')\">");
   

$invoicedialog->out();

printf("</table></center>");



/* **************************** Detail ********************************** */


echo "<hr><a NAME=detail></a>";
if (!$detail) $detail=1;


/* display details */

switch ($detail) {
        case "1": /* invoice positions */
                $querystring = sprintf("select ip.number, it.description, ".
                  "ip.chargefactor, ip.amount, ip.vat ".
                  "from phpa_invoicetypes it, phpa_invoicepos ip ".
                  "where ip.invoice=%s and ip.invoicetype=it.number",
                   $number);
                /* echo "<hr>" . $querystring . "<hr>"; */
                $db->query($querystring);

                printf("<table  class=listtable>\n");
                /* Display header */
                printf("<tr><th>Nummer</th><th>Typ</th><th>Faktor</th>".
                       "<th>Betrag</th><th>MwSt.</th><th></th></tr>");
                       
                /* Display all invoice positions */
                $invoicesum = 0.0;
                $invoicesumvat = 0.0;
                $taxrate = 0.16;
                $posnumber=1;

                while($db->next_record()) {
                        printf("<tr><td>%s</td>", $posnumber++);
                        printf("<td>%s</td>", $db->record["description"]);
                        printf("<td>");
                        if($db->record["chargefactor"] != 0)
                          printf("%.2f", $db->record["chargefactor"]);
                        printf("</td>");
                        printf("<td align=right>%s</td>", tolocalnum($db->record["amount"],$LOCALE));
                        printf("<td align=right>%s</td>", tolocalnum($db->record["vat"],$LOCALE));
                        $invoicesum += (float) $db->record["amount"];
                        $invoicesumvat += (float) $db->record["vat"];
                        printf("<td><a href=\"$PHP_SELF?ipnumber=%s&number=%s".

                        "&invposdel=1&detail=%s#detail\" " .
                        "onClick=\"return confirm('Eintrag loeschen?')\">" .
                        "<img alt=Del src=\"images/trash-x.png\" border=0>".
                        "</a></td></tr>\n",
                        $db->record["number"], $number, $detail);

                }

                /* last row is an input for new positions */
                printf("<tr bgcolor=#e0e0e0><FORM METHOD=POST ACTION=\"$PHP_SELF\">");
                printf("<td><input name=number type=hidden value=%s>Neu</td>", $number);
                /* data for drop down list */
                $querystring="select * from phpa_invoicetypes";
                $dblist->query($querystring);
                printf("<td><select name=type >\n");
                  while($dblist->next_record()) {
                     printf("<option value=%s>%s %.1f%% MwSt\n",
                     $dblist->record["number"],
                     $dblist->record["description"],
                     $dblist->record["vat_percent"]);
                  }
                printf("</select></td>\n");
                
                printf("<td><select name=chargefactor>\n");
                printf("<option value=0.0>\n");
                printf("<option value=0.3>0.3\n");
                printf("<option value=0.5>0.5\n");
                printf("<option value=0.75>0.75\n");
                printf("<option value=1.0>1.0\n");
                printf("<option value=1.2>1.2\n");
                printf("<option value=1.3>1.3\n");
                printf("<option value=1.5>1.5\n");
                printf("</select></td>\n");
                
                printf("<td colspan=2><input name=amount type=text size=10></td>\n");
//                printf("<td><input name=amountvat type=checkbox checked=checked></td>\n");
/*
                printf("<td><select name=amountvat>\n");
                  printf("<option value=16>16%%</option>\n");
                  printf("<option value=7>7%%</option>\n");
                  printf("<option value=0>0%%</option>\n");
                printf("</select></td>\n");
*/
                printf("<td><input name=invposaddbutton type=submit value=Neu></td>");
                printf("</FORM></tr>");


                /* Display invoice sum */
                printf("<tr><td colspan=3>Gesamt</td><td align=right>%s</td><td align=right>%s</td></tr>",
                   tolocalnum(sprintf('%.2f',$invoicesum),$LOCALE), 
                   tolocalnum(sprintf('%.2f',$invoicesumvat),$LOCALE));
                // $invoicesum += $invoicesum * $taxrate;
                $invoicesum += $invoicesumvat;
                printf("<tr><td colspan=3><b>Gesamt incl. MwSt.</b></td>".
                   "<td colspan=2 align=right><b>%s</b></td></tr>",
                   tolocalnum(sprintf('%.2f',$invoicesum),$LOCALE));



                printf("</table></center>\n");

                break;

}


$dblist->close();
$db->close();

echo "<hr>";
printf ("<a href=invoicepdf.php?number=%s target=_BLANK>".
        "PDF-Ausgabe</a>\n",$number);
/* end framework */
echo "</TD></TR></TABLE>";

/* End HTML PAGE */
echo "</BODY></HTML>";

?>
