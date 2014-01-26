<?php
if (isset($_GET['testrc4'])){
	include('jerrysLibrary.php');

	$t= explode( '>,', file_get_contents('temp/bigprimes'.hash( 'sha256', $_GET['n']).'.php'));
	$key= $t[12];

	echo 'file= '.$_GET['n'].' '.hash('sha256',$_GET['n']).'<br>';
	echo str_replace('<','&lt;', file_get_contents( 'temp/bigprimes'.hash('sha256',$_GET['n']).'.php')).'<br><br>';;

	echo '<br>received from client= '.$_GET['testrc4'];	
	$t= RC4crypt( $_GET['testrc4'], $_GET['n']);
	echo "<br>de= ".$t;
	echo '<br>en= '.RC4crypt( $t, $_GET['n']); 
	echo '<br>re= '.RC4crypt( RC4crypt( $t, $_GET['n']), $_GET['n']); 
	echo '<br>server= '.RC4crypt( RC4crypt( 'bee bop and ho', $_GET['n']), $_GET['n']).'<br>'; 
	echo '<br>clietn= '.
	echo 'ext='.RC4crypt( 'decode this', $_GET['n']);
	exit();
}
decryptFromClient( $str)
encryptToClient( $str)


?>
<html>
<head>
<script src="jerrysLibrary.js"></script>
<script>
function cryptoUnavalable(bits, serverKeyFileName, clientKey){
	ge('test').innerHTML+= 'NOOOO <br><br>';
	cryptoStarted( bits, serverKeyFileName, clientKey);
}
function cryptoStarted( bits, serverKeyFileName, clientKey){
	ge('test').innerHTML+= (bits+' '+serverKeyFileName+' '+clientKey)+'<br><br>';
	
	var send= 'encrypted internally';
	var sendc= JL_RC4crypt( send, JL_rc4keys, 'test');
	var sendpp= 'encrypted user';
	var sendp= encryptToServer( sendpp);
	
	ge('test').innerHTML+= 'file= '+JL_cryname+'<br>bits= '+JL_crybits+'<br>key= '+	JL_rc4keys+' '+JL_rc4keys.length+'<br>enkey= '+JL_enckeys+'<br>k1= '+	JL_rsakey1+'<br>k2= '+	JL_rsakey2+'<br>'+JL_RC4crypt( sendc, JL_rc4keys, 'test')+'<br>en= '+sendc+ '<br>user= '+sendpp+ '<br>en= '+sendp;
;
	multihttp(('?testrc4='+encodeURIComponent(sendc)+'&n='+JL_cryname+'&p='+sendp), '', 'finishtest');
}
function finishtest(a){
	var b= unescape(a)+'<br><br>';
	//ge('test').innerHTML+= '<br><br>'+a+'<br><br>';
	ge('test').innerHTML+= '<br><br>'+b+'<br><br>';
	var t= b.substring(b.indexOf('ext=')+4);
	ge('test').innerHTML+= 'from server: '+decryptFromServer( t);
}	
</script>
</head>

<body onload="initCrypto( 100)">

<span onclick="ge('test').innerHTML='';initCrypto(ge('pp').value)">initCrypto</span>( <input type="text" id="pp"> )<br><br>

<div id="test1"></div>
<div id="test"></div>

Had to rewrite the prime number generator to get it to work.<br><br>

My first rewrite which I posted last week was hasty.<br><br>

you can look at it.<br><br>

To get things to work on the javascript client I looked for a bcmath pachage for javascript.  Fouhnd one but ended up completely rewriting it.  By ingoring negative numbers, floating point and validaiton of the arguments I got it to work better than twice as fast as the pachage I looked at .<br><br>

its all here<br><br>

<div id="securecomm" style="position:fixed; top:-30px; right:20px; height:20px; overflow:hidden; border-radius:5px; background: ; padding:5px; font-family:tahoma; font-size:16px; color:grey;"></div><div style="position:fixed; top: 0px;right: 0px;width: 300px; height:5px;" onmouseover="JL_cryptcomm(' ',3,'')">&nbsp;</div>
</body>
</html>
