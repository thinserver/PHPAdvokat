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


class htmldialog{
   var $dialogstring ="";
   var $contents = array(); /* array all menue parts */
   var $stringbegin;
   var $stringend;
   
   function htmldialog() {
      if($this->stringbegin == '') 
        $this->stringbegin = sprintf("<center><table class=inputtable><tr><td>\n".
                  "<FORM METHOD=POST ACTION=\"$PHP_SELF\"><table>\n");
      if($this->stringend == '') 
        $this->stringend = sprintf("</table></form></table>\n");
   }

   function addinput($label, $input, $lcolor="", $rcolor="") {
     //var $tempstring = '';
       $tempstring .= "<tr>";
       if($lcolor != "") {
          $tempstring .= sprintf("<td bgcolor=%s>",$lcolor);
        }else{
          $tempstring .= sprintf("<td>");
        }
        $tempstring .= sprintf("%s</td>\n", $label);
      
        if($rcolor != "") {
          $tempstring .= sprintf("<td bgcolor=%s>",$rcolor);
        }else{
          $tempstring .= sprintf("<td>");
        }
        $tempstring .= sprintf("%s</td>", $input);
        $tempstring .= "</tr>\n";
        
        $this->contents = array_merge($this->contents, array($tempstring));

    }


   function out() {
      echo $this->stringbegin;
      while(list($number, $string)=each($this->contents)) {
        echo $string;
      }
      echo $this->stringend;
   }

}



?>