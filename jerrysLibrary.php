<?php
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
//
//
// php scripts must include 
// include('/your_path/jerrysLibrary.php');
// change '/your_path/' to the path on your server where you placed this file


// someone could abuse your server if you allow xdomaim    
define ( 'ALLOWXDOMAIN', 'true');  // true / false


// user PHP Functions ===============================================================

function decryptFromClient( $str){
	$urlsafe= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
	$esc= '().*';
	$out= '';
	for ($i=0; $i<strlen( $str); $i++){
		if ( strpos( $urlsafe, $str[$i]) !== false){
			$out.= $str[$i];	
		}else{
			$out.= chr(( strpos( $esc, $str[$i]) * 64) + strpos( $urlsafe, $str[$i+1]));
			$i++;			
		}
	}
	session_start();
	$a= RC4crypt( $out, $_SESSION['JL_keyfilename']);
	if ( strpos( $a, 'auTheNtiCate_-') === false){
		return false; 
	}
	return substr( $a, ( strpos( $a, 'auTheNtiCate_-') + 14));
}

function encryptToClient( $str){
	$str= JL_randomFill( 'JerryWickey', ( 53- (( strlen( $str) + 14) % 53))) . 'auTheNtiCate_-' . $str;
	session_start();
	return RC4crypt( $str, $_SESSION['JL_keyfilename']);
}

function RSAencrypt( $num, $GETn){
	if ( file_exists( 'temp/bigprimes'.hash( 'sha256', $GETn).'.php')){
		$t= explode( '>,', file_get_contents('temp/bigprimes'.hash( 'sha256', $GETn).'.php'));
		return JL_powmod( $num, $t[4], $t[10]);	
	}else{
		return false;
	}
}

function RSAdecrypt( $num, $GETn){
	if ( file_exists( 'temp/bigprimes'.hash( 'sha256', $GETn).'.php')){
		$t= explode( '>,', file_get_contents('temp/bigprimes'.hash( 'sha256', $GETn).'.php'));
		return JL_powmod( $num, $t[8], $t[10]);		
	}else{
		return false;
	}
}

function RC4crypt( $str, $GETn){
	$res= 'no keys';
	$key= '';
	if ( file_exists( 'temp/bigprimes'.hash( 'sha256', $GETn).'.php')){
		$t= explode( '>,', file_get_contents('temp/bigprimes'.hash( 'sha256', $GETn).'.php'));
		$key= ''.$t[12];
		$clen= 128;
		$s= array();
		$j= 0;
		$x= 0;
		$res= '';
		for ($i=0; $i<$clen; $i++){
			$s[$i]= $i;
		}
		for ($i=0; $i<$clen; $i++){
			$j= ($j + $s[$i] + ord($key[$i % strlen($key)])) % $clen;
			$x= $s[$i];
			$s[$i]= $s[$j];
			$s[$j]= $x;
		}
		$i= 0;
		$j= 0;
		for ($y=0; $y<strlen($str); $y++){
			$i= ($i + 1) % $clen;
			$j= ($j + $s[$i]) % $clen;
			$x= $s[$i];
			$s[$i]= $s[$j];
			$s[$j]= $x;
			$res.= chr(ord($str[$y]) ^ $s[($s[$i] + $s[$j]) % $clen]);
		}
	}
	return $res;
}


// Crypto GET Functions =========================================================

