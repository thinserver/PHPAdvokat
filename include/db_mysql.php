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


/* Klasse fuer den transparenten Zugriff auf MySQL */

class db_SQL {
        var $host = "localhost";        // DB-Host
        var $database;        // DB-Name
        var $port;        // Port des Listeners
        var $user;        // user und passwort fuer DB
        var $passwd;

        var $conn;        // ID der DB-Verbindung
        var $querytext;        // aktuelles SQL Statement
        var $query_id;        // aktuelle SQL Statement-ID
        var $err;        // Letzte Fehlermeldung
        var $errno;        // Letzte Fehlernummer

        var $record = array(); // Aktueller Datensatz
        var $row = 0;        // Aktuelle Zeilennummer
        var $rows = 0;        // Anzahl Zeilen
        
        


        function halt($msg) {
            printf("</td></tr></table>Database error:</b> %s<br>\n", $msg);
            printf("MySQL Error</b>: %s (%s)<br>\n",
              $this->err,
              $this->errno);
            die("Session halted.");
          }

        function connect ($myuser,$mypasswd) {
            if($myuser && $mypasswd) {
                $this->user=$myuser;
                $this->passwd=$mypasswd;
                $this->conn=mysql_connect($this->host, $this->user, $this->passwd);
                $this->Errno = mysql_errno($this->conn);
                $this->Error = mysql_error($this->conn);

            }
            if(!$this->conn) {
                 $this->halt("Database connection failed");
            }
	    if(!mysql_select_db($this->database,$this->conn)) {
                 $this->halt("Database not available");
	    }
        }



        function query($querystring) {
                $this->querytext = $querystring;
                $this->query_id = mysql_query($this->querytext, $this->conn);
                $this->Errno = mysql_errno($this->conn);
                $this->Error = mysql_error($this->conn);
                if($this->query_id) {
		   if(strtolower(substr(trim($this->querytext),0,6)) == 'select'){
                        $this->rows = mysql_num_rows($this->query_id);
                   } else {
                        $this->rows = 0;
		   }
                        $this->row = 0;
                } else {
                        $this->rows = 0;
                        $this->row = 0;
                        $this->halt("Query failed!");
                }
        }


        function next_record() {
                if($this->row < $this->rows) {
                        $this->record = mysql_fetch_array($this->query_id);
		        $this->Errno = mysql_errno($this->conn);
		        $this->Error = mysql_error($this->conn);
                        $this->row +=1;
                        $status = is_array($this->record);
                } else {
                        $status = FALSE;
                }
                return $status;
        }


        function seek($pos) {
                if(($pos >= 0 && $pos < $this->rows) &&
                   mysql_data_seek($this->query_id, $pos)) {
		       $this->Errno = mysql_errno($this->conn);
		       $this->Error = mysql_error($this->conn);
                       $this->row = $pos;
                }
        }


        function close() {
                $this->query = "";
                $this->rows = 0;
                $this->row = 0;
                mysql_close($this->conn);
        }

        function htmltable () {
                $resulttable='';
                if($this->rows > 0){
                        $resulttable=sprintf("<table %s>", $tableoptions);
                        while($this->next_record()) {
                                /* Ueberschriften */
                                if($this->row == 1) {
                                        $resulttable=$resulttable . "<tr>";
                                        while(list($key, $value)=each($this->record)){
                                                $resulttable=$resulttable . "<th>" . $key . "</th>";
                                        }
                                $resulttable=$resulttable . "</tr>\n";
                                reset($this->record);
                                } /* Ende Ueberschriften */
                                $resulttable=$resulttable . "<tr>";
                                while(list($key, $value)=each($this->record)){
                                        $resulttable=$resulttable . "<td>" . $value . "</td>";
                                }
                                $resulttable=$resulttable . "</tr>\n";
                        }
                        $resulttable=$resulttable . "</table>\n";
                }
                return $resulttable;
        }

}
?>
