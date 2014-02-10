// Jerry's Crypto-AJAX JavaScript and PHP Library
// Jan 8, 2014   Key West, FL US   
// email - jerry (@-symbol-) jerrywickey (.-symbol-) com   
// phone - eight hundred - seven two two - two two eight zero
//
// thanks to Stevish RSA http://stevish.com/rsa-encryption-in-pure-php 
// thanks to Ali Farhadi RC4 https://gist.github.com/farhadi/2185197
//
// This newest version of this file and its manual can be downloaded from 
// http://jerrywickey.com/test/testJerrysLibrary.php


// these functions will be called upon successful initiation respectively
//   cryptoStarted( JL_crybits, JL_cryname, JL_rc4keys)
//   cryptoUnavalable( JL_crybits, JL_cryname, JL_rc4keys)


// if this div is included in HTML page, secure communication status is updated to it
//    <div id="secureComIcon"></div>


//  path to jerrysLibrary.php  domain and subdirectory
//  set the directory path here.  Example   + '/your-path/';
//  the setting below presumes you've saved the PHP library file 
//  to the 'test/' directory on your server. 
var PHPlibpath= 'http://'+window.location.hostname + '/test/jerrysLibrary.php'; 


//  if PHP encryption or AJAX is not desired set JL_setAjax to false
//  miscellaneous functions are still availble
//  this can be set in the <body onload=  as well
var JL_setAjax= true;


var JL_crybits= 1024;
var JL_rc4keys= '';
var JL_speedts= 0;
var JL_enckeys= ''; 
var JL_cryname= '';
var JL_rsakey1= '';
var JL_rsakey2= '';
var JL_crytime= 0;
var JL_crydone= true;
var JL_crycomm= false;
var JL_cryActive= false;
var JL_ajax= new Array();
var JL_ajaxu= new Array();
var JL_crystart= 0;
if ( JL_setAjax && true){  // set this to false when testing is complete.
	multihttp(( PHPlibpath + '?serverhandshake=marko'), '', 'JL_enableAXCCStrue');
	JL_crystart= 10;
	JL_cryptcomm( 'Checking Communication Status', 10, 'whirl,blink');		
}


// User Crypto Functions =========================================================

function initCrypto( a){ 
	if ( parseInt( a) > 0){
		JL_crybits= parseInt( a);
	}
	if ( JL_crystart > 0 && JL_setAjax){
		JL_crystart--;
		setTimeout(( "initCrypto( "+a+")"), 500); 
	}else if ( JL_setAjax){	
		JL_rc4keys= '';
		JL_speedts= 0;
		JL_cryname= '';
		JL_rsakey1= '';
		JL_rsakey2= '';
		JL_crydone= true;
		JL_cryActive= false;
		var safe= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		for ( var i=0; i<10; i++){
			JL_cryname+= safe.charAt( parseInt( Math.random() * safe.length));
		}
		if (JL_crybits<50){ JL_crybits= 50; }
		if (JL_crybits>2048){ JL_crybits= 2048; }
		var c= new Array( 0, 50, 200, 500, 800, 1200, 1600, 2000);
		var b= new Array( 0,  1,   2,   3,   5,    7,   10,   15);
		var n= 0; while ( JL_crybits > c[++n]){}
		var result= '1';
		var str= '12345678901234567891';
		var a= '1234567';
		var b= '23456789002345678903';
		var d1= new Date(); 
		for (i=0; i<3; i++){ 		
			if ( i==1){ d1= new Date(); }
			if ( JL_bccomp( JL_bcmod( a, '2'), '1')==0) {
				result = JL_bcmod( JL_bcmul( result, str), b);
			}
			str= JL_bcmod( JL_bcmul( str, str), b);
			a= JL_bcdiv( a, '2');
			JL_bccomp( a, '0');
		} 
		var d2= new Date();
		JL_speedts= ''+( d2.getTime() - d1.getTime()); 
		for (i=0; i<b[n]; i++){
			multihttp(( PHPlibpath+'?newchannel='+JL_crybits+'&n='+JL_cryname+'&t='+JL_speedts), '', 'JL_initCryptoStep2')
		}
		JL_cryptcomm( 'Securing Encrypted Channel', 60, 'whirl,blink,count');
	}
}

