<?php
//echo urlencode( file_get_contents( $_SERVER['DOCUMENT_ROOT'].'/images/unlockred.gif'));
//echo ((  $_SERVER['DOCUMENT_ROOT'].'/images/unlockred.gif'));
//exit();

// Jerry's Crypto-AJAX JavaScript and PHP Library
// Feb 16, 2016   Fort Wayne, IN, US   
// email - jerry (@-symbol-) jerrywickey (.-symbol-) com   
// phone - eight hundred - seven two two - two two eight zero
//
// thanks to Stevish RSA http://stevish.com/rsa-encryption-in-pure-php 
// thanks to Ali Farhadi RC4 https://gist.github.com/farhadi/2185197
//
// This newest version and its manual can be downloaded from 
// http://jerrywickey.com/test/testJerrysLibrary2.php
//
// Use this php/js file as an HTML template 
//
// PHP configured for sessions, to include bc math and 
// for http wrap to file_get_contents and you are good to go 
// Just alter HTML to suite your application


// someone could abuse your server if you allow xdomaim    
define ( 'ALLOWXDOMAIN', 'false'); 	// true / false
define ( 'ALLOWAJAX', 'true');  	// true / false
define ( 'ENCRYPT', 2048);			// bit size of encryption set 0 for no encryption
define ( 'KEYDIR', 'temp');       	// path to crypt keys, forbidden to public .htaccess
define ( 'NK', 3);  				// number of key files for each size  512, 1024, 2048
define ( 'NUMPROC', 10);           	// number of concurent processes allowed on server
define ( 'NUMTIME', 900);           // milliseconds allowed each process
define ( 'NUMOFFS', 300);           // milliseconds to wait before starting first
$JL_primes= array();


// ========== 
//
// user AJAX php here
//
// sample

if ( isset( $_POST['encrypteddata'])){
	session_start();
	$formdata= decryptFromClient( $_POST['encrypteddata']);
	$formdata= explode( '&', trim( $formdata, '&'));
	$serverresponse= '';
	foreach( $formdata as $v){
		$nvpair= explode( '=', $v);
		if ( $nvpair[1] == 'checked' || $nvpair[1] == ''){
			$serverresponse.= '&'.$nvpair[0].'='.urlencode( $nvpair[1]);
		}else if ( $nvpair[0] == 'testtext'){
			$serverresponse.= '&testtext='.urlencode( '<span style="color: red;">Server can change other things than just forms</span>');			
		}else{
			$serverresponse.= '&'.$nvpair[0].'=Server Does something '.urlencode( $nvpair[1]);
		} 	
	}
	echo encryptToClient( $serverresponse);
	exit();
}

// ==========





// Library AJAX 

// set cookie on browser from browser
// setcookie( name, value, time, path, adomain)
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

// get cookie on browser from browser
// getcookie( name)
if ( isset( $_GET['getcky'])){
	echo $_COOKIE[ $_GET['getcky']];
	exit();
}

// set a session variable from browser	
// setsession( name, value)
if ( isset($_GET['setses'])){
	if ( $_GET['setses']=='JL_ses'){ exit(); }
	session_start(); 
	$_SESSION[$_GET['setses']]= $_GET['v']; 
	echo 'ok';
	exit();
}

// get a session variable from browser
// getsession( name)
if ( isset( $_GET['getses'])){
	session_start(); 
	echo $_SESSION[$_GET['getses']];
	exit();
}

// access other domains from browser javascript
// Xdomain( aurl, post, cookiejar, agent, timeout)
// multiXdomain( aurl, post, cookiejar, agent, timeout, response)

// turn xdomain off in many ways
// set the constant ALLOWDOMAIN false
// send a get request ?xdomain=noaccess
// place 'no' in a file xdomain.txt in the same directory on the server 
if ( isset( $_GET['xdomain'])){
	if ( strtolower( ALLOWXDOMAIN) != 'true'){
		echo 'refused';
		exit();
	}		
	if ( $_GET['xdomain']=='noaccess'){
		file_put_contents( 'xdomain.txt', 'no');
	}
	if ( file_exists( 'xdomain.txt')){
		if ( stripos( file_get_contents( 'xdomain.txt'), 'no') !== false){
			echo 'refused';
			exit();
		}
	}
	file_put_contents( 'XDomainabuse.txt', (time()."\n"), FILE_APPEND);
	$f= file_get_contents( 'XDomainabuse.txt');
	if ( trim( $f) == ''){ 
		echo 'refused';
		exit();
	}
	if ( substr_count( $f, "\n") > 3){
		echo 'refused';
		exit();
	}
	$f= exlplode( "\n", $f);
	$g= '';
	foreach ( $f as $v){	
		if ( $v > time()-60){
			$g.= $v."\n";
		}
	}
	file_put_contents( 'XDomainabuse.txt', $g);
	$agent= ''; if ( strlen( $_GET['a'])>0){ $agent= $_GET['a']; } 
	$timeout= ''; if ( strlen( $_GET['t'])>0){ $timeout= $_GET['t']; } 
	$cookiejar= ''; if ( strlen( $_GET['c'])>0){ $cookiejar= $_GET['c']; } 
	$post= ''; if ( strlen( trim( $_POST['p']))>0){ $post= $_POST['p']; } 
	echo JL_geturl($_GET['xdomain'], $agent, $timeout, $post, $cookiejar);
	exit();
}

