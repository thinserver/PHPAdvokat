<?php
  /**************************************************************************\
  * PHPAdvocat                                                               *
  * http://phpadvocat.sourceforge.net                                        *
  * By Burkhard Obergoeker <phpadvocat@obergoeker.de>                       *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

   require("./include/phpadvocat.inc.php");
   session_destroy();
?>
<HTML>
<HEAD>
<TITLE>PHPAdvocat - Anmeldung</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
<link rel="stylesheet" type="text/css" href="include/phpadvocat.css">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000">

<TABLE width=100%>
 <TR>
  <TD width=200 valign=top>
    <img SRC="images/phpadvocat.gif" height=100 width=185 align=ABSBOTTOM>

   </TD>
   <TD>
     <CENTER> <H1>PHPAdvocat</H1> </CENTER>

      <center>
      <table class=inputtable style={width:520;}>
       <tr>
        <td>
         <H2>Anmeldung</H1>
          <center>
          <form action="welcome.php" method="POST">
            <table>
<tr><td>Benutzername: </td><td><input type=text name=loginuser size=15></td></tr>
<tr><td>Passwort: </td><td><input type=password name=loginpasswd size=15></td></tr>
<tr><td><input value=Anmelden type=submit></td><td><input value=Abbruch type=reset></td></tr>
            </table>
           </form>
           </center>
          </td>
         </tr>
        </table>
       </CENTER>
</td></tr></table>
</body>
</html>