if (isset($_GET['newchannel'])){
	if ( !is_dir( 'temp')){
		mkdir( 'temp');
	}else{
		$d= opendir( 'temp');
		while ( $n= readdir( $d)){
			if ( strpos( $n, 'igprimes')==1 && (filemtime( 'temp/'.$n) + (10*3600)) < time() ){
				$f= '00000000';
				while ( strlen( $f) < filesize( 'temp/'.$n)){
					$f.= $f;
				}
				file_put_contents(( 'temp/'.$n), $f);
				unlink( 'temp/'.$n);
			}
		}
		closedir( $d);
	}
	$JL_primes= array();
	$do= true;		
	if ( file_exists( 'temp/primes.txt')){
		$JL_primes= explode( ',', file_get_contents( 'temp/primes.txt'));
		if ( $JL_primes[2261] == 19997){
			$do= false;
		}
	}
	if ( $do){
		$JL_primes= array();
		$numbers= array();
		for ( $i= 0; $i<20000; $i++){
			$numbers[$i]= $i;
		}
		$numbers[0]= $numbers[1]= 0; 
		foreach ( $numbers as $i=>$num){
			if( !$num) {
				continue;
			}
			$j= $i;		
			for ( $j+= $i; $j<20000; $j+= $i){
				$numbers[$j]= 0;
			}
		}
		$i= 0;
		foreach( $numbers as $num){
			if ( $num){
				$JL_primes[$i]= $num;
				$i++;
			}
		}
		file_put_contents( 'temp/primes.txt', implode( ',', $JL_primes));
	}
	$bits= $_GET['newchannel'];
	$name= hash( 'sha256', $_GET['n']);
	$t= intval( $_GET['t']);
	if ( $t<250){ $t= 250; }
	if ( $t>1000){ $t= 1000; }
	$size= floor( 1000 * pow( 1/$t, 0.4));
	$digits= floor(( $size * 0.3) - 9 + mt_rand( 0, 7));
	while ( $digits >= ( $size * 0.3) - 3){ $digits--; }
	if ( file_exists( 'temp/bigprimes'.$name.'.php')){
		$f= file_get_contents( 'temp/bigprimes'.$name.'.php');
		if ( strpos( $f, '>,r>,') !== false){
			$keys = explode( '>,', $f);
			echo 'public key,'.$keys[4].','.$keys[10].','.$keys[6].','.$digits.',';
			exit();
		}
	}
	if ( JL_threeLargePrimes( $size, $name, $_GET['n'])){ 
		$f= file_get_contents( 'temp/bigprimes'.$name.'.php');
		if ( strpos( $f, '>,r>,') !== false){
			$keys = explode( '>,', $f);
			echo 'public key,'.$keys[4].','.$keys[10].','.$keys[6].','.$digits.',';
			exit();
		}
	}
	echo 'not found,,,,';
	exit();
}

if (isset($_GET['setkey'])){
	$rc4key= RSAdecrypt( $_GET['setkey'], $_GET['n']);
	$name= hash( 'sha256', $_GET['n']);
	if ( file_exists( 'temp/bigprimes'.$name.'.php')){
		if ( strpos( file_get_contents( 'temp/bigprimes'.$name.'.php'), '>,rc4>,') === false){
			file_put_contents( ('temp/bigprimes'.$name.'.php'), ($rc4key . '>,rc4>,'), FILE_APPEND);
			$t= explode( '>,', file_get_contents( 'temp/bigprimes'.$name.'.php'));
			$t[12]= decryptFromClient( $_GET['b']);
			file_put_contents( ('temp/bigprimes'.$name.'.php'), implode( '>,', $t));
			echo encryptToClient( 'encryption_secured');
			exit();
		}
	}
	echo 'no,,,,';	
	exit();
}


// AJAX GET functions ===========================================================

// Handshake to validate session use 
if ( isset( $_GET['serverhandshake'])){
	session_start(); 
	$_SESSION['JL_ses']= $_GET['serverhandshake']; 
	echo 'pollo Jerry\'s library Jan 8, 2014';
	exit();
}

if ( isset( $_GET['setcky'])){
	$t= intval( $_GET['t']);
	if ($t==0){ $time= time()-(3600*24*30); }else{ $time= time()+$t; }
	$path= '/';
	if ( strlen( $_GET['p'])>0){ $path= $_GET['p']; } 
	$domain= '';
	if ( strlen( $_GET['d'])>0){ $domain= $_GET['d']; } 
	setcookie( $_GET['setcky'], $_GET['v'], $time, $path, $domain); 
	echo 'ok';
	exit();
}
	
if ( isset( $_GET['getcky'])){
	echo $_COOKIE[ $_GET['getcky']];
	exit();
}
	
if ( isset($_GET['setses'])){
	if ( $_GET['setses']=='JL_ses'){ exit(); }
	session_start(); 
	$_SESSION[$_GET['setses']]= $_GET['v']; 
	echo 'ok';
	exit();
}

if ( isset( $_GET['getses'])){
	session_start(); 
	echo $_SESSION[$_GET['getses']];
	exit();
}

