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


/* Klasse fuer den transparenten Zugriff auf PostgreSQL */

class db_SQL {
        var $host = "localhost";        // DB-Host
        var $database;        // DB-Name
        var $port = "5432";        // Port des Listeners
        var $user;        // user und passwort fuer DB
        var $passwd;

        var $conn;        // ID der DB-Verbindung
        var $querytext;        // aktuelles SQL Statement
        var $query_id;        // aktuelle SQL Statement-ID
        var $err;        // Letzte Fehlermeldung

        var $record = array(); // Aktueller Datensatz
        var $row = 0;        // Aktuelle Zeilennummer
        var $rows = 0;        // Anzahl Zeilen

        function halt($mesg) {
                printf("</td></tr></table>Datenbank-Fehler %s<br>\n", $mesg);
                printf("PSQL-Error: %s<br>\n", $this->err);
                die("Session halted");
        }

        function connect ($pguser,$pgpasswd) {
            if($pguser && $pgpasswd) {
                $this->user=$pguser;
                $this->passwd=$pgpasswd;
                $this->conn=pg_connect("host=$this->host port=$this->port
                         dbname=$this->database user=$this->user
                         password=$this->passwd");
            } else {
                $this->conn=pg_connect("host=$this->host port=$this->port
                         dbname=$this->database");
            }
            if(!$this->conn) {
                $this->halt("Database connection failed");
            }
        }

        function query($querystring) {
                $this->querytext = 'SET client_encoding = \'LATIN9\'';
                $this->query_id = pg_exec($this->conn, $this->querytext);
                $this->querytext = $querystring;
                $this->query_id = pg_exec($this->conn, $this->querytext);
                if($this->query_id) {
                        $this->rows = pg_numrows($this->query_id);
                        $this->row = 0;
                } else {
                        $this->rows = 0;
                        $this->row = 0;
                        $this->halt("Query failed!");
                }
        }

        function next_record() {
                if($this->row < $this->rows) {
                        $this->record = pg_fetch_array($this->query_id,
                                $this->row++);
                        $status = is_array($this->record);
                } else {
                        $status = FALSE;
                }
                return $status;
        }

        function seek($pos) {
                if($pos >= 0 && $pos < $this->rows) {
                        $this->row = $pos;
                }
        }

        function close() {
                $this->query = "";
                $this->rows = 0;
                $this->row = 0;
                pg_close($this->conn);
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
