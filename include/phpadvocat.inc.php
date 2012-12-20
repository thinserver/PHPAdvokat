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

/* set session name */
  session_name("PHPAdvocat");

/* start session handling */
  session_start();


  require("./include/menues.php");
  $PHP_SELF=$_SERVER["PHP_SELF"];
  
  $OSTYPE = 'UNIX';
  // $OSTYPE = 'WINDOWS';

  $DBSERVER='POSTGRESQL';
//  $DBSERVER='MYSQL';
//  $DBSERVER='INFORMIX';

  $LOCALE='DE';


/* PostgreSQL-Connection */
  if($DBSERVER=='POSTGRESQL') {
     require ("db_pgsql.php");

     class www_db extends db_SQL {
        var $database = "phpadvocat";
     }
  
/* Mysql-Connection */
  } elseif($DBSERVER=='MYSQL') {
     require("db_mysql.php");

     class www_db extends db_SQL {
        var $database = "phpadvocat";
     }

/* Informix-Connection */
  } elseif($DBSERVER=='INFORMIX') {
     require("db_ifx.php");

     class www_db extends db_SQL {
        var $database = "phpadvocat";
        }

}



/* functions for phpadvocat */

/* correct an empty date to 'NULL' */
function nullcorr($datefield) {
      if($datefield =="") { 
        return "NULL";
      } else {
        return "'".$datefield."'";
      }
}

/* changes an iso formatted date into local format */
function tolocaldate($datestring, $locale) {
 // $localstring='01.01.1998';
 $localstring='';

 $year=(int) substr($datestring, 0, 4);
 $month=(int) substr($datestring, 5, 2);
 $day=(int) substr($datestring, 8, 2);
 
 if(is_int($year) && is_int($month) && is_int($day) &&
    checkdate($month, $day, $year)) {
    switch (strtoupper($locale)) {
      case "DE": /* german date format */
        /* generate ISO-Format timestamp 1997-12-17 07:37:16-08 */
        $localstring = sprintf("%02d.%02d.%04d", $day, $month, $year);
        break;
    } /* end switch */
 }
 return $localstring;
}

/* changes an local formatted date into iso format */
function toisodate($datestring, $locale) {
 // $isostring='1998-01-01';
 $isostring='';
 switch (strtoupper($locale)) {
    case "DE": /* german date format */
      $datearray = explode('.', $datestring);
      $day=(int) $datearray[0];
      $month=(int) $datearray[1];
      $year=(int) $datearray[2];
      break;
 } /*case */

 if(is_int($year) && is_int($month) && is_int($day) &&
    checkdate($month, $day, $year)) {
    /* generate ISO-Format timestamp 1997-12-17 07:37:16-08 */
    $isostring= sprintf("%04d-%02d-%02d", $year, $month, $day);
 }
 return $isostring;
}

/* changes an local formatted number into iso format */
function toisonum($numstring, $locale) {
  $isostring='';
  switch (strtoupper($locale)) {
    case "DE": /* german number format */
       /* first delete all periods, then change comma into devimal points */
       $tempstring = str_replace(',', '.', str_replace('.', '', $numstring));
       // $tempstring = str_replace(',', '.', $numstring);
       if(is_numeric($tempstring)) $isostring = $tempstring;
       break;
  }
  return $isostring;
} /* end function */


/* changes an iso formatted number into local format */
function tolocalnum($numstring, $locale) {
  $localstring='';
  switch (strtoupper($locale)) {
    case "DE": /* german number format */
       /* first delete all periods, then change comma into devimal points */
       if(is_numeric($numstring)) {
          $localstring = str_replace('.', ',', $numstring);
       }
       break;
  }
  return $localstring;
} /* end function */



/* base menue for phpadvocat */

$phpa_menue = new vertical_menue;
$phpa_menue->version = "<a href=doc target=_blank>PHPAdvocat 0.8-6</a>";
$phpa_menue->ulogo = "images/phpadvocat.gif";
$phpa_menue->contents = array(
            "<A HREF=filesrequest.php>Akten</A>",
            "<A HREF=partnerrequest.php>Adressen</A>",
            "<A HREF=calendar.php>Kalender</A>",
            "<A HREF=statistics.php>Statistiken</A>",
            "<A HREF=bookkeepingrequest.php>Buchhaltung</A>",
            "<A HREF=admin.php>Administration</A>");

$phpa_menue->selected = 0;

/* global config variables */
// $tempfile='/tmp';


?>
