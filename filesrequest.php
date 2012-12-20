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

/* if new file is requested and address is empty, jump to partneredit */
if(($_POST["pfileaddbutton"]) && (0==$_POST["partner"])){
  header ("Location: partneredit.php");
  die;
}

$changecheck="";

/* initiate database */
$db = new www_db;
$db->connect($user, $passwd);

/*++++++++++++ handle changed data from GET or POST +++++++++*/

/******************   end insert new pfile with known partner **********/

/* add new record from last line form */
/* first: Button in pfilerequest.php, second button in partneredit.php */
if($_POST["pfileaddbutton"] || $_POST["partnereditbutton"])
{

  /************ begin insert new pfile with known partner **********/
  if($_POST["partnereditbutton"]) { /* button from partneredit.php */
    /* create new pfile with new partner */
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
    $partner=$_POST["number"];
    $createdate=date('Y-m-d', time());
  } else { /* end create new partner */
    /* if coming from filesrequest partner number is in $partner */
    $partner=$_POST["partner"];
    $createdate=toisodate($_POST["createdate"], $LOCALE);
  }

  $subject=$_POST["subject"];

  /* generate processregister */
  $year=date("Y", time());
  /* initial value if it is the first in this year */
  $processregister=$year . "-001";
  // echo "<hr>". $processregister . "<hr>";
  
  /* get the higest processregister for current year */
  $querystring = sprintf("select max(processregister) as procreg ".
                 "from phpa_pfiles ".
                 "where processregister like '%s%%'",$year);
  // echo "<hr>". $querystring . "<hr>";

  /* extract running number part and add one */
  if (!$db->query($querystring) && $db->next_record() 
      && ($db->record["procreg"]!="")) {
       $procreg=$db->record["procreg"];
     $rnumber=substr($procreg,5,3);
     $processregister=$year . "-".sprintf("%03.0f",$rnumber+1);
  }
  // echo "<hr>". $processregister . "<hr>";


  $querystring = sprintf("insert into phpa_pfiles (processregister, ".
                 "createdate, partner, subject) ".
    "values ('%s', %s, %s, '%s')", $processregister, 
    nullcorr($createdate), $partner, $subject);
  // echo "<hr>". $querystring . "<hr>";
  if (!$db->query($querystring)) {
      $changecheck="Neue Akte";
  }
}

/* delete record from clicked link in table*/
if($_GET["pfiledel"] == 1)
{
   $pnumber=$_GET["pnumber"];
   /* since all usabel databases are able to keep the referntial*/
   /* integrity, there is no need to delete the details */
	$querystring = sprintf("delete from phpa_pfiles where number=%s", 
	  $pnumber);
    // echo "<hr>". $querystring . "<hr>";
  if (!$db->query($querystring)) {
      $changecheck="Akte gel&ouml;scht";
  }
}

/*++++++++++++ end of Data handling +++++++++++++++++++++++++*/

if($_POST["oldfiles"])    {$oldfiles=$_POST["oldfiles"]; }
elseif($_GET["oldfiles"]) {$oldfiles=$_GET["oldfiles"]; }
if($oldfiles =='') $oldfiles=0;

// echo "<hr>".$oldfiles."<hr>" ;


/* Begin HTML page */
echo "<HTML><HEAD>";
echo "<TITLE>PHPAdvocat - Liste Akten</TITLE>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"include/phpadvocat.css\">";
echo "</HEAD>";
echo "<BODY BGCOLOR=\"#FFFFFF\" TEXT=\"#000000\">\n";

/* create frame; left side for menu */
echo "<TABLE width=100%><TR><TD width=200 valign=\"top\">\n";

/* here comes the navigation menue */

  $phpa_menue->account=$user;
  $phpa_menue->selected=0;
  $phpa_menue->draw_menue();

/* display a chooser for actual or archieved files */
echo "<table width=100% valign=top><form METHOD=GET ACTION=$PHP_SELF><tr><td>";
if($oldfiles==1) {
  echo "<input type=checkbox name=oldfiles value=1 checked=on>";
} else {
  echo "<input type=checkbox name=oldfiles value=1>";
}
echo "alle Akten";
echo "</td><td>";
echo "<input type=submit name=actualselectbutton value=Go>";
echo "</td></tr></form></table>";
/* end of chooser for actual files */



