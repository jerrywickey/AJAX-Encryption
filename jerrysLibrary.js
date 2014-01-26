// Jerry's Crypto-Ajax JavaScript and PHP Library
// Jan 8, 2014   Key West, FL US   
// jerry @-symbol jerrywickey dot-symbol-. com   
// phone - eight hundred - seven two two - two two eight zero

// thanks to Stevish RSA http://stevish.com/rsa-encryption-in-pure-php 
// thanks to Ali Farhadi RC4 https://gist.github.com/farhadi/2185197


//  html javascript pages must include in HTML header 

// 	  {script src="your_path/jerrysLibrary.js">{/script>

//  change both '{' to less than signs 
//  change 'path' to the directory on your server to which you saved this file


//  Use the following in body onload, if encryption is desired

// 	  {body onload="initCrypto( 1024 )" style="your-style">

//  set 1024 to any number of bits destired.  max 2048
//  when an encrypted channel is secured the function 

//    cryptoStarted( JL_crybits, JL_cryname, JL_rc4keys) 

//  will be called, if it exists.  You write it if you wish to do something with 
//  it's three parameters.  number of bits, name of encryption file on server, and 
//  the public encryption keys.   This is merely for your information.  It is optional.
//  You don't need to keep track of thse things.  The two encryption functions below 
//  keep and use this data automatically.

//    cryptoUnavalable( JL_crybits, JL_cryname, JL_rc4keys)  is called if unavailable

//    initCrypto( number_bits )  could be called again from this function


//  The only encryption functions you need are these, 
//  which work after onload="initCrypto(

//    encryptToServer( str)              decryptFromServer( str)


//  and optionally crypto communication status appears in th <div below, if it exits.  

//    <div id="securecomm" style="position:fixed; top:-30px; right:20px; height:20px; overflow:hidden; border-radius:5px; background:#ddd; padding:5px; font-family:tahoma; font-size:16px; color:grey;"></div><div style="position:fixed; top: 0px;right: 0px;width: 300px; height:5px;" onmouseover="JL_cryptcomm(' ',3,'')">&nbsp;</div>


//  path to jerrysLibrary.php  domain and subdirectory
//  set the directory path here.  Example   + '/your-path/';
//  the setting below presumes you've saved the PHP library file 
//  to the 'test/' directory on your server. 
var PHPlibpath= 'http://'+window.location.hostname + '/test/jerrysLibrary.php'; 


//  if PHP encryption or AJAX is not desired set JL_setAjax to false
//  miscellaneous functions are still availble
var JL_setAjax= true;


var JL_crybits= 512;
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
if ( JL_setAjax){
	multihttp(( PHPlibpath + '?serverhandshake=marko'), '', 'JL_enableAXCCStrue');
	JL_crystart= 10;
	JL_cryptcomm( 'Checking Communication Status', 10, 'whirl,blink');		
}


// User Crypto Functions =========================================================

function initCrypto( a){
	if ( parseInt( a) > 0){
		JL_crybits= a;
	}
	JL_crybits= parseInt( JL_crybits);	
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
		var l= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		for ( var i=0; i<10; i++){
			JL_cryname+= l.charAt( parseInt( Math.random() * l.length));
		}
		var n= 1;
		if (JL_crybits<50){ JL_crybits= 50; }
		if (JL_crybits>200){ n= 2; }
		if (JL_crybits>500){ n= 3; }
		if (JL_crybits>800){ n= 10; }
		if (JL_crybits>1200){ n= 20; }
		if (JL_crybits>1600){ n= 40; }
		if (JL_crybits>2000){ n= 60; }
		if (JL_crybits>2048){ JL_crybits= 2048; }
		var d1= new Date(); 
		for (i=0; i<3; i++){ 
			var t= JL_bcdiv( '123456789012345678901234567890', '12'); 
		} 
		var d2= new Date();
		JL_speedts= ''+( d2.getTime() - d1.getTime()); 
		for (i=0; i<n; i++){
			multihttp(( PHPlibpath+'?newchannel='+JL_crybits+'&n='+JL_cryname+'&t='+JL_speedts), '', 'JL_initCryptoStep2')
		}
		JL_cryptcomm( 'Securing Encrypted Channel', 60, 'whirl,blink,count');
	}
}