function encryptToServer( str){
	if ( !JL_cryActive){
		alert ( "A secure channel has not yet been established.\n\nWait a few seconds and try again\n\n003");
		return 'none';	
	}
	JL_cryptcomm( '<span style="color:red">Encrypted Uplink</span>', 6, '');
	str= JL_randomFill( 'JerryWickey', ( 53- (( str.length + 14) % 53))) + 'auTheNtiCate_-' + str;
	var estr= JL_RC4crypt( str, JL_rc4keys);
	var valid= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
	var esc= '().*';
	var out= '';
	for (var i=0; i<estr.length; i++){
		if ( valid.indexOf( estr.charAt(i)) > -1){
			out+= estr.charAt(i);
		}else{
			out+= esc.charAt( Math.floor( estr.charCodeAt(i) / 64));
			out+= valid.charAt( estr.charCodeAt(i) % 64);		
		}
	}
	return out;
}

function decryptFromServer( str){
	if ( !JL_cryActive){
		alert ( "A secure channel has not yet been established.\n\nWait a few seconds and try again\n\n003");
		return 'none';	
	}
	JL_cryptcomm( '<span style="color:green">Decrypted Downlink</span>', 4, '');
	var dstr= JL_RC4crypt( str, JL_rc4keys);
	if ( dstr.indexOf( 'auTheNtiCate_-') == -1){
		alert( "WARNING:\n\nInvalid encrypted data received\n\nThis may simply be an Internet communication error");
	}
	return dstr.substring( dstr.indexOf( 'auTheNtiCate_-') + 14);
}


// User AJAX Functions 
// Cookie, Session and XDomain functions =========================================

function setcookie( aname, avalue, time, path, adomain){
	return synchttp(( PHPlibpath+'?setcky='
		+encodeURIComponent( aname)
		+'&v='+ encodeURIComponent( avalue)
		+'&t='+ encodeURIComponent( time)
		+'&p='+ encodeURIComponent( path)
		+'&d='+ encodeURIComponent( adomain)), '');	
}

function getcookie( aname){
	return synchttp(( PHPlibpath+'?getcky='+encodeURIComponent( aname)), '');
}

function setsession( aname, avalue){
	return synchttp(( PHPlibpath +'?setses=' +encodeURIComponent( aname) +'&v=' +encodeURIComponent( avalue)), '');
}

function getsession( aname){
	return synchttp(( PHPlibpath+'?getses='+encodeURIComponent( aname)), '');
}

function Xdomain( aurl, post, cookiejar, agent, timeout){
	return synchttp(( PHPlibpath+'?xdomain='+encodeURIComponent( aurl)
		+'&a='+encodeURIComponent( agent)+'&t='+encodeURIComponent( timeout)
		+'&c='+encodeURIComponent( cookiejar)), 
		('&p='+encodeURIComponent( post)));
}

function multiXdomain( aurl, post, cookiejar, agent, timeout, response){ 
		multihttp(( PHPlibpath+'?xdomain='+encodeURIComponent( aurl)
		+'&a='+encodeURIComponent( agent)
		+'&t='+encodeURIComponent( timeout)
		+'&c='+encodeURIComponent( cookiejar)), 
		('&p='+encodeURIComponent( post)), response);
}

function synchttp( where, post){
	var http= JL_browserspec();
	if ( http){
		var methodt= 'GET';
		if ( post.length>0){
			methodt= 'POST'
		}
		http.open( methodt, JL_cachefix( where), false);
		if ( post.length>0){
			http.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded");
		}
		http.send( post);
		return http.responseText;
	}else{
		return false;
	}
}

