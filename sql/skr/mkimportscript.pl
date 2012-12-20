#!/usr/bin/perl
# 
#
# insert into phpa_exp_categories (number,description) 
# values(1    ,'Ausstehende Einlage nicht eingefordert');

$datum='06.12.2006';

# Erste Zeile ist Ueberschrift => raus
if(defined($zeile = <STDIN>)) {

# Zeilenweise einlesen der Datei, Trenner ist Tab
  while(defined($zeile = <STDIN>)) {
  	# Aufspalten in einzelne Felder
	# @felder = split(/	/,$zeile);
	@felder = split(/;/,$zeile);

	# Number ist erstes Feld
	$number = $felder[0];

	# InventarNummer ist zweites Feld
	$description = $felder[1];

	$uva_code = $felder[2];
	$tax_category = $felder[3];
	$taxfactor = $felder[4];
	$in_or_out = $felder[6];

	print "insert into phpa_exp_categories ";
	print "(number,description,uva_code,tax_category,taxfactor,in_or_out) \n";

   print "values (";
	print $number . ", ";

	print "'" . $description . "', ";

	print $uva_code . ", ";

	print $tax_category . ", ";

	print "'" . $taxfactor . "', ";

	print "'" . $in_or_out . "'";
   print ");\n";

  }
}
