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
require("./fpdf/fpdf.php");

/* Get User Account from Session Vars */ 
$user = $_SESSION["dbuser"];
$passwd = $_SESSION["dbpasswd"];

$changecheck="";

/* initialize database */
$db = new www_db;
$db->connect($user, $passwd);

/* possibly it is better to take thit vorm config table */
$taxrate = 0.16;
$currency= 'EUR';

/* import invoice number if transmitted by GET or POST */
if($_POST["number"] !=0) {
  $number = $_POST["number"];
} elseif($_GET["number"] !=0) {
  $number = $_GET["number"];
}


/* get pfile number */
$querystring=sprintf("select * ".
               "from phpa_invoices where number=%s", $number);
// echo "<hr>0".$querystring."<hr>";
if((!$db->query($querystring)) && $db->next_record()) { 
  $pnumber=$db->record["pfile"];
  $value=$db->record["pfilevalue"];
  $charge=$db->record["charge"];
  $address=$db->record["address"];
  $createdate=tolocaldate($db->record["createdate"],$LOCALE);
  $invoicetext=$db->record["invoicetext"];

}  

/* get partner number */
$querystring=sprintf("select * from phpa_pfiles where number=%s", $pnumber);
// echo "<hr>1".$querystring."<hr>";
if((!$db->query($querystring)) && $db->next_record()) { 
  $partner=$db->record["partner"];
  $court=$db->record["court"];
  $processregister=$db->record["processregister"];
  $pfilecreatedate=tolocaldate($db->record["createdate"],$LOCALE);
  
  $pfileenddate=tolocaldate($db->record["enddate"],$LOCALE);
  /* if no enddate given, use the date today */
  if($pfileenddate =='') $pfileenddate=tolocaldate(date("Y-m-d", time()),$LOCALE);
  
  $pfilesubject=$db->record["subject"];
}  

/* get address  */
$querystring=sprintf("select * from phpa_partner where number=%s", $address);
// echo "<hr>2".$querystring."<hr>";
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

/* get court  */
$querystring=sprintf("select organization from phpa_partner where number=%s", $court);
// echo "<hr>3".$querystring."<hr>";
if((!$db->query($querystring)) && $db->next_record()) { 
  $courtorganization=$db->record["organization"];
}


$querystring=sprintf("select * from phpa_config where number=1");
// echo "<hr>4".$querystring."<hr>";
if((!$db->query($querystring)) && $db->next_record()) { 
  $configtitle=$db->record["title"];
  $configname=$db->record["name"];
  $configprename=$db->record["prename"];
  $configorganization=$db->record["organization"];
  $configstreet=$db->record["street"];
  $configzip=$db->record["zip"];
  $configcity=$db->record["city"];
  $configbank=trim($db->record["bank"]).
    ', BLZ:'.trim($db->record["bank_id"].
    ', Konto:'.trim($db->record["account"]));
  $configvat_id=$db->record["vat_id"];
  $configemail=$db->record["email"];
}


/* set letter phrases */
$subject = sprintf("Nummer: %s\n", $number);
$subject .= sprintf("Akte %s \n\n", $processregister);
$subject .= sprintf("In Sachen %s \n", $pfilesubject);
$subject .= sprintf(" %s \n\n", $courtorganization);
$subject .= sprintf("Leistungszeitraum: %s bis %s ", 
                $pfilecreatedate, $pfileenddate);


$closing = "Mit freundlichen Grüßen ";
$signature = $configprename.' '. $configname;

/* start PDF definitions */
  
class PDF extends FPDF
  {
    //Page header
    function Header()
	 {
	    //Logo
       // $this->Image('logo_pb.png',10,8,33);
	    //Arial bold 15
	    $this->SetFont('Arial','',10);
       //Move to the right
       // $this->Cell(0);
       //Title
       $this->Cell(95,5,$GLOBALS["configorganization"],0,0);
       $this->Cell(0,5,$GLOBALS["configstreet"],0,1,'R');

       $this->Cell(95,5,$GLOBALS["configemail"],B,0);
       $this->Cell(0,5,$GLOBALS["configzip"]." ". $GLOBALS["configcity"],B,1,'R');

       //Line break
       $this->Ln(20);
    }
    
    //Page footer
  function Footer()
    {
      //Position at 2.5 cm from bottom
      $this->SetY(-25);

      $this->SetFont('Arial','',8);
      $this->Cell(0,4,$GLOBALS["configbank"],T,1);
      $this->Cell(0,4,"Umsatzsteuer-Nr.:".$GLOBALS["configvat_id"],0,0);

       // $this->Cell(0,5,$GLOBALS["configstreet"],T,1,'R');


      //Arial italic 8
      $this->SetFont('Arial','I',8);
      //Page number
      $this->Cell(0,4,'Seite '.$this->PageNo().'/{nb}',0,0,'R');
    }
}