// crytography maintance, build new keysets, maintain key set files
if ( isset( $_GET['maintaincrypt'])){
	$st= microtime( true);
	$validbits= ' 1024 2048 ';

	$keyf= array();
	$i= 0;
	$keyn= array();

	$n1024= 0;
	$n2048= 0;
	$deletelist= '';

	$dh= opendir( KEYDIR);
	// continue named key set 
	// bits=
	// bigprime=
	// smallprime=
	// modulo=
	// privatekey=
	// publickey=
	// keyset=complete
	while (( $file= readdir( $dh)) !== false){
		$ft= filemtime( KEYDIR.'/'.$file);
		$f= file_get_contents( KEYDIR.'/'.$file);
		if ( JL_getvalue( 'keyset', $f) != ''){
			// all files that have a completed keyset
			$keyf[$i]['n']= KEYDIR.'/'.$file;
			$keyf[$i]['b']= JL_getvalue( 'bits', $f);
			$keyf[$i]['t']= $ft;
			if ( $keyf[$i]['b'] == 1024){ $n1024++; }
			if ( $keyf[$i]['b'] == 2048){ $n2048++; }
echo 'key file '.$file.' bits='.$keyf[$i]['b'].'='.JL_getvalue( 'keyset',  $f).'<br>';
			$i++;

		}else if ( strpos( $validbits, (' '.JL_getvalue( 'bits', $f).' ')) !== false && $ft > time()-(24*3600)){
			// find the last file that has a bit value but not keyset  this  is not yet done
			$keyn['n']= KEYDIR.'/'.$file;
			$keyn['b']= JL_getvalue( 'bits', $f);
			$keyn['t']= $ft;
			if ( $keyn['b'] == 1024){ $n1024++; }
			if ( $keyn['b'] == 2048){ $n2048++; }
echo 'not done '.$file.' bits='.$keyn['b'].'<br>';

		}else if ( JL_getvalue( 'bits', $f) != '' && $ft < time()-(24*3600)){
			// delete anyfile that has bits, but not keyset.  It was not completed in 24 hours
			$deletelist.= KEYDIR.'/'.$file.',';
echo 'delete- '.$file.'<br>';			
			if ( JL_getvalue( 'bits', $f) == 1024){ $n1024--; }
			if ( JL_getvalue( 'bits', $f) == 2048){ $n2048--; }

		}else{
//echo 'not key '.$file.'<br>';						
		}
	}
	closedir( $dh);
	
	// count how many of each size sort by time oldest first
	// delete if there are more than wanted of that size
	$keyf= JL_sortby( $keyf, 't');
	$keyf= array_reverse( $keyf);
	$wait= file_get_contents( KEYDIR.'/working.txt');
	for ( $i=0; $i<count( $keyf); $i++){
		// delete if too many
		if ( $keyf[$i]['b'] == 1024 && $n1024 > NK){
//			$deletelist.= $keyf[$i]['n'].',';
//			$n1024--;
		}
		if ( $keyf[$i]['b'] == 2048 && $n2048 > NK){
//			$deletelist.= $keyf[$i]['n'].',';
//			$n2048--;
		}
		// delete if too old and enough
		if ( $keyf[$i]['t'] < time()-(2*24*3600) && $keyf[$i]['b'] == 1024 && $n1024 >= NK && strpos( $wait, $keyf[$i]['n']) === false){
			$deletelist.= $keyf[$i]['n'].',';
			$n1024--;
		}
		if ( $keyf[$i]['t'] < time()-(2*24*3600) && $keyf[$i]['b'] == 2048 && $n2048 >= NK && strpos( $wait, $keyf[$i]['n']) === false){
			$deletelist.= $keyf[$i]['n'].',';
			$n2048--;
		}
	}
	$del= explode( ",", trim( $deletelist, ','));
	foreach( $del as $v){
		if ( trim( $v) != ''){
			file_put_contents( $v, ('tried to delete '.$v."\n"), FILE_APPEND);
			file_put_contents( $v, JL_randomFill( '0123456789abcdef', strlen( file_get_contents( $v))));
			unlink( $v);
echo 'delete- '.$v.'<br>';			
		}
	}
	
	// make a new key file if needed
echo $n1024.' '.$n2048.' '.NK.'<br>';		
	$bits= 0;
	$kname= $keyn['n'];
	if ( $n2048 < NK){ $bits= 2048; }
	if ( $n1024 < NK){ $bits= 1024; }
echo $bits.'<br>';
	if ( strpos( $validbits, (' '.$bits.' ')) !== false){
		$kname= KEYDIR.'/RSAkeyset_'.$bits.'_'.JL_randomFill( 'JerryWickey', 1).JL_randomFill( '0123456789abcdef', 15).'.php';
		file_put_contents( $kname, ( '<'.'?php $a= basename( $_SERVER[\'SCRIPT_NAME\']); $b= filesize( $a); $c= \'0\'; while( strlen( $c) < $b){ $c.= \'0\'; } file_put_contents( $a, $c); unlink( $a); exit(); ?'.">\nbits=".$bits."\n"));
echo 'make new file '.$bits.' '.$kname.'<br>';
	}

	// work on an existing key file
	$done= true;
	if ( file_exists( $kname)){
		$key= file_get_contents( $kname);
		$np= JL_monitorserverwork( $kname);
// select encryption size to create
		$bits= JL_getvalue( 'bits', $key);
		if ( strpos( $validbits, (' '.$bits.' ')) !== false && trim( JL_getvalue( 'keyset', $key)) == ''){
echo 'work on '.$kname.' bits='.$bits.'<br>';
			if (( $pn1= JL_getvalue( 'bigprime', $key)) == ''){
				$done= false;
				$pn1= JL_make_prime( intval(( $bits /2 * log10(2)) +2));		
				if ( $pn1){ 
					file_put_contents( $kname, ('bigprime='.$pn1."\n"), FILE_APPEND);
				}
			}
			if (( $pn2= JL_getvalue( 'smallprime', $key)) == '' && $st > time()-8){
				$done= false;
				$pn2= JL_make_prime( intval(( $bits /2 * log10(2)) +1));		
				if ( $pn2){ 
					file_put_contents( $kname, ('smallprime='.$pn2."\n"), FILE_APPEND);
				}
			}
			if (( $mod= JL_getvalue( 'modulo', $key)) == '' &&  $st > time()-8){
				$done= false;
				$mod= bcmul( $pn1, $pn2);
				file_put_contents( $kname, ('modulo='.$mod."\n"), FILE_APPEND);
			}
			if ((( $pubkey= JL_getvalue( 'publickey', $key)) == '' || ( $prvkey= JL_getvalue( 'privatekey', $key)) == '') &&  $st > time()-8){
				$done= false;
				$prm= array( 3,5,7,11,13,17,19,23,29,31,37,41,43,47,53,59,61,67,71,73,79,83,89,97);
				$p= 0;
				while (( !JL_is_coprime(( $pubkey= $prm[$p]), $mod) || strlen( $prvkey= JL_euclid( $pubkey, bcmul( bcsub( $pn1, 1), bcsub( $pn2, 1)))) < 100) && $p < count( $prm)){ $p++;}
				file_put_contents( $kname, ('publickey='.$pubkey."\n"), FILE_APPEND);
				file_put_contents( $kname, ('privatekey='.$prvkey."\n"), FILE_APPEND);
			}

			$fname= substr( $kname, strrpos( $kname, '/'));
			$fname= substr( $fname, 0, strpos( $fname, '.'));
			$fname= trim( $fname, './');

			// test it before sealing it.  Deleat it if not.
			if ( $done && JL_getvalue( 'keyset', $key) == '' && $st > time()-8){
				$test= JL_randomFill( '123456789', strlen( JL_getvalue( 'modulo', $key)/2));
echo 'pass?';
				if ( RSAdecrypt( RSAencrypt( $test, $fname), $fname) == $test){			
echo ' yes ';
					file_put_contents( $kname, ('keyset='.$fname."\n"), FILE_APPEND);
				}else{
					file_put_contents( $kname, ('tried to delete '.$fname."\n"), FILE_APPEND);
					unlink( $kname);
echo ' no delete-'.$kname.'<br>';			
				}
			}	
		}	
	}

	// send instruction to continue
	if ( !$done){
		//i= n[0]; setTimeout( t, ((( n[1] - i) * n[2]) + n[3]));
		echo '<!-- cmd '.( $t= intval( JL_monitorserverwork( $kname))).','.NUMPROC.','.NUMTIME.','.NUMOFFS.', -->'.$t.','.$kname.' '.$keyn['b'];
	}else{
		echo '<!-- cmd 0,10,900,500, -->done';
	}
	exit();
}