function encryptToServer( str){
	JL_cryptcomm( '<span style="color:red">Encrypted Uplink</span>', 6, '');
	var VA= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
	var VE= '!#$*';
	var out= '';
	for (var i=0; i<str.length; i++){
		if ( VA.indexOf( str.charAt(i)) > -1){
			out+= str.charAt(i);
		}else{
			out+= VE.charAt( Math.floor( str.charCodeAt(i) / 64));
			out+= VA.charAt( str.charCodeAt(i) % 64);		
		}
	}
	out= '_-'+out;
	while ( out.length < 53){
		out= '_________'+out;
	}
	while ( out.length % 53 > 10 ){
		out= '_________'+out;
	}		
	return JL_RC4crypt( out, JL_rc4keys, '');
}

function decryptFromServer( str){
	JL_cryptcomm( '<span style="color:green">Decrypted Downlink</span>', 4, '');
	var t= JL_RC4crypt( str, JL_rc4keys, '');
	return t.substring( t.indexOf( '_-') + 2);
}


// User AJAX Functions 
// Cookie, Session and XDomain functions =========================================

function setcookie( aname, avalue, time, path, adomain){
// sets a cookie	
	return synchttp(( PHPlibpath+'?setcky='
		+encodeURIComponent( aname)
		+'&v='+ encodeURIComponent( avalue)
		+'&t='+ encodeURIComponent( time)
		+'&p='+ encodeURIComponent( path)
		+'&d='+ encodeURIComponent( adomain)), '');	
}

function getcookie( aname){
// returns the value of a cookie
	return synchttp(( PHPlibpath+'?getcky='+encodeURIComponent( aname)), '');
}

function setsession( aname, avalue){
// sets a session variable on your server
	return synchttp(( PHPlibpath+'?setses='+encodeURIComponent( aname)+'&v='+encodeURIComponent( avalue)), '');
}

function getsession( aname){
// returns a session variable value
	return synchttp(( PHPlibpath+'?getses='+encodeURIComponent( aname)), '');
}

function Xdomain( aurl, post, cookiejar, agent, timeout){
// returns the source code for any url
	return synchttp(( PHPlibpath+'?xdomain='+encodeURIComponent( aurl)
		+'&a='+encodeURIComponent( agent)+'&t='+encodeURIComponent( timeout)
		+'&c='+encodeURIComponent( cookiejar)), 
		post);
}

function multiXdomain( aurl, post, cookiejar, agent, timeout, response){ 
// returns the source code for any url asynchronously.
// that is that it sends the result to the function named in 'response'
		multihttp(( PHPlibpath+'?xdomain='+encodeURIComponent( aurl)
		+'&a='+encodeURIComponent( agent)
		+'&t='+encodeURIComponent( timeout)
		+'&c='+encodeURIComponent( cookiejar)), 
		post, response);
}

function synchttp( where, post){
// returns the output of a page on your server
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
// returns the output of a page on your server asynchronously 
// that is that it sends the result to the function named in 'dowith'
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

function ge(a){  // a short cut for document.getElementById()  
	return document.getElementById(a);
}

function ms(a){  // a short cut for document.body.style.cursor=
	if (a==1){a= 'default';}
	if (a==2){a= 'pointer';}
	document.body.style.cursor= a; 
}

function getypos(obj){  // returns the verticle position in pixels of an element
	var y= 0
	while(obj){
		y= y+obj.offsetTop;
		obj= obj.offsetParent;
	}
	return y;
}

function getxpos(obj){  // returns the horizontal position in pixels of an element
	var x= 0
	while(obj){
		x= x+obj.offsetLeft;
		obj= obj.offsetParent;
	}
	return x;
}

function ekey(a){  // returns true if the enter key is pressed
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

function trim( txt, ofthese){  // trim function that works in all broswers
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
// replaceall that avoids the limitations of regular expression
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
				multihttp(( PHPlibpath+'?newchannel='+JL_crybits+'&n='+JL_cryname+'&t='+JL_speedts), '', 'JL_initCryptoStep2');	
			}else{
				JL_setAjax= false;
				alert("PHP Library path is incorrect or PHP library is not found.\n\najax, Cookies, Sessions, XDomain and Crypto javascript functions may not be available.\n\nIf this is intentional set\nJL_setAjax \nto false in the file jerrysLibrary.js\n\n001");		
			}
		}
	}else{
		JL_cryptcomm( 'Secure channel canceled', 3, '');				
	}
}