header('Pragma: public');
/* begin new PDF page */
$pdf=new PDF();
$pdf->SetSubject(sprintf("Rechnung Nr %s, Akte %s", $number, $processregister));
$pdf->SetAuthor("PHPAdvocat");


$pdf->AliasNbPages();
$pdf->SetTopMargin(15);
$pdf->SetLeftMargin(25);
$pdf->SetRightMargin(20);
$pdf->AddPage();

/* sender line above adressee */
$pdf->SetFont('Arial','',6);
$pline=sprintf("%s, %s, %s %s",
    $configorganization, 
    $configstreet, $configzip, $configcity);
$pdf->Cell(0,10,$pline ,0,1);

/* Addressee */
$pdf->SetFont('Times','',12);
$pdf->Cell(0,5,$partnertitle ,0,1);
$pdf->Cell(0,5,$partnerprename. ' ' .$partnername ,0,1);
$pdf->Cell(0,5,$partnerorganization ,0,1);
$pdf->Cell(0,5,$partnerstreet ,0,1);

$pdf->SetFont('Times','B',12);
$pdf->Cell(0,10,$partnerzip.' '.$partnercity ,0,1);

/* display invoice date */
$pdf->Cell(0,10,$createdate ,0,1,'R');

/* here begins the real part of the invoice */
/* first all iformations needed as header */
$pdf->SetFont('Times','B',14);
$pdf->Cell(0,15, 'Kostenrechnung' ,0,1,'C');


/* display subject */
$pdf->SetFont('Times','B',11);
$pdf->MultiCell(0,5, $subject ,0,1);
/* leave space */
$pdf->Cell(0,10, '' ,0,1);

$pdf->SetFont('Times','',11);

$pdf->MultiCell(0,5, $invoicetext ,0,1);
$pdf->Cell(0,10, 'Gegenstandswert: '.tolocalnum($value,$LOCALE).' '.$currency ,0,1);



 /* Display Invoice Positions */
  $querystring = sprintf("select ip.number, it.description, ".
      "ip.chargefactor, ip.amount, ip.vat ".
      "from phpa_invoicetypes it, phpa_invoicepos ip ".
      "where ip.invoice=%s and ip.invoicetype=it.number",
       $number);
    /* echo "<hr>" . $querystring . "<hr>"; */
  $db->query($querystring);

    $invoicesum = 0.0;
    $posnum =1;

  /* create table headers */
  $pdf->Cell(10,5,'Nr.',B,0,'R');
  $pdf->Cell(85,5,'Gegenstand',B,0);
  $pdf->Cell(15,5,'Faktor',B,0,'R');
  $pdf->Cell(30,5,'Betrag',B,0,'R');
  $pdf->Cell(25,5,'MwSt.',B,1,'R');
  

    while($db->next_record()) {
        $pdf->Cell(10,5,$posnum++,0,0,'R');
        $pdf->Cell(85,5,$db->record["description"],0,0);
         if($db->record["chargefactor"] != 0) {
              $pdf->Cell(15,5,tolocalnum(sprintf("%.2f", $db->record["chargefactor"]),$LOCALE),0,0,'R');
         } else {
              $pdf->Cell(15,5,'',0,0);
         }

        $pdf->Cell(30,5,tolocalnum($db->record["amount"],$LOCALE).' '.$currency ,0,0,'R');
        $invoicesum += (float) $db->record["amount"];
        $pdf->Cell(25,5,tolocalnum($db->record["vat"],$LOCALE).' '.$currency ,0,1,'R');
        $invoicesumvat += (float) $db->record["vat"];
    }
  
  $pdf->Cell(110,5,'Gesamt',T,0);
  $pdf->Cell(30,5,tolocalnum(sprintf('%.2f',$invoicesum),$LOCALE).' '.$currency,T,0,'R');  
  $pdf->Cell(25,5,tolocalnum(sprintf('%.2f',$invoicesumvat),$LOCALE).' '.$currency,T,1,'R');  

  $pdf->SetFont('Times','B',11);
  $invoicesum += $invoicesumvat;
  $pdf->Cell(135,10,'Gesamt incl. MwSt.',T,0);
  $pdf->Cell(30,10,tolocalnum(sprintf('%.2f',$invoicesum),$LOCALE).' '.$currency,T,1,'R');  
  $pdf->SetFont('Times','',11);

  $pdf->Cell(0,15,$closing,0,1);  
  $pdf->Cell(0,10,$signature,0,1);  

$pdf->Output();
?>