// other cryptography AJAX for negotiating a key file and establishing a secure chanel
if ( isset( $_POST['setAkey'])){
	session_start();	
	$_SESSION['JL_RC4key']= RSAdecrypt( $_POST['setAkey'], $_POST['key']);
	echo encryptToClient( $_SESSION['JL_RC4key']);
	exit();
}
if ( isset( $_POST['set4key'])){
	session_start();	
	$temp= decryptFromClient( $_POST['set4key']);
	$_SESSION['JL_RC4key']= $temp;
	echo encryptToClient( $_SESSION['JL_RC4key']);
	exit();
}
if ( isset( $_POST['testrc4'])){
	session_start();
	echo encryptToClient( decryptFromClient( $_POST['testrc4']));
	exit();	
}

// user functions
function decryptFromClient( $str){
	//session_start();
	$out= JL_urldesafe( $str);
	$a= RC4crypt( $out, $_SESSION['JL_RC4key']);
	if ( strpos( $a, 'auTheNtiCate_-') === false){
		$_SESSION['JL_RC4key']= '';
		return $out; 
	}
	return substr( $a, ( strpos( $a, 'auTheNtiCate_-') + 14));
}
function encryptToClient( $str){
	//session_start();
	$enc= JL_randomFill( 'JerryWickey', ( 53- (( strlen( $str) + 14) % 53))) . 'auTheNtiCate_-' . $str;
	if ( $_SESSION['JL_RC4key'] != ''){
		return RC4crypt( $enc, $_SESSION['JL_RC4key']);
	}
	return $str;
}
function RSAencrypt( $num, $key){
	if ( file_exists( KEYDIR.'/'.$key.'.php')){
		$f= file_get_contents( KEYDIR.'/'.$key.'.php'); 
		return JL_powmod( $num, JL_getvalue( 'publickey', $f), JL_getvalue( 'modulo', $f));	
	}else{
		return $num;
	}
}
function RSAdecrypt( $num, $key){
	if ( file_exists( KEYDIR.'/'.$key.'.php')){
		$f= file_get_contents( KEYDIR.'/'.$key.'.php'); 
		return JL_powmod( $num, JL_getvalue( 'privatekey', $f), JL_getvalue( 'modulo', $f));			
	}else{
		return $num;
	}
}
function RC4crypt( $str, $key){
	$res= $str;
	if ( file_exists( KEYDIR.'/'.$key.'.php')){
		$key= file_get_contents( KEYDIR.'/'.$key.'.php');
	}
	if ( trim( $key) != ''){
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

// Internal functions
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
function JL_urldesafe( $str){
	$urlsafe= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
	$esc= '().*'; // %&+. =
	$out= '';
	for ($i=0; $i<strlen( $str); $i++){
		if ( strpos( $urlsafe, $str[$i]) !== false){
			$out.= $str[$i];	
		}else{
			$out.= chr(( strpos( $esc, $str[$i]) * 64) + strpos( $urlsafe, $str[$i+1]));
			$i++;			
		}
	}
	return $out;
}
function JL_urlsafe( $str){
	$valid=	$urlsafe= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
	$esc= '().*'; // %&+. =
	$out= '';
	for ( $i=0; $i<strlen( $str); $i++){
		if ( strpos( $valid, $str[$i]) !==false){
			$out.= $str[$i];
		}else{
			$out.= $esc[ ord( $str[$i]) / 64];
			$out.= $valid[ ord( $str[$i]) % 64];		
		}
	}
	return $out;
}
function JL_randomFill( $safe, $n){
	$out= '';
	for ( $i=0; $i<$n; $i++){
		$out.= $safe[ mt_rand( 0, strlen( $safe))];
	}
	return $out;
}
function JL_getvalue( $name, $txt){
	$value= ''; // $name.' not found';
	if (( $p= strrpos( $txt, ( "\n".$name.'='))) !== false){
		$value= trim( substr( $txt, ( strpos( $txt, '=', $p) + 1), ( strpos( $txt, "\n", ($p+1)) - strpos( $txt, '=', $p) - 1)));
	} 
	return $value;
}
function JL_sortby( $a, $k){
	$ndone= true;
	while ( $ndone){
		$ndone= false;
		for ( $i=1; $i<count( $a); $i++){
			if ( $a[$i-1][$k] < $a[$i][$k]){
				$ndone= true;
				$b= $a[$i-1][$k];
				$a[$i-1][$k]= $a[$i][$k];
				$a[$i][$k]= $b;
			}
		} 	
	}
	return $a;
}
function JL_monitorserverwork( $n){
	file_put_contents(( KEYDIR.'/working.txt'), ( time().';'.$n."\n"), FILE_APPEND);
	// time\n
	do{
		$f= file_get_contents( KEYDIR.'/working.txt');
		if ( strlen( $f) == 0){ sleep ( 1);}
	}while ( strlen( $f) == 0);
	$f= explode( "\n", $f);
	$ff= '';
	$c= 0;
	foreach ( $f as $n=>$v){
		if ( intval( $v) > time()-10){
			$c++;
		}
		if ( intval( $v) > time()-300){
			$ff.= $v."\n";
		} 	
	}
	file_put_contents(( KEYDIR.'/working.txt'), $ff);
	if ( $c < NUMPROC){
		return ( NUMPROC - $c);
	}else{
		return 1;
	}
}
function JL_make_prime( $bits){
	$t= time();
	$tt= intval( substr(( ''.$t), 9, 1));
	for ( $i=0; $i<$tt; $i++){ mt_rand( 0,1); }
	$d= $bits;
	$b= array( 1, 3, 7, 9);
	$n= '';
	$p= false;
	while ( !$p && $t > time()-9 ){		
		$n= ''; 
		while ( strlen( $n) < $d -2){
			$n.= mt_rand( 0,9); 
		}
		str_shuffle( $n );
		$n= mt_rand( 1, 9) . $n . $b[mt_rand( 0, 3)];
		while ( !$p && $t > time()-9 ){
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
		return false;
	}
}	
function JL_is_coprime( $less, $more){
	if ( bccomp( bcmul( bcdiv( $more, $less), $less), $more)==0){
		return false;
	}else{
		return true;	
	}
}
function JL_is_prime( $num) {
	global $JL_primes;
	if ( count( $JL_primes) < 10){ 
		$JL_primes= explode( ',', file_get_contents( KEYDIR.'/primes.txt')); 
	}
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
	$tmp= bcpowmod( $base, $tmp, $num);
	if ( !bccomp( $tmp, '1')) { return true; }
	while ( $zero_bits--) {
		if ( !bccomp( bcadd( $tmp, '1'), $num)) { return true; }
		$tmp = bcpowmod( $tmp, '2', $num);
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
function JL_powmod( $a, $b, $c){
	return bcpowmod( $a, $b, $c);
}


// retrieve resource files from Jerry's server and store them on this server
// one time execution
$path=  substr( $_SERVER['SCRIPT_FILENAME'], 0, strrpos( $_SERVER['SCRIPT_FILENAME'], '/'));
$httpurl= 'http://'. 'jerrywickey.com/test'  ;
if ( !is_dir( $path.'/'.KEYDIR)){
	mkdir( $path.'/'.KEYDIR);
}
$ft= time();
if ( !file_exists( $path.'/'.KEYDIR.'/primes.txt')){
	file_put_contents( $path.'/'.KEYDIR.'/primes.txt', file_get_contents( 'http://jerrywickey.com/test/temp/primesORG.txt'));
}
while ( strlen( file_get_contents( $path.'/'.KEYDIR.'/primes.txt')) < 100 && $ft > time()-10){
	sleep( 1);
	file_put_contents( $path.'/'.KEYDIR.'/primes.txt', file_get_contents( 'http://jerrywickey.com/test/temp/primesORG.txt'));
}
if ( !file_exists( $path.'/'.KEYDIR.'/unlockred.gif')){
	file_put_contents( $path.'/'.KEYDIR.'/unlockred.gif', file_get_contents( 'http://jerrywickey.com/images/unlockred.gif')); 
}
while ( strlen( file_get_contents( $path.'/'.KEYDIR.'/unlockred.gif')) < 100 && $ft > time()-10){
	sleep( 1);
	file_put_contents( $path.'/'.KEYDIR.'/unlockred.gif', file_get_contents( 'http://jerrywickey.com/images/unlockred.gif'));
}
if ( !file_exists( $path.'/'.KEYDIR.'/lockred.gif')){
	file_put_contents( $path.'/'.KEYDIR.'/lockred.gif', file_get_contents( 'http://jerrywickey.com/images/lockred.gif'));
}
while ( strlen( file_get_contents( $path.'/'.KEYDIR.'/lockred.gif')) < 100 && $ft > time()-10){
	sleep( 1);
	file_put_contents( $path.'/'.KEYDIR.'/lockred.gif', file_get_contents( 'http://jerrywickey.com/images/lockred.gif'));
}
if ( $ft < time()-10){
	echo "all resource files have not been retrieved";
	exit();
} 

// run on every page request to populate cryptography javascript variables

if ( isset( $_GET['initcrypt']) || ENCRYPT > 0){
	$bits= ENCRYPT;
	if ( isset( $_GET['initcrypt'])){
		$bits= intval( $_GET['initcrypt']);
	}
	$stop= '';
	$outk= '';
	$outm= '';
	$outf= '';
	if ( intval( $bits) > 0){
		// nas_/key_ bits_- md5.php
		// bits=
		// bigprime=
		// smallprime=
		// modulo=
		// privatekey=
		// publickey=
		// keyset=complete
		$keyf= array();
		$i= 0;
		$dh= opendir( KEYDIR);
		while (( $file= readdir( $dh)) !== false){
			$file= KEYDIR.'/'.$file;
			$f= file_get_contents( $file);
			if ( JL_getvalue( 'keyset', $f) != ''){
				$keyf[$i]['n']= $file;
				$keyf[$i]['b']= JL_getvalue( 'bits', $f);
				$keyf[$i]['t']= filemtime( $file);
				$i++;
			}
		}
		closedir( $dh);
		$keyf= JL_sortby( $keyf, 't');
		$keys= array();
		$j= 0;
// select encryption size to use
		for ( $i=0; $i<count( $keyf); $i++){
			if ( intval( $keyf[$i]['b']) >= $bits){
				$keys[$j]= $keyf[$i];
				$j++;
			}
		}
		$keys= JL_sortby( $keys, 'b');
		$keys= array_reverse( $keys);
		$i= 0;
		while ( $keys[$i]['b'] == $keys[0]['b'] && $i < count( $keys)){ $i++;}
		$i--; 
		$f= $keys[rand( 0, $i)]['n'];
		if ( file_exists( $f)){
			$key= file_get_contents( $f);
			$outk= JL_getvalue( 'publickey', $key);
			$outm= JL_getvalue( 'modulo', $key);
			$outf= JL_getvalue( 'keyset', $key);
		}else{
			$stop= 'Not enough key sets have been generated.  Generating.  To view library Refresh page when done.<script>JL_maintaincryptsend()</script></body></html> ';
		}
		if ( isset( $_GET['initcrypt'])){
			echo $outf.','.$outk.','.$outm;
			exit();
		}
		JL_monitorserverwork( $f);
	}
}


// ========== 
//
// user php here to run before each page load
//
// and /or inline php embedded in the html below
// ==========








?>
<!DOCTYPE HTML PUBLIC>
<!-- over 600 lines of php above this.  Download link below to see it -->
<html>
<head>
<script type="text/javascript">

// libary javascript.  This can not be in a .js file as php must populate initial global variable values
// every thing after the vairable declarations could be done in .js file.

var JL_setAjax= '<?php echo ALLOWAJAX; ?>';	// allow ajax
var JL_crybits= '<?php echo ENCRYPT; ?>';	// 0 for no encryption; set these in php header at top
var RSApublick= '<?php echo $outk; ?>';
var RSAmodulok= '<?php echo $outm; ?>';
var RSAkeyname= '<?php echo $outf; ?>';
var PHPlibpath= '';
var JL_rc4keys= '';
var JL_ajax= new Array();
var JL_ajaxu= new Array();
var JL_comm= 0;


// example of shortest possible source file  '=' is '<' in the obvious places for obvious reason
// even despite javascript remark // 
// apache or other http server will parse < ?php  I had to separate with space even here

// =?php include( 'filepath_to_php_file_above'); ? >
// =!DOCTYPE HTML PUBLIC>=html>=head>
// =script type="text/javascript">
// var JL_setAjax= '=?php echo ALLOWAJAX; ? >';
// var JL_crybits= '=?php echo ENCRYPT; ? >';
// var RSApublick= '=?php echo $outk; ? >';
// var RSAmodulok= '=?php echo $outm; ? >';
// var RSAkeyname= '=?php echo $outf; ? >';
// =/script>
// =script type="text/javascript" src="filepath_to_js_file_below">=/script>
// =/head>=body>. . .=/body>=/html>


// User AJAX Functions 
// Cookie, Session and XDomain functions =========================================
function setcookie( aname, avalue, time, path, adomain){	// set a cookie
	return synchttp(( 
		PHPlibpath+'?setcky='
		+encodeURIComponent( aname)
		+'&v='+ encodeURIComponent( avalue)
		+'&t='+ encodeURIComponent( time)
		+'&p='+ encodeURIComponent( path)
		+'&d='+ encodeURIComponent( adomain)), ''
	);	
}
function getcookie( aname){	// get a cookie
	return synchttp(( PHPlibpath+'?getcky='+encodeURIComponent( aname)), '');
}
function setsession( aname, avalue){	// set a session variable
	return synchttp(( PHPlibpath +'?setses=' +encodeURIComponent( aname) +'&v=' +encodeURIComponent( avalue)), '');
}
function getsession( aname){	// get the value of a session variable
	return synchttp(( PHPlibpath+'?getses='+encodeURIComponent( aname)), '');
}
function Xdomain( aurl, post, cookiejar, agent, timeout){
	// returns response to get or post of any domain foreign to this page server
	return synchttp(( PHPlibpath+'?xdomain='+encodeURIComponent( aurl)
		+'&a='+encodeURIComponent( agent)+'&t='+encodeURIComponent( timeout)
		+'&c='+encodeURIComponent( cookiejar)), 
		('&p='+encodeURIComponent( post)));
}
function multiXdomain( aurl, post, cookiejar, agent, timeout, response){ 
	// returns response to get or post of any domain foreign to this page server to specified function
	multihttp(( PHPlibpath+'?xdomain='+encodeURIComponent( aurl)
		+'&a='+encodeURIComponent( agent)
		+'&t='+encodeURIComponent( timeout)
		+'&c='+encodeURIComponent( cookiejar)), 
		('&p='+encodeURIComponent( post)), response);
}
function synchttp( where, post){	
	// returns response to get or post 
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
	// returns response to get or post to specified function
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
function ge( a){	// returns an obect with -a- id
	return document.getElementById( a);
}
function ms( a){	// modifies the mouse cursor
	if ( a==1){ a= 'default';}
	if ( a==2){ a= 'pointer';}
	document.body.style.cursor= a; 
}
function getypos( obj){ // returns the absolute y position of the object
	var y= 0
	while( obj){
		y= y+obj.offsetTop;
		obj= obj.offsetParent;
	}
	return y;
}
function getxpos( obj){	// returns the absolute x position of the object
	var x= 0
	while( obj){
		x= x+obj.offsetLeft;
		obj= obj.offsetParent;
	}
	return x;
}
function ekey( a){  // returns true if the enter key is clicked
	var key= 0;
	if ( window.event){
		key= window.event.keyCode;
	}else if ( a){
		key= a.which;
	}
	var r= false;
	if ( key==13){ r= true;}
	return r;
}
function trim( txt, ofthese){	// trims chars from both ends of a string
	var trims= " \t\r\n";
	txt= ''+txt;
	ofthese= ''+ofthese;
	if ( ofthese.length>0){ trims= ofthese;}
	if ( txt.length>0){
		while ( trims.indexOf(txt.charAt(0))>=0 && txt.length>0){
			txt= txt.substring(1);
		}
		while ( trims.indexOf(txt.charAt(txt.length-1))>=0 && txt.length>0){
			txt= txt.substring(0, txt.length-1);
		}		
	}
	return txt;
}
function subtute( replacethis, withthis, intext){ // substitutes a string for another string in a string
	if ( replacethis == withthis){ return intext; }
	replacethis= ''+replacethis;
	withthis= ''+withthis;
	intext= ''+intext;
	if ( replacethis.length > 0 && intext.length > 0){
		var position= 0;
		var occurance= intext.indexOf( replacethis);
		while ( occurance != -1){
			intext= intext.substring( 0, occurance) + withthis + intext.substring(replacethis.length+occurance);
			position= occurance + withthis.length;
			occurance= intext.indexOf( replacethis, position);
		}
	}	
	return intext;		
}
function collect( prefix){  // collects values from all DOM elements with the given prefix
	var out= '';			// use this as a fast way to collect data for AJAX forms
	var all= document.getElementsByTagName("*");
	for ( var i=0; i<all.length; i++){
		if ( all[i].id.indexOf( prefix) == 0){
			out+= '&'+all[i].id+'=';
			if ( ('text textarea hidden password').indexOf( all[i].type) >-1){
				out+= encodeURIComponent( all[i].value);
			}
			if ( ('radio checkbox').indexOf( all[i].type) >-1){
				if ( all[i].checked){
					out+= 'checked';
				}
			}
			try{
				out+= all[i].innerHTML;
			}catch(e){}
		}
	}
	return out; 
}
function populate( nameValueSirializedArray){  // reverse of collect.  It populates a form or page elements
	var all= nameValueSirializedArray.split( "&"); // nameValueSirializedArray= '&name=value&name2=value2. . .
	for ( var i=0; i<all.length; i++){
		if ( all[i].indexOf( '=') > -1){
			var parts= all[i].split( '=');
			try{
				ge( parts[0]).value= subtute( '+', ' ', unescape( parts[1]));
			}catch(e){}
			try{
				ge( parts[0]).innerHTML= subtute( '+', ' ', unescape( parts[1]));
			}catch(e){}
			try{
				if ( parts[1] == 'checked'){ 
					ge( parts[0]).checked= true;
				}else if ( parts[1] == ''){
					ge( parts[0]).checked= false;
				}
			}catch(e){}
		}
	}
}

// User Cryptography Functions ==================================================
function initcrypt( bits){	// initilize cryptography functions of the specified bits
	if ( bits != 0){
		if ( bits == 1){
			bits= JL_crybits;
		}
		if ( RSAmodulok.length < 100){ return false; }
		setTimeout(( 'JL_sendinit( '+bits+')'), 200);
		JL_cryptcomm( 'plaintext', 10, ('Securing '+bits+' Channel')); //RSApublick, RSAmodulok);
	}else{
		JL_cryptcomm( 'plaintext', 10, 'No Secure Channel Requested');
	}
}
function decryptFromServer( m){	// decrypte a message from the server
	if ( JL_rc4keys.length > 0){
		var str= JL_RC4crypt( m, JL_rc4keys);
		if ( str.indexOf( 'auTheNtiCate_-') == -1){
			//alert( "WARNING:\n\nInvalid encrypted data received\n\nThis may simply be an Internet communication error");
			JL_cryptcomm( 'message', 10, '<span style="color:red;">Plain Text Downlink</span>');
			//return str;
		}
		JL_cryptcomm( 'message', 10, '<span style="color:red;">Encrypted Downlink</span>');
		return str.substring( str.indexOf( 'auTheNtiCate_-') + 14);
	}
	JL_cryptcomm( 'message', 10, '<span style="color:red;">Plain Text Downlink</span>');
	return m;
}
function encryptToServer( str){	// encrypt a message going to the server
	if ( JL_rc4keys.length > 0){
		str= JL_randomFill( '0123456789ABDFGHLMNOPQSTUVXZbdfghlmnopqstuvxz', ( 53- (( str.length + 14) % 53))) + 'auTheNtiCate_-' + str; //salt the new key
		JL_cryptcomm( 'message', 10, '<span style="color:green;">Encrypted Uplink</span>');
		return JL_urlsafe( JL_RC4crypt( str, JL_rc4keys));
	}
	JL_cryptcomm( 'message', 10, '<span style="color:green;">Plain Text Uplink</span>');
	return JL_urlsafe( str);
}
function JL_cryptcomm( cmd, time, mess){	// activate the cryptography user interface
//ge('maint').innerHTML+= cmd+' '+ time+' '+mess+'<br>';
	if ( !ge( 'securegif')){
		var t='<div id="secureback" style="position: relative; background: white; border-radius: 0px 0px 10px 0px; width: 300px; padding: 2px;">';
		t+= '<div id="secureprog" style="position: absolute; top: 2px; left: 0px; width: 0px; background: #8f8; border-radius: 0px 5px 5px 0px;">&nbsp;</div>';
		t+= '<div id="securemess" style="position: absolute; top: 2px; left: 50px; width: 250px;"></div>';
		t+= '<img onclick="if(JL_rc4keys==\'\'){alert(\'Plain Text Connection\');}else{alert(\'Connection Secured by \'+JL_crybits+\' bit key \'+JL_rc4keys);}" id="securegif" src="<?php echo KEYDIR; ?>/unlockred.gif" style="position: relative; width: 45px; height: 30px;"></div>';
		ge('comstat').innerHTML= t; 
	}
	if ( mess != ''){ 
		ge( 'securemess').innerHTML= mess; 
		ge( 'secureback').style.background= '#ddd';
		ge( 'secureback').style.width= '300px';
	}
	if ( cmd == 'secure'){
		if ( time <= 0){
			ge( 'securemess').innerHTML= '';
			ge( 'secureback').style.width= '0px';
			ge( 'secureback').style.background= 'white';
			ge( 'secureprog').style.width= '0px';
		}else{
			ge( 'securegif').src="<?php echo KEYDIR; ?>/lockred.gif";
			ge( 'securegif').style.width= '30px';
			ge( 'securegif').style.height= '20px';
			clearTimeout( JL_comm);
			JL_comm= setTimeout( 'JL_cryptcomm( \'message\', '+(time-1)+', \''+mess+'\')', 1000);
		}
	}
	if ( cmd == 'plaintext'){
		if ( time <= 0){
			ge( 'securemess').innerHTML= '';		
			ge( 'secureback').style.width= '0px';
			ge( 'secureback').style.background= 'white';
			ge( 'secureprog').style.width= '0px';
		}else{
			ge( 'securegif').src="<?php echo KEYDIR; ?>/unlockred.gif";
			ge( 'securegif').style.width= '30px';
			ge( 'securegif').style.height= '20px';
			clearTimeout( JL_comm);
			JL_comm= setTimeout( 'JL_cryptcomm( \'message\', '+(time-1)+', \''+mess+'\')', 1000);
		}
	}
	if ( cmd == 'timer'){
		if ( time <= 0){
			ge( 'secureback').style.background= 'white';
			ge( 'secureprog').style.width= '0px';
		}else{
			if ( time > 9){ time= 6; }
			ge( 'secureback').style.background= '#f88';
			ge( 'secureprog').style.width= parseInt(( 30 * time ) + 20)+'px';
			clearTimeout( JL_comm);
			JL_comm= setTimeout( 'JL_cryptcomm( \'timer\', '+(time+1)+', \'\')', 1000);
		}
	}
	if ( cmd == 'message'){
		if ( time <= 0){
			ge( 'securemess').innerHTML= '';
			ge( 'secureback').style.width= '0px';
			ge( 'secureback').style.background= 'white';
			ge( 'secureprog').style.width= '0px';
		}else{
			ge( 'securemess').innerHTML= mess;
			ge( 'secureback').style.background= '#ddd';
			ge( 'secureback').style.width= '300px';
			clearTimeout( JL_comm);
			JL_comm= setTimeout( 'JL_cryptcomm( \'message\', '+(time-1)+', \''+mess+'\')', 1000);
		}
	}		
	//ge('comstat').innerHTML= '<div style="">'+cmd+' '+time+' '+mess+'</div>';
}
// Internal Cryptography Functions ==================================================
function JL_sendinit( bits){
	JL_rc4keys= JL_randomFill( '123456789', 1) + JL_randomFill( '0123456789', (RSAmodulok.length-3));
	JL_sPowmodInParts( JL_rc4keys, RSApublick, RSAmodulok, '1', 0);  
	// JL_sPowmodInParts passes multi stage results to JL_sendkey
}
function JL_sendkey( key){
	JL_cryptcomm( 'plaintext', 0, '');
	JL_cryptcomm( 'timer', 1, 'Securing RC4 Channel');
	multihttp(( PHPlibpath+'?'), ('&setAkey='+key+'&key='+RSAkeyname), 'JL_sendrc4');
	// multihttp passes responce to JL_sendrc4
}
function JL_sendrc4( key){
	if ( JL_rc4keys == decryptFromServer( unescape( key))){
		var temp= '';
		var dash= '';
		while ( temp.length+8 < RSAmodulok.length-2){
			temp+= dash+JL_randomFill( '0123456789ABDFGHLMNOPQSTUVXZabdfghlmnopqstuvxz', 7);
			// Jerry Wickey
			// The letters of my name are impressed into every key in a way that does not aid decryption.
			// if you figure out how I did it, you can undo to and use your name instead
			dash= '-';
		}
		var etemp= encryptToServer( temp);
		JL_rc4keys= temp;
		multihttp(( PHPlibpath+'?'), ('&set4key='+etemp), 'JL_testsecure');
		JL_cryptcomm( 'timer', 1, ('Negotiating '+JL_crybits+' bit RC4 key')); //JL_rc4keys, encryptToServer( temp));
	}else{
		JL_cryptcomm( 'plaintext', 10, 'Problem negotiating key.  Reload page for secure connection.');
	}	
	
	// multihttp passes responce to JL_testsecure
}
function JL_testsecure( key){
	if ( JL_rc4keys == decryptFromServer( unescape( key))){
		JL_cryptcomm( 'timer', 0, ' ');
		JL_cryptcomm( 'secure', 10, (JL_crybits+' bit key secured'));//JL_rc4keys, decryptFromServer( unescape( key)));
		//alert( decryptFromServer( synchttp( '?', ('&testrc4='+encryptToServer( 'good')))));
		JL_maintaincryptsend();
	}
}
function JL_maintaincrypt( cmd){
	// called by initcrypt to run in the background
	cmd= unescape( cmd);
	var n= cmd.substring( cmd.indexOf( '<!-- cmd ')+9).split( ',');
	var d= parseInt( n[1]) + parseInt( n[2]) + parseInt( n[3]);
ge( 'maint').innerHTML+= '<br>'+d+' '+n[0]+' '+cmd.substring( cmd.indexOf( '<!-- cmd ')+9)+'<br>';
	if ( !isNaN( d)){
		for ( var i=0; i<n[0]; i++){
			var d= (( parseInt( n[1]) - i) * parseInt( n[2])) + parseInt( n[3]);
			var t= 'multihttp(\''+PHPlibpath+'?maintaincrypt=1\', \'\', \'JL_maintaincrypt\')';
			setTimeout( 'JL_maintaincryptsend()', d);
ge( 'maint').innerHTML+= d+' ';
		}	
	}	
}
function JL_maintaincryptsend(){
	multihttp(( PHPlibpath+'?maintaincrypt=1'), '', 'JL_maintaincrypt');
	ge( 'maint').innerHTML+= '+ ';
}

// the actual encryption functions
// returns to JL_sendkey a to the power of b mod c
function JL_sPowmodInParts( a, b, c, r, i){	// RSA encryption  is broken up onto parts
	JL_cryptcomm( 'timer', (i+1), ('Negotiating '+JL_crybits+' bit RSA key')); 
	// because it is computationally expensive
	r= JL_bmod( JL_bmul( r, a), c);
	i++;
	if ( i < parseInt( b)){
		var h= 'JL_sPowmodInParts( "'+a+'", "'+b+'", "'+c+'", "'+r+'", "'+i+'")';
		setTimeout( h, 500);
	}else{
		JL_sendkey( r); // send the results to server 
	}
}
function JL_RC4crypt( str, key){  // RC4 encryption and decryption
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

// Internal functions
function JL_urlsafe( str){
	var urlsafe= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
	var esc= '().*'; // %&+. =
	var out= '';
	for (var i=0; i<str.length; i++){
		if ( urlsafe.indexOf( str.charAt( i)) > -1){
			out+= str.charAt( i);
		}else{
			out+= esc.charAt( Math.floor( str.charCodeAt( i) / 64));
			out+= urlsafe.charAt( str.charCodeAt( i) % 64);		
		}
	}
	return out;
}
function JL_urldesafe( str){
	var urlsafe= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
	var esc= '().*'; // %&+. =
	var out= '';
	for ( i=0; i<str.length; i++){
		if ( esc.indexOf( str.charAt(i)) > -1){
			out+= String.fromCharCode( esc.indexOf( str.charAt( i)) * 64 + urlsafe.indexOf( str.charAt( i+1)));
		}else{
			out+= str.charAt(i);
		}
	}
	return out;
}
function JL_randomFill( safe, n){
	var t= new Date();
	t= ''+t.getTime();
	t= parseInt( t.substring( 11, 12));
	for ( var i=0; i<t; i++){ Math.random(); }
	if ( safe==''){ safe= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_'; }
	var out= '';
	for (var i=0; i<n; i++){
		out+= safe.charAt( Math.floor( Math.random() * safe.length));
	}
	return out;
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

// Integer Binary Math library
function JL_isgreater( a, b){	// returns 0 on equel, 1 on a>b, -1 on a<b
	a= a+'';
	b= b+'';
	if ( a.length > b.length){ return 1; }
	if ( a.length < b.length){ return -1; }
	var i= 0;
	while ( a.charAt( i) == b.charAt( i) && i < a.length){ i++; }
	if ( i == a.length){ return 0;}
	if ( a.charAt( i) > b.charAt( i)){ return 1; }else{ return -1; }
}
function  JL_iadd( a, b){	// returns a + b
	a= a+'';
	b= b+'';
	var l= a.length;
	while ( b.length < l){ b= '0' + b; }
	if ( b.length > l){ l= b.length; }
	while ( a.length < l){ a= '0' + a; }
	l--;
	var c= 0;
	var r= '';
	for ( var i=l; i>=0; i--){
		c= c + parseInt( a.charAt( i)) + parseInt( b.charAt( i));
		if ( c > 9){
			c= c-10;
			r= '' + c + r;
			c= 1;
		}else{
			r= '' + c + r;
			c= 0;
		}
	}
	if ( c == 1){ r= ''+ c+ r; }
	while ( r.charAt( 0) == '0' && r.length > 1){ r= r.substring( 1); }
	return r;
}
function  JL_isub( a, b){	// returns a - b or NV9999. . . if b>a
	a= a+'';
	b= b+'';
	var r= '';
	var l= a.length;
	while ( b.length < l){ b= '0' + b; }
	if ( b.length > l){ l= b.length; }
	while ( a.length < l){ a= '0' + a; }
	l--;
	var c= 0;
	var r= '';
	for ( var i=l; i>=0; i--){
		c= parseInt( a.charAt( i)) - parseInt( b.charAt( i)) - c;
		if ( c < 0){
			c= c+10;
			r= c + r;
			c= 1;
		}else{
			r= c + r;
			c= 0;
		}
	}
	while ( r.charAt( 0) == '0' && r.length > 1){ r= r.substring( 1); }
	if ( c == 1){ r= 'NV'+r; }
	return r;
}
function JL_bmul( a, b){	// returns a * b
	a= a+'';
	b= b+'';
	var v= [ a];
	var vr= [ 1];
	var i= 0;
	while ( JL_isgreater( b, vr[i]) != -1){
		v[i+1]= JL_iadd( v[i], v[i]);
		vr[i+1]= JL_iadd( vr[i], vr[i]);
		i++;
	}
	var r= '0';
	var d= b;
	for ( var i= v.length-1; i >= 0; i--){
		if ( JL_isgreater( d, vr[i]) != -1){
			r= JL_iadd( r, v[i]);
			d= JL_isub( d, vr[i]);
		}
	}
	return r;
}
function JL_bdiv( a, b){	// returns a / b
	a= a+'';
	b= b+'';
	if ( JL_isgreater( b, '0') == 0){
		return 'NV';
	}
	if ( JL_isgreater( a, b) == -1){
		return '0';
	}
	if ( JL_isgreater( a,  JL_iadd( b, b)) == -1){
		return '1';
	}
	var r= 0;
	var d= a;
	var v= [ b];
	var vr= [ 1];
	var i= 0;
	var t= '';
	while ( JL_isgreater( d, v[i]) == 1){
		v[i+1]=  JL_iadd( v[i], v[i]);
		vr[i+1]=  JL_iadd( vr[i], vr[i]);
		i++;
	}
	for ( var i= v.length-1; i >= 0; i--){
		if ( JL_isgreater( d, v[i]) != -1){
			d=  JL_isub( d, v[i]);
			r=  JL_iadd( r, vr[i]);
		}
	}
	return r;
}
function JL_bmod( a, b){	// returns the a mod b
	//return JL_isub( a, JL_bmul( b, JL_bdiv( a, b)));
	a= a+'';
	b= b+'';
	if ( JL_isgreater( b, '0') == 0){
		return 'NV';
	}
	if ( JL_isgreater( a, b) == -1){
		return a;
	}
	var r= 0;
	var d= a;
	var v= [ b];
	var vr= [ 1];
	var i= 0;
	var t= '';
	while ( JL_isgreater( d, v[i]) == 1){
		v[i+1]=  JL_iadd( v[i], v[i]);
		vr[i+1]=  JL_iadd( vr[i], vr[i]);
		i++;
	}
	for ( var i= v.length-1; i >= 0; i--){
		if ( JL_isgreater( d, v[i]) != -1){
			d=  JL_isub( d, v[i]);
			r=  JL_iadd( r, vr[i]);
		}
	}
	return d;
}
function JL_bpow( a, b){	// raise a to power b
	a= a+'';
	b= b+'';
	var v= [ a];
	var vr= [ 1];
	var i= 0;
	while ( JL_isgreater( b, vr[i]) != -1){
		v[i+1]= JL_bmul( v[i], v[i]);
		vr[i+1]= JL_iadd( vr[i], vr[i]);
		i++;
	}
	var r= '1';
	var d= b;
	for ( var i= v.length-1; i >= 0; i--){
		if ( JL_isgreater( d, vr[i]) != -1){
			r= JL_bmul( r, v[i]);
			d= JL_isub( d, vr[i]);
		}
	}
	return r;
}
function JL_spowmod( a, b, c){  // small power mod
	a= a+'';
	b= b+'';
	c= c+'';
	var r= '1';
	for ( var i= 0; i < parseInt( b); i++){
		r= JL_bmod( JL_bmul( r, a), c);
	}
	return r;
}
</script>

<meta charset="utf-8">
<meta name="viewport" content="width=800, initial-scale=1.0">

<meta property="og:title" content="Nerida Hadar">
<meta property="og:type" content="project">
<meta property="og:image" content="http://neridahadar.club/banners/IMG_0205.JPG">
<meta property="og:url" content="http://neridahadar.club">
<meta property="og:description" content="watch a boat turn into a yacht">

<link rel="apple-touch-icon" sizes="57x57" href="http://jerrywickey.com/favicon.ico">
<link rel="apple-touch-icon" sizes="60x60" href="http://jerrywickey.com/favicon.ico">
<link rel="apple-touch-icon" sizes="72x72" href="http://jerrywickey.com/favicon.ico">
<link rel="apple-touch-icon" sizes="76x76" href="http://jerrywickey.com/favicon.ico">
<link rel="apple-touch-icon" sizes="114x114" href="http://jerrywickey.com/favicon.ico">
<link rel="apple-touch-icon" sizes="120x120" href="http://jerrywickey.com/favicon.ico">
<link rel="apple-touch-icon" sizes="144x144" href="http://jerrywickey.com/favicon.ico">
<link rel="apple-touch-icon" sizes="152x152" href="http://jerrywickey.com/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="http://jerrywickey.com/favicon.ico">
<link rel="icon" type="image/png" sizes="192x192"  href="http://jerrywickey.com/favicon.ico">
<link rel="icon" type="image/png" sizes="32x32" href="http://jerrywickey.com/favicon.ico">
<link rel="icon" type="image/png" sizes="96x96" href="http://jerrywickey.com/favicon.ico">
<link rel="icon" type="image/png" sizes="16x16" href="http://jerrywickey.com/favicon.ico">

<title>Test RC4 over RSA</title>
</head>
<body style="margin: 20px; font-family: arial;" onload="initcrypt( 1)">
<!--  Counter to popular opinion, I advice against external CSS files.  There are good uses, but for
the most part, who wants to try to figure out in which CSS file the class for a span can be found 
so that one can add a single use CSS attribute which is better posed inline?  Just my rant today. -->

<div id="comstat" style="position: fixed; top: 0px; left: 0px;" ></div>
<div id="maint" style="float: right; width: 200px; "><b>Maintenance Activity</b><br><br></div>

<?php if ( $stop != ''){
	echo $stop;
	exit();
}
?>
<br>
<a href="http://<?php copy(( $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']), ( $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'].'.txt')); echo $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'.txt'; ?>">Download</a> the new version Feb 19, 2016<br>
<a href="http://jerrywickey.com/test/testJerrysLibrary.php">See manual of functions for the older version</a>
<br><br>

Much faster bcmath based on true binary multiplication <br>
- multiply two 700 digit decimal numbers (2300 bits) in less than <br>
- 40 iterations of 700 digit integer addition function (14000 700 int adds)<br><br>

greatly improved xdomain abuse detection and prevention<br><br>

added 'collect' and 'populate' functions for super easy form handling<br><br>

higher and much stronger encryption<br><br>

easier to use php html javascript template in a single -plug and play file<br><br>

This page demonstrates encryption in general and specifically of form data.  Play with it.<br><br>

PHP server must be configured to wrap file_get_contents to http requests, be configured for sessions and have bcmath installed.  Add HTML, php and javascript as desired.  &lt;body onload="initcrypt( 1)" starts encryption with the default settings at the top of this php source code.  onload="initcrypt( 0) or no "onload" prevents its start.  You may also use onload="initcrypt( 2048)" or 1024 or any size may also be used.  Only 1024 or 2048 will be selected.  Altering PHP code under the remarks "// select encryption size to use
" and "// select encryption size to create" provides any encryption level desired.  Remember an RSA key set more than 2500 bits is very very computationally expensive even just to generate.
  Just play with it.  Download the source code and use it in anyway you like.<br><br>


<hr>

<input type="text" id="testname"> Sample input box<br>

<!-- button onclick="var a=ge('testname').value.split(',');JL_cryptcomm(a[0],a[1],a[2])">See</button><br -->

<span id="testtext">testing</span> Sample span element<br>

<input type="checkbox" id="testbox"> Sample check box<br>

<input type="radio" name="rb" id="testsw1"><input type="radio" name="rb" id="testsw2"> Sample radio buttons<br>

Sample text area<br>
<textarea id="testta"></textarea><br>

<script> var rd= ''; </script>

<button onclick="rd= collect( 'test'); ge('output').innerHTML= '<b>Form Data collected: </b>'+rd+'<br><br>'">Collect Form Data</button>

<button onclick="rd= encryptToServer( rd); ge('output').innerHTML+= '<b>Form Data encrypted: </b>'+rd+'<br><br>'">Encrypt Form Data</button>

<button onclick="rd= synchttp( '?', ('&encrypteddata='+ rd)); ge('output').innerHTML+= '<b>Encrypted Data recieved back from the server: </b>'+escape( rd)+'<br><br>'">Send to Server</button>

<button onclick="rd= decryptFromServer( rd); ge('output').innerHTML+= '<b>Data decrypted, ready for population of the page: </b>'+rd+'<br><br>'">Decrypt Form Data From Server</button>

<button onclick="populate( rd)">Populate Form Data</button><br><br>

<div id="output"></div><hr><br><br>

It's free.  Sorry Government Hawks.  You just don't understand how much
you don't understand about deep data.  Your brains might be defective.  
Tortured by modern mythologies. Moses explains this.  Some people's 
opinions just aren't worth listening to. You  really can't find what you're 
looking for if you look at more data.  You NEED to look at LESS data.  If 
You don't believe me, Ask Google or IBM about AI.  If you don't, you 
shall inexorably over look the next big tragedy.  It shall happen right 
under your noses.  Embarrassing! I wish you would ask my help.  I could 
and would gladly advise you, pro-bono. <br><br>

I could start with Moses' admonishment for governments.  I am fully aware that you don't think Moses is relevant anymore.  But clearly you have not read and understood what he said.  You probably don't even know what you know and what you don't know.

</body>
</html>