function JL_initCryptoStep3( k){
	JL_enckeys= unescape( k);
	multihttp(( PHPlibpath+'?setkey='+ JL_enckeys+'&n='+ JL_cryname), '', 'JL_initCryptoStep4');
}

function JL_initCryptoStep4( a){
	if ( JL_RC4crypt( unescape( a), JL_rc4keys, 'test')=='encryption_secured'){	
		JL_cryptcomm( (JL_crybits+'-bit Secure Channel Established'), 8, 'blink');
		JL_cryActive= true;
		try{
			cryptoStarted( JL_crybits, JL_cryname, JL_rc4keys);
		}catch(e){}
	}else{
		if ( confirm( "A secure channel was not achieved\b\nTry again?")==true ){
			setTimeout( ("initCrypto( "+ JL_crybits+ ")"), 500);	
		}else{
			JL_cryptcomm( 'Secure channel canceled', 3, '');		
		}
		cryptoUnavalable( JL_crybits, JL_cryname, JL_rc4keys);
	}
}

function JL_RSAencrypt( str, a, b, callback){
	JL_cryptcomm( 'Setting Session Keys', 60, 'Whirl,Count' );
	JL_RSAencryptStep( str, a, b, callback, '1', 0);
}

function JL_RSAencryptStep( str, a, b, callback, result, count){	
	count++;
	if ( JL_bccomp( JL_bcmod( a, '2'), '1')==0) {
		result = JL_bcmod( JL_bcmul( result, str), b);
	}
	str= JL_bcmod( JL_bcmul( str, str), b);
	a= JL_bcdiv( a, '2');
	if ( JL_bccomp( a, '0')!=0){
		var e= "JL_RSAencryptStep('" +str+"','" +a+"','" +b+"','" +callback +"','" +result+"','" +count+"')";
		setTimeout( e, 10);	
		clearTimeout( JL_crytime);
		try{
			ge('cryptocount').innerHTML= count;
		}catch(e){}
	}else{
		eval( callback+'("'+ escape( result)+'")' );
	}
}

function JL_enableAXCCStrue( a){
	if ( unescape( a).indexOf( 'pollo ')!==0){
		JL_setAjax= false;
		JL_cryptcomm( ' ', 0.5, '');
		alert("PHP Library path is incorrect or PHP library is not found.\n\najax, Cookies, Sessions, XDomain and Crypto javascript functions may not be available.\n\nIf this is intentional set\nJL_setAjax \nto false in the file jerrysLibrary.js\n\n002");
	}else{
		JL_crystart= 0;
		JL_cryptcomm( 'Communication Established', 10, 'blink');		
	}
}

function JL_RC4crypt( str, key, secure){
	if ( !JL_cryActive && secure!='test'){
		alert ( "A secure channel has not yet been established.\n\nWait a few seconds and try again\n\n003");
		return false;	
	}

	var validcrypt= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
//validcrypt= '';
//for (i=0; i<128; i++){ validcrypt+= String.fromCharCode(i); }	
	var s= [];
	var j= 0;
	var x= 0;
	var res= '';
	for (var i= 0; i<validcrypt.length; i++){
		s[i]= i;
	}
	for (i= 0; i<validcrypt.length; i++) {
		j= (j + s[i] + key.charCodeAt(i % key.length)) % validcrypt.length;
		//j= (j + s[i] + ( JL_crychar.indexOf( key.charAt( i % key.length)))) % JL_crychar.length;
		x= s[i];
		s[i]= s[j];
		s[j]= x;
	}
	i= 0;
	j= 0;
	for (var y= 0; y<str.length; y++) {
		i= (i + 1) % validcrypt.length;
		j= (j + s[i]) % validcrypt.length;
		x= s[i];
		s[i]= s[j];
		s[j]= x;
		res+= String.fromCharCode(str.charCodeAt(y) ^ s[(s[i] + s[j]) % validcrypt.length]);
		//res+= JL_crychar.charAt(str.charCodeAt(y) ^ s[(s[i] + s[j]) % JL_crychar.length]);
		//res+= JL_crychar.charAt( JL_crychar.indexOf( str.charAt(y)) ^ s[(s[i] + s[j]) % JL_crychar.length]);
	}
	return res;
}

