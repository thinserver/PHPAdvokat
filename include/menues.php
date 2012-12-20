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

/* asseble menue */

class vertical_menue{
      var $contents = array(); /* array all menue parts */
      var $selected;           /* number of choosen point */
      var $ulogo;
      var $version;
      var $account;

   function draw_menue() {
      if ($this->ulogo) {
         printf ("<img SRC=\"%s\" height=100 width=185 align=ABSBOTTOM><br>%s\n",
           $this->ulogo, $this->version);
      }

      echo "<table bgcolor=\"#FFAAAA\" width=100%><tr><td><a href=login.php>" . $this->account .
           " abmelden</a></td></td></tr></table>";

      echo "<table bgcolor=\"#EEEEEE\" width=100%>";
         while(list($nummer, $wert)=each($this->contents)) {
            if($this->selected == $nummer) {
               printf("<tr><td bgcolor=\"BBBBBB\">%s</td></tr>\n", $wert);
            } else {
               printf("<tr><td>%s</td></tr>\n", $wert);
            }
         }
      echo "</table>";
   } /* draw_menue() */

}


function array_insert(&$original_array, $new_element, $position)  {
      $end_pos = count($original_array);
      if ($position <= $end_pos) {
                  $neues_array = array_slice($original_array, 0, $position);
            $neues_array = array_merge($neues_array, $new_element);
            $neues_array = array_merge($neues_array,
                  array_slice($original_array, $position, $end_pos));
            $original_array = $neues_array;
      } else {
            $neues_array = array_merge($original_array, $new_element);
            $original_array = $neues_array;
      }
}

?>
