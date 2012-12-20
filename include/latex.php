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

function subst_letter_vars($templatefile, $targetfile, $pnumber, $address) {
  $returnval=0;
  $filecontent='';

  // echo "<hr> template:". $templatefile. " target:".$targetfile ."<hr>";

  /* initialize database */
  $db = new www_db;
  $db->connect($_SESSION["dbuser"], $_SESSION["dbpasswd"]);
  
  
  
  /* first load data form source file */
  if(file_exists($templatefile)){ 
     if($fd=fopen($templatefile, "r")) {
       $filecontent=fread($fd, filesize($templatefile));
       fclose($fd);

     } /* endif fopen($templatefile) */
  } /* endif file_exists($templatefile) */


   $querystring=sprintf("select * from phpa_config where number=1");
   if((!$db->query($querystring)) && $db->next_record()) { 
     $filecontent = str_ireplace('$CFG_TITLE', $db->record["title"], $filecontent);
     $filecontent = str_ireplace('$CFG_PRENAME', $db->record["prename"], $filecontent);
     $filecontent = str_ireplace('$CFG_NAME', $db->record["name"], $filecontent);
     $filecontent = str_ireplace('$CFG_ORGANIZATION', $db->record["organization"], $filecontent);
     $filecontent = str_ireplace('$CFG_STREET', $db->record["street"], $filecontent);
     $filecontent = str_ireplace('$CFG_ZIP', $db->record["zip"], $filecontent);
     $filecontent = str_ireplace('$CFG_CITY', $db->record["city"], $filecontent);
     $filecontent = str_ireplace('$CFG_PHONE', $db->record["phone"], $filecontent);
     $filecontent = str_ireplace('$CFG_FAX', $db->record["fax"], $filecontent);
     $filecontent = str_ireplace('$CFG_EMAIL', $db->record["email"], $filecontent);
     $filecontent = str_ireplace('$CFG_BANK_ID', $db->record["bank_id"], $filecontent);
     $filecontent = str_ireplace('$CFG_BANK', $db->record["bank"], $filecontent);
     $filecontent = str_ireplace('$CFG_ACCOUNT', $db->record["account"], $filecontent);
     $filecontent = str_ireplace('$CFG_VAT_ID', $db->record["vat_id"], $filecontent);
   }


   $querystring=sprintf("select * from phpa_partner where number=%s", $address);
   if((!$db->query($querystring)) && $db->next_record()) { 
     $filecontent = str_ireplace('$ADR_TITLE', $db->record["title"], $filecontent);
     $filecontent = str_ireplace('$ADR_PRENAME', $db->record["prename"], $filecontent);
     $filecontent = str_ireplace('$ADR_NAME', $db->record["name"], $filecontent);
     $filecontent = str_ireplace('$ADR_ORGANIZATION', $db->record["organization"], $filecontent);
     $filecontent = str_ireplace('$ADR_STREET', $db->record["street"], $filecontent);
     $filecontent = str_ireplace('$ADR_ZIP', $db->record["zip"], $filecontent);
     $filecontent = str_ireplace('$ADR_CITY', $db->record["city"], $filecontent);
     /* generate opening */
     if ($db->record["type"] == 'Person') {
       if ($db->record["title"] == 'Frau'){
         $opening = 'Sehr geehrte Frau '. $db->record["name"];
       }elseif ($db->record["title"] == 'Herr'){
         $opening = 'Sehr geehrter Herr '. $db->record["name"];
       }
     } else /* type == Firma/Greicht */ {
        $opening = 'Sehr geehrte Damen und Herren';
     } /* endif type == Person */
     $filecontent = str_ireplace('$OPENING', $opening, $filecontent);
   }


   $querystring=sprintf("select * from phpa_pfiles where number=%s", $pnumber);
   if((!$db->query($querystring)) && $db->next_record()) { 
      $filecontent = str_ireplace('$subject', $db->record["subject"], $filecontent);
      $filecontent = str_ireplace('$processregister', $db->record["processregister"], $filecontent);
   }  

   /* at last we strip the DOS-like ^M */
   $filecontent = str_ireplace(chr(13), '', $filecontent);



  /* last write generated text into target file */
  if($targetfile) {
     if(($fd=fopen($targetfile, "w")) && (fwrite($fd, $filecontent))) {
       fclose($fd);
       echo "written <hr>";
       $returnval=1;
     }
}


  // $db->close();
  
  // destroy $db;
  return $returnval;
} /* end function subst_letter-vars */
?>