function JL_cryptcomm( mess, time, whirlBlinkCount ){	
	clearTimeout( JL_crytime);
	if ( !JL_crycomm ){ 
		try {
			ge('securecomm').innerHTML= '';
			ge('securecomm').style.top= '-55px';
			JL_crycomm= true;
		}catch(e){
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
	a= (''+a);
	b= (''+b);
	if ( a.indexOf('.') > -1){
		a= a.substring( 0, a.indexOf('.'));
	}
	if ( b.indexOf('.') > -1){
		b= b.substring( 0, b.indexOf('.'));
	}
	if (a.length > b.length){ return 1; }	
	if (a.length < b.length){ return -1; }
	var i= 0;
	while ( a.charAt(i)==b.charAt(i) && ++i<a.length){ }
	if ( i==a.length){ return 0; }
	if ( parseInt( a.charAt(i)) > parseInt( b.charAt(i))){ return 1; }	
	return -1;		
}

function JL_bcadd( a, b){
	a= (''+a);
	b= (''+b);
	while ( a.length < b.length){ a= '0'+a; }
	while ( b.length < a.length){ b= '0'+b; }
	a= '0'+a;
	b= '0'+b;
	var s= a.split('');
	var t= 0;
	for (var i=0; i<s.length-1; i++){
		t= parseInt( s[s.length-i-1]) + parseInt( b.charAt( b.length-i-1));;
		if (t>9){
			s[s.length-i-1]= t - 10;
			s[s.length-i-2]= parseInt( s[s.length-i-2]) + 1;
		}else{
			s[s.length-i-1]= t;
		}		
	}
	var v= s.join('')+' ';
	v= trim( v, '0');
	return trim( v, '');
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
	a= (''+a);
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
	var v= s.join('')+' ';
	v= trim( v, '0');
	return minus + trim( v, '');
}

function JL_bcmul( a, b){
	var s= [];
	for (var i=0; i < a.length + b.length; i++){
		s[i]= 0;
	}
	var t= 0;
	for (i=0; i<b.length; i++){	
		for (var j=0; j<a.length; j++){
			t= s[i+j] + ( parseInt( a.charAt( a.length - j - 1)) * parseInt( b.charAt( b.length - i - 1))); 
			s[i+j]= t % 10;
			s[i+j+1]= s[i+j+1] + Math.floor( t / 10);
		}
	}
	s.reverse();
	var v= s.join('')+' ';
	v= trim( v, '0');
	return trim( v, '');
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
			r= JL_bcadd( r, rr);
			a= JL_bcsub( a, e);
			if ( typeof es[i] == 'undefined'){
				es[i]= JL_bcmul( e, '2');
				rrs[i]= JL_bcmul( rr, '2');
			}
			e= es[i];
			rr= rrs[i];
			i++;
		}
	}
	return r;
	// a is the remainder that is modulo
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

function JL_bcpow( a, e){
	var p2= '1';
	var pe= [];
	var bi= [];
	var p= 0;
	while ( JL_bccomp( e, p2) >= 0){
		bi[p]= p2;
		pe[p]= false;
		p2= JL_bcmul( p2, '2');
		p++;
	}	
	for (var i=p; i>=0; i--){
		if ( JL_bccomp( e, bi[i]) >= 0){
			pe[i]= true;
			e= JL_bcsub( e, bi[i]);
		}
	}	
	var res= '1';
	var ex= a;
	for (i=0; i<pe.length; i++){
		if (pe[i]){
		 	res= JL_bcmul( res, ex);	
		}
		ex= JL_bcmul( ex, ex);
	}
	return res;
}