function multihttp( where, post, dowith){
	var w= -1;
	var u= 0;
	while ( u < JL_ajaxu.length && w==-1){
		if ( JL_ajaxu[u]!='busy'){ w= u; }
		u++;
	}
	if ( w==-1){ w= u; }
	JL_ajaxu[w]= 'busy';
	JL_ajax[w]= JL_browserspec();
	if ( JL_ajax[w]){
		var methodt= 'GET';
		if ( post.length>0){
			methodt= 'POST'
		}
		JL_ajax[w].open( methodt, JL_cachefix( where), true);
		if ( post.length>0){
			JL_ajax[w].setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		}
		if ( dowith!=''){
			JL_ajax[w].onreadystatechange= function(){
				if ( JL_ajax[w].readyState==4){
					eval( 'JL_ajaxu['+w+']= ""');
					eval( dowith+'("'+escape(JL_ajax[w].responseText)+'")');
				}
			}
		}
		JL_ajax[w].send( post);
	}else{
		w= false;
	}
	return w;
}


// User Miscellaneous Functions ==================================================

function ge(a){  
	return document.getElementById(a);
}

function ms(a){  
	if (a==1){a= 'default';}
	if (a==2){a= 'pointer';}
	document.body.style.cursor= a; 
}

function getypos(obj){  
	var y= 0
	while(obj){
		y= y+obj.offsetTop;
		obj= obj.offsetParent;
	}
	return y;
}

function getxpos(obj){  
	var x= 0
	while(obj){
		x= x+obj.offsetLeft;
		obj= obj.offsetParent;
	}
	return x;
}

function ekey(a){  
	var key= 0;
	if (window.event){
		key= window.event.keyCode;
	}else if (a){
		key= a.which;
	}
	var r= false;
	if (key==13){r= true;}
	return r;
}

function trim( txt, ofthese){  
	var trims= " \t\r\n";
	txt= ''+txt;
	ofthese= ''+ofthese;
	if (ofthese.length>0){trims= ofthese;}
	if (txt.length>0){
		while (trims.indexOf(txt.charAt(0))>=0 && txt.length>0){
			txt= txt.substring(1);
		}
		while (trims.indexOf(txt.charAt(txt.length-1))>=0 && txt.length>0){
			txt= txt.substring(0, txt.length-1);
		}		
	}
	return txt;
}

function subtute( replacethis, withthis, intext){  
	replacethis= ''+replacethis;
	withthis= ''+withthis;
	intext= ''+intext;
	if (replacethis.length > 0 && intext.length > 0){
		var position= 0;
		var occurance= intext.indexOf(replacethis);
		while (occurance != -1){
			intext= intext.substring(0, occurance) + withthis + intext.substring(replacethis.length+occurance);
			position= occurance + withthis.length;
			occurance= intext.indexOf(replacethis, position);
		}
	}	
	return intext;		
}


// internal functions ===========================================================

function JL_initCryptoStep2( a){
	if ( JL_setAjax){
		if ( JL_crydone){
			var b= unescape(a);
			if ( b.indexOf( 'public key,')==0){
				JL_crydone= false;
				var c= b.split( ',');
				JL_rsakey1= ''+c[1];
				JL_rsakey2= ''+c[2];
				var digits= parseInt( c[4]);
				JL_rc4keys= '' + parseInt(( Math.random() * 9) + 1);
				for (var i=1; i<digits; i++){
					JL_rc4keys+= '' + parseInt( Math.random() * 10);
				}
				JL_RSAencrypt( JL_rc4keys, c[1], c[2], 'JL_initCryptoStep3');
			}else if( b.indexOf('not found,')==0){
				multihttp(( PHPlibpath+'?newchannel='+JL_crybits+'&n='+JL_cryname+'&t='+JL_speedts), '', 'JL_initCryptoStep2')
			}
		}
	}else{
		JL_cryptcomm( 'Secure channel canceled', 3, '');				
	}
}