echo "</TD><TD>\n";

/* display title */
echo "<CENTER><H1>Liste Akten</H1></CENTER>\n";


echo "<table width=100%><tr>\n";
echo "<td>" . date("d.m.Y", time()) . "</td>";
echo "<TD ALIGN=RIGHT>".$changecheck ."</TD>";
print "</tr></table>\n";

print "<hr><center>";

$querystring = 
  "select p.number as pnumber, ".
  "p.processregister as processregister, " .
  "pt.name as name, " .
  "p.createdate as cdate, " .
  "p.enddate as edate, " .
  "p.subject as subject " .
  "from phpa_pfiles p, phpa_partner pt " .
  "where pt.number=p.partner ";
  
/* complete where statement for archieved Files */
if($oldfiles =='0') {
  $querystring .= "and p.enddate is null "; }

/* Sort by table header */
switch ($_GET["fsort"]) {

  case "number"  :$querystring .= "order by processregister";
                  break;
  case "name"    :$querystring .= "order by name";
                  break;
  case "subject" :$querystring .= "order by subject";
                  break;

  default        :$querystring .= "order by createdate desc";
}               




//  echo "<hr>" . $querystring . "<hr>"; 

 $db->query($querystring);

printf("<table class=listtable><tbody>\n");
/* printf("<table style=\"border:2;width:90%%\"><tbody>\n");*/

/* table header */
printf("<th><a href=$PHP_SELF?fsort=number&oldfiles=%s>Register</a></th>",$oldfiles);
printf("<th><a href=$PHP_SELF?fsort=cdate&oldfiles=%s>Datum</a></th>",$oldfiles);
printf("<th><a href=$PHP_SELF?fsort=name&oldfiles=%s>Mandant</a></th>",$oldfiles);
printf("<th><a href=$PHP_SELF?fsort=subject&oldfiles=%s>Bezeichnung</a></th>",$oldfiles);
printf("<th></th>");

while($db->next_record()) {
   if($db->record["edate"] == '') {
     printf("<tr>");
   } else {
     /* make row grey if file is closed */
     printf("<tr bgcolor=grey>");
   }
	/* printf("<td>%s</td>", $db->row); */
	printf("<td><a href=\"pfileedit.php?pnumber=%s\">%s (%s)</a></td>",
		 $db->record["pnumber"], $db->record["processregister"], 
		 $db->record["pnumber"]);
	printf("<td>%s</td>", tolocaldate($db->record["cdate"],'DE'));
	printf("<td>%s</td>", $db->record["name"]);
	printf("<td>%s</td>", $db->record["subject"]);
	
   /* delete this record */
   printf("<td><a href=\"$PHP_SELF?pnumber=%s&pfiledel=1\" " .
         "onClick=\"return confirm('Eintrag loeschen?')" .
         "\"><img alt=Del src=\"images/trash-x.png\" border=0>".
         "</a></td></tr>\n", $db->record["pnumber"]);
	printf("</tr>\n");
}

/* last row is an input for new docs */
printf("<tr class=input><FORM METHOD=POST ACTION=\"$PHP_SELF\">");
printf("<td>Neu</td>");
printf("<td><input name=createdate type=text size=10 value='%s'></td>\n",
       date("d.m.Y", time()));

$querystring = sprintf("select * from phpa_partner order by name, prename");
$db->query($querystring);               		
printf("<td><select name=partner>\n");
printf("<option value=0>- Neue Adresse -\n");
while($db->next_record()) {
     printf("<option value=%s>%s, %s\n",
            $db->record["number"], $db->record["name"], $db->record["prename"]);
}
printf("</select></td>");

printf("<td><input name=subject type=text size=50></td>\n");
printf("<td><input name=pfileaddbutton type=submit value=Neu></td>");
printf("</FORM></tr>");
/* end of input row */

printf("</tbody class=listtable></table>\n"); 

printf("</table></form></center>");
$db->close();

/* end of page */
echo "<hr></TD></TR></TABLE></BODY></HTML>";

?>
