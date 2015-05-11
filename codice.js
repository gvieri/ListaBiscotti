/**
 *
 *  @author  Vieri Giovambattista
 *  @version 0,0
 *  @license AGPL 
 *  
 */


var loadUrl 	='http://www.ziogianni.com/'; // mettete qua' la url di partenza del vs test. 

var container	=document.getElementById("container");
var report	=document.getElementById("repo");
var el 		=document.getElementById("ifrm");

var report	=document.getElementById("repo");
var spedizione	=document.getElementById("spedizione");
var cookki;
var spedizioneOldSrc = spedizione.src;

el.setAttribute('src', loadUrl);

cookki	=el.contentDocument.cookie;
report.innerHTML="<hr>";



function faiTutto() {
	spedizione.src = spedizioneOldSrc;
	spedizione.onLoad=faiTutto0();
}

function faiTutto0() {
	var now = new Date().toLocaleString();
	setTimeout(function(){
		var formDomain;
		var formDateAndTime;
		var formCookieRawDate;
	
		formDomain=spedizione.contentWindow.document.getElementById("websitedomain"); 
		formDateAndTime=spedizione.contentWindow.document.getElementById("dateandtime"); 
		formCookieRawDate=spedizione.contentWindow.document.getElementById("cookierawdata"); 
		cookki=el.contentDocument.cookie;
		splitAndPrintCookie(cookki);
		report.innerHTML+="<hr>";
		// now start to fill the form 
		formDomain.value	= spedizione.src;
		formCookieRawDate.value	= cookki;
		formDateAndTime.value	= now;
	}, 3000);
}


function splitAndPrintCookie(myCookie) {
        var _tmp = myCookie
        var _split = _tmp.split(";");
	report.innerHTML+="<ol>";
	for( var i = 0 ; i < _split.length ; i++){
		report.innerHTML+="<li>"+_split[i]+"</li>";


	}
	report.innerHTML+="</ol>";
}