function JL_initCryptoStep3( k){
	var safe= '01234567890123456789024571';
	safe+= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	var digits= Math.floor( Math.log(2) * JL_crybits / Math.log(62));
	var tempkeys= '';
	while ( tempkeys.length < digits){
		if ( tempkeys != ''){ tempkeys+= '-'; }
		for ( var i=0; i<10; i++){
			tempkeys+= safe.charAt( Math.floor( Math.random() * safe.length));
		}
	}
	JL_enckeys= k;
	JL_cryActive= true;
	multihttp(( PHPlibpath+ '?setkey='+JL_enckeys+ '&b='+encryptToServer( tempkeys)+ '&n='+JL_cryname), '', 'JL_initCryptoStep4');
	JL_rc4keys= tempkeys;
	JL_crybits= Math.round( Math.log( 62) * JL_rc4keys.length / Math.log(2));
}

function JL_initCryptoStep4( a){
	if ( decryptFromServer( unescape( a))=='encryption_secured'){	
		JL_cryptcomm(( JL_crybits+'-bit Secure Channel Established'), 8, 'blink');
		try{
			cryptoStarted( JL_crybits, JL_cryname, JL_rc4keys);
		}catch(e){}
	}else{
		JL_cryActive= false;
		JL_rc4keys= '';
		JL_crybits= 0;
		if ( confirm( "A secure channel was not achieved\b\nTry again?")==true ){
			setTimeout( ("initCrypto( "+ JL_crybits+ ")"), 500);	
		}else{
			JL_cryptcomm( 'Secure channel canceled', 3, '');		
			try{
				cryptoUnavalable( JL_crybits, JL_cryname, JL_rc4keys);
			}catch(e){}
		}
	}
}

function JL_RSAencrypt( str, a, b, callback){
	JL_cryptcomm( 'Negotiating Session Key', 60, 'Whirl,Count' );
	JL_RSAencryptStep( (''+str), (''+a), (''+b), '1', callback, 0);
}

function JL_RSAencryptStep( str, a, b, result, callback, count){	
	count++;
	if ( JL_bccomp( JL_bcmod( a, '2'), '1')==0) {
		result = JL_bcmod( JL_bcmul( result, str), b);
	}
	str= JL_bcmod( JL_bcmul( str, str), b);
	a= JL_bcdiv( a, '2');
	if ( JL_bccomp( a, '0')!=0){
		var e= "JL_RSAencryptStep('" +str+"','" +a+"','" +b+"','" +result+"','" +callback +"'," +count+")";
		setTimeout( e, 10);	
		clearTimeout( JL_crytime);
		try{
			ge('cryptocount').innerHTML= ( 60 - count);
		}catch(e){}
	}else{
		eval( callback+'("'+ result+'")' );
	}
}

function JL_enableAXCCStrue( a){
	if ( unescape( a).indexOf( 'pollo ')!==0){
		JL_setAjax= false;
		JL_cryptcomm( ' ', 0.5, '');
		alert("PHP Library path is incorrect or PHP library is not found.\n\najax, Cookies, Sessions, XDomain and Crypto javascript functions may not be available.\n\nIf this is intentional set\nJL_setAjax \nto false in the file jerrysLibrary.js\n\n002");
	}else{
		JL_crystart= 0;
		JL_cryptcomm( 'Unencrypted Communication Established', 10, 'blink');		
	}
}

function JL_randomFill( safe, n){
	var out= '';
	for (var i=0; i<n; i++){
		out+= safe.charAt( Math.floor( Math.random() * safe.length));
	}
	return out;
}

function JL_RC4crypt( str, key){
	var clen= 128;
	var s= [];
	var j= 0;
	var x= 0;
	var res= '';
	for (var i= 0; i<clen; i++){
		s[i]= i;
	}
	for (i= 0; i<clen; i++) {
		j= (j + s[i] + key.charCodeAt(i % key.length)) % clen;
		x= s[i];
		s[i]= s[j];
		s[j]= x;
	}
	i= 0;
	j= 0;
	for (var y= 0; y<str.length; y++) {
		i= (i + 1) % clen;
		j= (j + s[i]) % clen;
		x= s[i];
		s[i]= s[j];
		s[j]= x;
		res+= String.fromCharCode(str.charCodeAt(y) ^ s[(s[i] + s[j]) % clen]);
	}
	return res;
}