if ( isset( $_GET['xdomain'])){
	if ( strtolower( ALLOWXDOMAIN) != 'true'){
		echo 'false';
		exit();
	}		
	if ( $_GET['xdomain']=='noaccess'){
		file_put_contents( 'xdomain.txt', 'no');
	}
	if ( file_exists( 'xdomain.txt')){
		if ( stripos( file_get_contents( 'xdomain.txt'), 'no') !== false){
			echo 'false';
			exit();
		}
	}
	$agent= ''; if ( strlen( $_GET['a'])>0){ $agent= $_GET['a']; } 
	$timeout= ''; if ( strlen( $_GET['t'])>0){ $timeout= $_GET['t']; } 
	$cookiejar= ''; if ( strlen( $_GET['c'])>0){ $cookiejar= $_GET['c']; } 
	$post= ''; if ( strlen( trim( $_POST['p']))>0){ $post= $_POST['p']; } 
	echo JL_geturl($_GET['xdomain'], $agent, $timeout, $post, $cookiejar);
	exit();
}


// internal php Functions ===========================================================

function JL_threeLargePrimes($bits, $name, $GETn){
 	$v= rand( 2,6);
 	if ( !file_exists( 'temp/bigprimes'.$name.'.php')){
		$u= JL_make_prime( ceil( $bits / 2) + $v);
		if ( $u != 'not found'){
			$php= '<'.'?php $a= basename($_SERVER[\'SCRIPT_NAME\']); $b= filesize($a); $c=\'\'; while( strlen( $c) < $b){ $c.= \'0000000000000\'; } file_put_contents( $a, $c); unlink( $a); exit(); ?';
			if ( !file_exists( 'temp/bigprimes'.$name.'.php')){
				file_put_contents(( 'temp/bigprimes'.$name.'.php'), ( $php.'>,'.$u.'>,'));
			}				
		}
		return false;
	}else{
		$f= file_get_contents( 'temp/bigprimes'.$name.'.php');
		if (strpos( $f, '>,v>,')===false){ 
			$temp= explode( '>,', $f);
			$u= $temp[1]; 
			$v= JL_make_prime( floor( $bits / 2) - $v);
			if ( $v=='not found' || substr( $u, -16, 2) < (substr( $v, -16, 2) + 2) && substr( $u, -16, 2) > (substr( $v, -16, 2) - 2) ) {
				return false;
			}
			if ( strpos( file_get_contents('temp/bigprimes'.$name.'.php'), '>,v>,')===false ){
				file_put_contents(( 'temp/bigprimes'.$name.'.php'), ($v.'>,v>,'), FILE_APPEND);
			}
			return false;
		} 
		if ( strpos( $f, '>,r>,')===false){ 
			$psize = ($bits > 51) ? intval($bits/3) : 17;
			$p= JL_make_prime( $psize);;
			if ($p!='not fount' && strpos( file_get_contents('temp/bigprimes'.$name.'.php'), '>,r>,')===false){
				$t= explode( '>,', $f);
				$u= $t[1];
				$v= $t[2];
				$p= $p;
				$b= $bits;
				$r = bcmul($u, $v);
				$q= JL_euclid( $p, bcmul( bcsub( $u, 1), bcsub( $v, 1)));
				file_put_contents(('temp/bigprimes'.$name.'.php'), ($p.'>,p>,'.$bits.'>,b>,'.$q.'>,q>,'.$r.'>,r>,'), FILE_APPEND);
				// php, u, v,, p,, b bits,, q private,, r ,, rc4key,,
				// 0    1  2   4   6        8           10   12
				// public key is  $t[4] $t[10] $t[6] 
				// private key is $t[8] $t[10] $t[6]
				session_start();
				$_SESSION['JL_keyfilename']= $GETn;
			}else{
				return false;
			}
		}
	}
	return true;
}

