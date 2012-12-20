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

/* Klasse fuer den transparenten Zugriff auf PostgreSQL */

class db_SQL {
        var $database;        /* DB-Name
        var $user;        /* user und passwort fuer DB */
        var $passwd;

        var $conn;        /* ID der DB-Verbindung */
        var $querytext;        /* aktuelles SQL Statement */
        var $query_id;        /* aktuelle SQL Statement-ID */
        var $err;        /* Letzte Fehlermeldung */

        var $record = array(); /* Aktueller Datensatz */
        var $allrecords = array(); /* alle Datensaetze */
        var $row = 0;        /* Aktuelle Zeilennummer */
        var $rows = 0;        /* Anzahl Zeilen */

        function halt($mesg) {
                printf("</td></tr></table>Datenbank-Fehler %s<br>\n", $mesg);
                printf("IFX-Error: %s<br>\n", ifx_errormsg());
                die("Session halted");
        }

        function connect ($ifuser, $ifpasswd) {
                $this->user=$ifuser;
                $this->passwd=$ifpasswd;
                $this->conn=ifx_connect($this->database, $this->user,
                         $this->passwd);
                if(!$this->conn) {
                        $this->halt("Database connection failed");
                }
        }

        function htmltable ($tableoptions) {
                $this->row=1;
                $resulttable='';
                if($this->rows > 0){
                        $resulttable=sprintf("<table %s>", $tableoptions);
                        /* Ueberschriften */
                        if($this->row <= $this->rows) {
                                $resulttable=$resulttable . "<tr>";
                                while(list($key, $value)=each($this->allrecords[$this->row])){
                                        $resulttable=$resulttable . "<th>" . $key . "</th>";
                                }
                                $resulttable=$resulttable . "</tr>\n";
                                reset($this->allrecords[$this->row]);
                        }

                        /* Echte Daten */
                        while($this->row <= $this->rows) {
                                $resulttable=$resulttable . "<tr>";
                                while(list($key, $value)=each($this->allrecords[$this->row])){
                                        $resulttable=$resulttable . "<td>" . $value . "</td>";
                                }
                                $resulttable=$resulttable . "</tr>\n";
                                $this->row++;
                        }
                        $resulttable=$resulttable . "</table>\n";
                }
                return $resulttable;
        }

        function query($querystring) {
                $this->rows = 0;
                $this->querytext = $querystring;
                $dummy = $querystring;
                if($this->query_id) {
                        ifx_free_result($this->query_id);
                }

                $this->query_id = ifx_query($this->querytext, $this->conn);

                /*
                echo "<hr>Laenge:" .
                     strpos(strtolower(trim($dummy)), "select") ."<hr>";
                */

                if($this->query_id)  {
                        /* if (strlen(stristr(trim($dummy), "select")) > 0 ) { */
                        if (strpos(strtolower(trim($dummy)), "select") == 0) {
                        while($this->record = ifx_fetch_row($this->query_id))  {
                                $this->rows++;
                                $this->allrecords[$this->rows] = $this->record;
                        }
                        $this->row = 0;
                        }
                } else {
                        $this->rows = 0;
                        $this->row = 0;
                        $this->halt("Query failed!");
                }
        }

        function next_record() {
                if($this->row < $this->rows) {
                        $this->row++;
                        $this->record = $this->allrecords[$this->row];
                        $status = is_array($this->record);
                } else {
                        $status = FALSE;
                }
                return $status;
        }


        function seek($pos) {
                if($pos >= 0 && $pos < $this->rows) {
                        $this->row = $pos;
                        $this->record = $this->allrecords[$this->row];
                } else {
                        $status = FALSE;
                }
                return $status;
        }

        function status() {
                $status = ifx_error();
        }


        function close() {
                $this->query = "";
                $this->rows = 0;
                $this->row = 0;
                ifx_close($this->conn);
        }
}
?>
