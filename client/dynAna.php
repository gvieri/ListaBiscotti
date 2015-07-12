
<?php

/*
author Giovambattista Vieri 
(c) Giovambattista Vieri 2015 all rights reserved 

status alpha (this code has not be fully tested and surely is bugged) use to your own risk. 
license Affero GPL (AGPL) 

To use: install php-cli package. Then php ./dynAna.php 



*/




$shortopts = "u:"; // url required
$shortopts .= "c:"; // cookie to be used; 
$shortopts .= "s::"; // show html
$options = getopt($shortopts);
if(empty($options)) {
	echo "usage: -u <url to be tested> [-c cookie/s to used] [-s show html]\n";
	exit; 
}
@$url=$options['u']; 
@$strCookie=$options['c'];


$ch = curl_init($url);

$cookieList=array(); 

curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
if(strlen($strCookie) >1) {
	curl_setopt( $ch, CURLOPT_COOKIE, $strCookie ); 
}
$output=curl_exec($ch);

if(!curl_errno($ch))
{
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header_string = substr($output, 0, $header_size);
	$html = substr($output, $header_size);

	$header_rows = explode(PHP_EOL, $header_string);
	insertCookieInList( $header_rows, $url) ;
	
	$info = curl_getinfo($ch);
}

curl_close($ch);
processHtml($html); 
if (isset($options['s'])){ 
	echo "-------------------------------------------\n";
	echo $html; 
	echo "-------------------------------------------\n";
}

print_r ( $cookieList) ; 

function getLeaf($url)  {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output=curl_exec($ch);
	if(!curl_errno($ch))
		{
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header_string = substr($output, 0, $header_size);
		$html = substr($output, $header_size);


		$header_rows = explode(PHP_EOL, $header_string);
		insertCookieInList( $header_rows, $url) ;
	}	

}
function processJavascript($url)  {
	global $cookieList;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output=curl_exec($ch);
	if(!curl_errno($ch))
		{
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header_string = substr($output, 0, $header_size);
		$html = substr($output, $header_size);


		$header_rows = explode(PHP_EOL, $header_string);
		insertCookieInList( $header_rows, $url) ;
		$html = substr($output, $header_size);
		$lines = explode(PHP_EOL, $html);
		foreach ($lines as $line ){ 
			$line1=strtolower($line);	
			if(strpos($line1,'.cookie') !== false) 	{ 
				if(strlen($line1) >100) { 
					$dummy="...";
					$dummy.=substr($line, strpos($line1,'.cookie'),100); 
					$dummy.="...";
				} else $dummy=$line; 
				array_push($cookieList, $url."+|+".$dummy) ; 
			}

		}




	}	

}

function processHtml($html) { 
	$xml = new DOMDocument(); 

	@$xml->loadHTML($html); ////
	$xml->preserveWhiteSpace = false;
	$links = array(); 
	foreach($xml->getElementsByTagName('link') as $link) {
		$dummy=$link->getAttribute('href');
		if (strlen ($dummy) >1 ) { 
			getLeaf($dummy);
		} 
	}
	foreach($xml->getElementsByTagName('script') as $link) {
		$dummy=$link->getAttribute('src');
		if (strlen ($dummy) >1 ) { 
			processJavascript($dummy);
		} 
	}
	
	foreach($xml->getElementsByTagName('img') as $link) {
		$dummy=$link->getAttribute('src');
		if (strlen ($dummy) >1 ) { 
			getLeaf($dummy);
		} 
	}
	
}


function insertCookieInList( $headers, $url )   { 
	global $cookieList;
	foreach ($headers as $item)  { 
		if (strncmp($item, "Set-Cookie:",10)==0 ) { 
			// found a cookie! 
			array_push($cookieList, $url."+|+".$item) ; 
		}
	}
} 

?>