function JL_make_prime( $bits){
	$d= strlen( bcmul( bcpow( 2, $bits), 2));
	$m= microtime( true); 
	$b= array(1,3,7,9);
	$n= '';
	$p= false;
	while ( $m + 4.5 > microtime( true) && !$p){
		$t= microtime( true);
		$n= ''; 
		while ( strlen( $n) < $d-2 ){
			$n.= mt_rand( 0,9); }
		str_shuffle( $n );
		$n= mt_rand( 1,9) . $n . $b[mt_rand( 0,3)];
		while ( $t + 1.1 > microtime( true) && !$p ){
			if( substr( $n, -1, 1) == '3'){
				$n= bcadd( $n, 4);
			}else{
				$n= bcadd( $n, 2);
			}
			$p= JL_is_prime( $n);
		}
	}
	if ( $p){
		return $n; 	
	}else{
		return 'not found';
	}
}
		
function JL_is_prime( $num) {
	global $JL_primes;
	if ( bccomp( $num, 1)==0){ return false; }
	foreach( $JL_primes as $prime) {
		if ( bccomp( $num, $prime) == 0){ return true; }
		if ( ! bcmod( $num, $prime)){ return false; }
	}
	for($i = 0; $i < 7; $i++) {
		if ( !JL_millerTest($num, $JL_primes[$i])){ return false; }
	}
	return true;
}
	
function JL_millerTest( $num, $base) {
	$tmp= bcsub( $num, '1');
	$zero_bits= 0;
	while ( !bccomp( bcmod( $tmp, '2'), '0')) {
		$zero_bits++;
		$tmp= bcdiv( $tmp, '2');
	}
	$tmp= JL_powmod( $base, $tmp, $num);
	if ( !bccomp( $tmp, '1')) { return true; }
	while ( $zero_bits--) {
		if ( !bccomp( bcadd( $tmp, '1'), $num)) { return true; }
		$tmp = JL_powmod( $tmp, '2', $num);
	}
	return false;
}

function JL_euclid( $num, $mod){
	$x= '1';
	$y= '0';
	$num1= $mod;
	do {
		$tmp= bcmod( $num, $num1);
		$q= bcdiv( $num, $num1);
		$num= $num1;
		$num1= $tmp;
			$tmp= bcsub( $x, bcmul( $y, $q));
		$x= $y;
		$y= $tmp;
	} while ( bccomp( $num1, '0'));
	if ( bccomp( $x, '0') < 0) {
		$x= bcadd( $x, $mod);
	}
	return $x;
}

function JL_powmod( $num, $pow, $mod) {
	if ( function_exists('bcpowmod')) {
		return bcpowmod( $num, $pow, $mod);
	}
	$result= '1';
	do {
		if ( !bccomp( bcmod( $pow, '2'), '1')) {
			$result = bcmod( bcmul( $result, $num), $mod);
		}
	   $num = bcmod( bcpow( $num, '2'), $mod);

	   $pow = bcdiv( $pow, '2');
	} while ( bccomp( $pow, '0'));
	return $result;
}

function JL_randomFill( $safe, $n){
	$out= '';
	for ( $i=0; $i<$n; $i++){
		$out.= $safe[ mt_rand( 0, strlen( $safe) -1)];
	}
	return $out;
}

function JL_geturl( $url, $agent, $timeout, $post, $cookiejar){
	$page= false;
	if ( strlen( $url)>10 && strtolower( substr( $url, 0, 7))=='http://'){
		$a= ' (Jerrybot; http://www.botproject.jerrywickey.net/ ) ';
		if ( strlen( $agent)<5){ $agent= $a;}
		if ( strlen( $timeout)<1){ $timeout= 6;}
		if ( strlen( $post)<5){ $post= false;}
		if ( strlen( $cookiejar)<5){ $cookiejar= false;}
		$curl= curl_init();
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt( $curl, CURLOPT_MAXREDIRS, 3);
		curl_setopt( $curl, CURLOPT_URL, $url);
		curl_setopt( $curl, CURLOPT_USERAGENT, $agent);  
		curl_setopt( $curl, CURLOPT_TIMEOUT, ($timeout+1));
		curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, $timeout);		
		if ( $post!=false){
			curl_setopt( $curl, CURLOPT_POST, true);
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $post);
		}
		if ( $cookiejar!=false){
			curl_setopt( $curl, CURLOPT_COOKIEJAR, $cookiejar);
		}
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true);	
		$page= curl_exec( $curl); 
		$error= curl_error( $curl);
		curl_close( $curl);
		if ( strlen( $error)>0){ $page= 'ERROR [' . $error . '] ' . $page;}
	}
	return $page;
}