function JL_cryptcomm( mess, time, whirlBlinkCount ){	
	clearTimeout( JL_crytime);
	if ( !JL_crycomm ){ 
		try {
			ge('securecomm').innerHTML= img;
			ge('securecomm').style.top= '-55px';
			JL_crycomm= true;
		}catch(e){
			try {
				ge('secureComIcon').innerHTML= '<div id="securecommimg" style="position:fixed; top:-5px; left:-5px; width:30px; height:20px; text-align:right; overflow:hidden; border-radius:5px; background:#eee; padding:5px; font-family:tahoma; font-size:16px; color:grey;"></div><div id="securecomm" style="position:fixed; top:-30px; left:30px; height:20px; overflow:hidden; border-radius:5px; background:#ddd; padding:5px; font-family:tahoma; font-size:16px; color:grey;"></div>';
				ge('securecomm').innerHTML= img;
				ge('securecomm').style.top= '-55px';
				JL_crycomm= true;
			}catch(e){
				JL_crycomm= false;
			}
			JL_crycomm= false;
		}
	}
	if ( JL_crycomm ){  
		if ( trim( mess, '').length > 0){
			var m= '';
			if ( whirlBlinkCount.toLowerCase().indexOf('whirl') > -1){
				m= '<img src="http://jerrywickey.com/israel/images/whirl.gif" width="16" height="16" border="0" valign="bottom"> ';
			}
			m+= '<span id="cryptocount" style="color: red; font-size: 0.7em;"></span> ';					
			ge('securecomm').style.top= '-5px';
			ge('securecomm').innerHTML= m + mess;
			var img= '<img style="width:25px; height:20px; margin-top: 3px;"  onmouseover="ms(2);JL_cryptcomm(\' \',4,\'\')" onmouseout="ms(1)" onclick="JL_cryptcomm(\' \',4,\'\');if(confirm(\'';
			if ( JL_cryActive){
				img+= 'Encryption key\\n\\n'+JL_rc4keys +'\\n\\n'+ JL_crybits+'-bit Secure connection. Click Ok to refresh key\')){initCrypto(JL_crybits)}" src="http://jerrywickey.com/images/lockred.gif">';
			}else{
				img+= 'Connection is unlocked, plain text. Click Ok to establish a secure connection\')){initCrypto(JL_crybits)}" src="http://jerrywickey.com/images/unlockred.gif">';
			}
			ge('securecommimg').innerHTML= img;
		}
		if ( mess.length > 0){
			ge('securecomm').style.top= '-5px';
		}				
		if ( whirlBlinkCount.toLowerCase().indexOf('count') > -1 ){
			try{
				ge('cryptocount').innerHTML= Math.round( time );
			} catch(e){}
		}
		if ( whirlBlinkCount.toLowerCase().indexOf('blink') > -1 && parseInt( time * 20) % 10 == 0 ){
			var b= '#ddd';
			if (parseInt( time * 20) % 20 == 0){ b= '#eee'; }
			ge('securecomm').style.background= b;
		}
		if ( time < 0.3){
			ge('securecomm').style.top= parseInt(( time / 0.3 * 25) - 30)+'px';
		}
		if ( time <= 0){
			//ge('securecomm').innerHTML= '';
		}			
		if ( time > 0){
			time= time - 0.05;
			var e= 'JL_cryptcomm( "", '+time+', "'+whirlBlinkCount+'")';
			JL_crytime= setTimeout( e, 50);
		}
	}
}

function JL_cachefix( a){
	a+= a.indexOf( '?')>=0 ? '&' : '?';
	return a+'pereventCaching='+( Math.floor( Math.random() * 99999999));
}

function JL_browserspec(){
	if ( window.XMLHttpRequest){
		return new XMLHttpRequest();
	}else{
		return new ActiveXObject( "Microsoft.XMLHTTP");
	}
}


// BC Math Library ===============================================================

function JL_bccomp( a, b){
	if (a.length > b.length){ return 1; }	
	if (a.length < b.length){ return -1; }
	var i= 0; while ( a.charAt(i)==b.charAt(i) && ++i<a.length){ }
	if ( i==a.length){ return 0; }
	if ( parseInt( a.charAt(i)) > parseInt( b.charAt(i))){ return 1; }	
	return -1;		
}

function JL_bcadd( a, b){
	var zero= '00000000000000000000'; while ( zero.length < a.length + b.length){ zero+= ''+zero; } 
	if ( a.length < b.length){ a= ''+ zero.substring( 0, ( b.length - a.length )) + a; }
	if ( b.length < a.length){ b= ''+ zero.substring( 0, ( a.length - b.length )) + b; }
	var s= ('0'+a).split('');
	var t= 0;
	for (var i=0; i<a.length; i++){
		t= parseInt( s[s.length-i-1]) + parseInt( b.charAt( b.length-i-1));;
		if (t > 9){
			s[s.length-i-1]= t - 10;
			s[s.length-i-2]= parseInt( s[s.length-i-2]) + 1;
		}else{
			s[s.length-i-1]= t;
		}		
	}
	return trim( trim(( s.join('')+' '), '0'), '');
}

function JL_bcsub( a, b){
	var x= JL_bccomp( a, b);
	if ( x==0){
		return '0';
	}
	var minus= '';
	if ( x < 0){
		var x= a;
		a= b;
		b= x;
		minus= '-';
	}
	var s= a.split('');
	var t= 0;
	for (var i=0; i<s.length; i++){
		t= parseInt(s[s.length-i-1]);
		if ( i<b.length){ t= t - parseInt( b.charAt( b.length-i-1)); }
		if ( t<0){
			s[s.length-i-1]= t + 10;
			s[s.length-i-2]= s[s.length-i-2] - 1;
		}else{
			s[s.length-i-1]= parseInt( t);
		}		
	}
	return minus + trim( trim(( s.join('')+' '), '0'), '');
}

function JL_bcmul( a, b){
	var s= [];
	for (var i=0; i < a.length + b.length; i++){ s[i]= 0; }
	var t= 0;
	for (i=0; i<b.length; i++){	
		for (var j=0; j<a.length; j++){
			t= s[i+j] + ( parseInt( a.charAt( a.length - j - 1)) * parseInt( b.charAt( b.length - i - 1))); 
			s[i+j]= t % 10;
			s[i+j+1]= s[i+j+1] + Math.floor( t / 10);
		}
	}
	s.reverse();
	return trim( trim(( s.join('')+' '), '0'), '');
}

function JL_bcdiv( a, b){
	var r= '0';
	var rr= '1';
	var e= b;
	var rrs= [];
	var es= [];
	var i= 0;
	while( JL_bccomp( a, b) >= 0){
		rr= '1';
		e= b;
		i= 0;
		while( JL_bccomp( a, e) >= 0){
			a= JL_bcsub( a, e);
			r= JL_bcadd( r, rr);
			if ( typeof es[i] == 'undefined'){
				es[i]= JL_bcmul( e, '2');
				rrs[i]= JL_bcmul( rr, '2');
			}
			e= es[i];
			rr= rrs[i];
			i++;
		}
	}
	// a is the remainder
	return r;
}

function JL_bcmod( a, m){
	var s= [];
	var e= m;
	var i= 0;
	while( JL_bccomp( a, m) >= 0){
		e= m;
		i= 0;
		while( JL_bccomp( a, e) >= 0){
			a= JL_bcsub( a, e);
			if ( typeof s[i] == 'undefined'){
				s[i]= JL_bcmul( e, '2');
			} 
			e= s[i];
			i++;
		}
	}
	return a;
}
