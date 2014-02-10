<?php

// AJAX response code 

if ( isset( $_GET['someGETvar']) || isset( $_POST['somePOSTvar'])){
	$tm= microtime( true);
	$dt= date( 'h:i:s');
	
	$out= 'request received '.$dt. substr( $tm, 10, 4).'<br>';
	foreach ( $_GET as $n=>$v){
		$out.= 'GET: '.$n.'='.$v.'<br>';
	}
	foreach ( $_POST as $n=>$v){
		$out.= 'POST: '.$n.'='.$v.'<br>';
	}
	
	// simulate some hard work to show off the advantages of multihttp
	sleep( rand( 1, 3));

	if ( $_GET['someGETvar'] > 10 || $_POST['somePOSTvar'] > 10){
		foreach ( $_COOKIE as $n=>$v){
			$out.= 'COOKIE: '.$n.'='.$v.'<br>';
		}
		session_start();
		foreach ( $_SESSION as $n=>$v){
			$out.= 'SESSION: '.$n.'='.$v.'<br>';
		}
	}		

	$out.= 'elapsed time '. floor(( microtime( true) - $tm) * 1000).' milliseconds<br><br>';
	echo $out;	
	exit();
}	


// AJAX encryption

if ( isset( $_POST['encrypted']) || isset( $_GET['testRememberMe'])){
	include( 'jerrysLibrary.php');

	$decrypted= decryptFromClient( $_POST['encrypted']);
	if ( isset( $_GET['testRememberMe'])){
		$decrypted= decryptFromClient( $_GET['testRememberMe']);
	}	
	
	$out = 'The message that was sent to the server:<br><span style="font-size:0.25em;"><br></span><b>';
	$out.= $decrypted.'</b><br><br>';	
	$out.= 'This message was encrypted before being sent. This is the encrypted message which the server received:<br><span style="font-size:0.25em;"><br></span><b>';
	$out.= $_POST['encrypted'].$_GET['testRememberMe'] . '</b><br><br>';
	$out.= 'PHP can now do something with it.  Respond to it.  In this example all the text you are reading in this div is the response. Before the response is sent back it must be encrypted again:<br><span style="font-size:0.25em;"><br></span><b>';
	$enc= encryptToClient( $out);
	$out.= str_replace( '<', '&lt', encryptToClient( $enc)).'</b><br><br>';
	$out.= 'The client then decrypts which re-produces the whole message you are reading right now.';
	echo encryptToClient( $out);
	exit();
} 


//redacted  comment handler

$message= '';
if ( file_exists('comments.php') && true){
	include ('comments.php');
}
if ( isset( $_POST['submitcomment'])){
	include( 'jerrysLibrary.php');
	echo encryptToClient( '<!-- 0 init -->Comments have been redacted from this server<br><br><a href="http://jerrywickey.com/test/testJerrysLibrary.php">Jerry\'s Library</a>');
	//redacted
	exit();
}	                                          
if ( isset( $_GET['abuse'])){
	//redacted
	exit();
}	                                          	
if ( isset( $_GET['cryplink'])){
	//redacted
	exit();
}	                                          	
?>
<!DOCTYPE HTML PUBLIC>
<html>
<head>
<!--  
Jerry's Crypto-AJAX JavaScript and PHP Library
Jan 8, 2014   Key West, FL US   
email - jerry (@-symbol-) jerrywickey (.-symbol-) com   
phone - eight hundred - seven two two - two two eight zero

thanks to Stevish RSA http://stevish.com/rsa-encryption-in-pure-php 
thanks to Ali Farhadi RC4 https://gist.github.com/farhadi/2185197

<!--
This newest verstion of this file along with the PHP portion
of the source can be downloaded from 

		http://jerrywickey.com/test/testJerrysLibrary.php





<!--      All example code is clearly remarked in this way      


 *                        initCrypto()                                   *
 *************************************************************************
 *		code example 													 *


 
 set these important constants either here or 
 in the first lines of the file jerrysLibrary.js             -->

<script src="http://jerrywickey.com/test/jerrysLibrary.js"></script>
<script>

//  path to jerrysLibrary.php  domain and subdirectory
//  set the directory path here.  Example   + '/your-path/';
//  the setting below presumes you've saved the PHP library file 
//  to the 'test/' directory on your server. 
var PHPlibpath= 'http://'+window.location.hostname + '/test/jerrysLibrary.php'; 

//  if PHP encryption or AJAX is not desired set JL_setAjax to false
//  miscellaneous functions are still availble
//  this can be set in the <body onload=  as well
var JL_setAjax= true;


//	These security issues must be set in the first lines 
//  of the file     	 jerrysLibrary.php

//  someone could abuse your server if you allow xdomaim    
// define ( 'ALLOWXDOMAIN', 'true');  // true / false

//  store session decryption key in SESSION variable
// define ( 'STOREKEYINSESSION', 'true');   // true / false -->
</script>




<title>Jerry's Cryptography AJAX Library</title>
<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1"/>
<meta name="viewport" content="width=800">
<style>
img.ex 
	{height: 0.7em; margin: 15px 5px 0px 20px; valign: bottom;} 

div.code 
	{font-family: courier; margin: 10px 5px 10px 30px; color: #66a;}
div.sec  
	{font-size: 1.2em; font-weight: bold; margin: 30px 0px 15px 0px;}
div.explain 
	{font-size: 0.8em; font-weight: normal; color: black; margin: 15px 0px 20px 30px; padding: 5px; border-radius: 5px; background: #f4f4f4;}
div.example 
	{font-size: 0.8em; font-weight: normal; color: black; margin: 15px 0px 20px 30px; padding: 5px; border-radius: 5px; background: #f4f4f4;}
span.workex 
	{font-size: 0.7em; font-weight: normal; color: black; }

div.comment
	{ margin-bottom: 15px; width: 100%; border-radius: 5px; border: thin solid #ddd; background: #eee; }
div.commentname
	{padding: 3px 5px 0px 5px; border-radius: 5px; font-size: 1em; font-weight: bold; background: #eef; color: #a88; }
div.commenttext
	{padding: 5px; border-radius: 5px; background: #f8f8f8; color: #888; }
span.time
	{font-size: 0.7em; font-weight: normal; color: #aaa; }
div.report
	{float: right; font-size: 0.7em; color: red;}
span.more
	{font-size: 0.7em; color: #faa;}
</style>
</head>

<body onload="initCrypto( 1024)" style="margin: 0px; font-family: tahoma;">
<div style="<?php if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'mob') !== false){ echo 'width:799px;'; } ?>">

	<div style="float: right; text-align: right; margin: 0px 0px 10px 20px; font-size: 0.7em; color: #888; background: #eef; border-radius: 5px; padding: 5px; width: 150px;">
	
		<a href="http://www.ipage.com/green-certified/" onclick="MyWindow=window.open('http://www.ipage.com/green-certified/jerrywickey.com','greenCertified','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=550,height=700,left=50,top=50'); return false;">
		<img src="http://www.ipage.com/green-certified/hosting-badge-1.png" border="0"></a>
		<br>
		<br>
		Jerry Wickey<br>
		Key West, FL US<br>
		<a style="color: #888;" href="http://jerrywickey.com">jerrywickey.com</a><br>
		<a style="color: #888;" href="mailto:jerry@jerrywickey.com">jerry@jerrywickey.com</a><br>
		<br><br>
		
		<div style="color: red; font-size: 1.5em;">
			<span id="wow"><b>Download</b></span><hr>
			<a href="jerrysLibrary.js">jerrysLibrary.js</a><br> 
			<br>
			<a href="jerrysLibrary.php.txt">jerrysLibrary.php</a><br>
			<br>
			<br>
			<a href="testJerrysLibrary.php.txt">testJerrysLibrary.php</a>
			
			<div style="color: black; font-size: 0.7em; text-align: left; margin-left: 5px;">
				<br>
				PHP, HTML and Javascript source for this page.
				The AJAX server form handler example is in this page	
				<br>
				<br>
				<div style="color: red; font-weight: bold;">
					<div id="comments">
						<img src="http://jerrywickey.com/israel/images/whirl.gif" width="15" height="15"> 
						<span style="font-size: 0.7em;">
						Retrieving comments.<br>
						Waiting for secure channel.</span>
					</div>
					<div id="followers"></div>
					<a href="#tocom">Comment below</a>
				</div>
			</div>
		</div>
		<br>
		<br>
		
	</div>
	
	<div style="margin: 0px 20px 10px 20px; font-size: 1em; color: #555;">
	
		<br>
		<center><h2>Jerry's Cryptography and AJAX javascript and PHP Library</h2>

		<h2>Ubiquitous Robust Encryption</h2></center>
	
		<b>Any web page can easily employ robust encryption</b><br><br>

		<table style="font-family: Tahoma, Arial, sans-serif; font-size: 1em; color: #888;">
		<tr><td style="width: 30px;">
		</td><td style="width: 250px;">
			<b>RC4 over RSA<br>
			really is just this easy</b>
			<span style="font-size: 0.7em;"><br><br>
			No SSL, no secure server needed.  javascript and PHP function library does everything, even client to server URL encoding automatically
			</span>
		</td><td style="width: 20px;">
		</td><td style="font-family:  Courier; font-size: 0.7em;">
			<b>js: &lt;body onload= initCrypto( 1024 bit)<br><br>
			js: encryptToServer( clientPlainText)<br><br>
			PHP: decryptFromClient( encryptedDataFromClient)<br><br>
			PHP: encryptToClient( serverPlainText)<br><br>
			js: decryptFromServer( encryptedDataFromServer)</b><br>
		</td></tr>
		</table><br>

		<div style="font-size: 0.7em; color: #666;">
			Encrypt everything on the Internet from mobile device web apps to dinner recipe web pages. The technology is now free.<br><br>

			Encrypting only important documents merely identifies which documents are important, attracting the most powerful decryption efforts. Which is better? A society in which no one can have any secrets? Or a society in which anyone can keep everything secret? 
		</div><br>


		<div class="sec">
			Cryptography
		</div>

		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'cryp')">
		<span style="color: #844">void</span> <b>initCrypto</b>( no_bits )<br>
		<div id="cryp" class="explain" style="display: none;">








<!--                      initCrypto( no_bits )                          *
 *************************************************************************
 *		code example 													-->




			<center><h3>initCrypto()</h3></center><br>

			This initializes the key generating handshake with the server.  It takes the number of bits for the key size and returns void.  <br><br>
			
			Call this function in &lt;body onload="" 
			<div class="code">	
				&lt;body onload="initCrypto( 1024 )">
			</div>	
			or let the user start crypto by selecting his desired bit strength
			<div class="code">	
				&lt;input type="text" id="bitstrength"><br>
				&lt;span onclick="initCrypto( getElementById( 'bitstrength').value)">Start&lt;/span>
			</div>	
			
			The Red Lock icon top right can be clicked at any time to show the current key and to reinitialize encryption.<br><br>
			
			To incorporate the Red Lock icon in your page, simply include this div at the bottom of your HTML, just before the &lt;/body>
			<div class="code">	
				&lt;div id="secureComIcon">&lt;/div>
			</div>
			If you use the Red Lock icon, please download these three graphics files and put them on your server in the directory "/images/" so that your pages don't leach off my server.  Or feel free to replace them with your own images.  Search jerrysLibrary.js for them to change the code in any way.<br>
			<br>
			&nbsp;&nbsp;&nbsp;<a href="http://jerrywickey.com/images/spacer.gif">spacer.gif</a> This is a smalls transparent blank space image.  So don't be confused when you don't see anything.<br>
			&nbsp;&nbsp;&nbsp;<a href="http://jerrywickey.com/images/lockred.gif">lockred.gif</a><br>
			&nbsp;&nbsp;&nbsp;<a href="http://jerrywickey.com/images/unlockred.gif">unlockred.gif</a><br><br>
			
			If the "secureComIcon" div is not found, the encryption status is not reported except by the two functions below.<br><br>
			
			When initCrypt() has completed securing an encrypted channel it calls this function
			<div class="code">	
				cryptoStarted( JL_crybits, JL_cryname, JL_rc4keys) 
			</div>
			If a secure channel could not be obtained for any reason, this function is called instead
			<div class="code">	
				cryptoUnavalable( JL_crybits, JL_cryname, JL_rc4keys)
			</div>
			
			These functions are not in the library.  You may write them.  If you don't write them and they don't exist, they are ignored.  They are not needed for secure communications.  They are provided for user information only.  You can use these to inform the user of status or for any other purpose.  You do not have to use nor write them at all.<br><br>
			
			Calling initCrypto() for the very first time creates a directory "temp" on your server.  This directory is created in the directory in which you place jerrysLibrary.php library file.  The contents of this directory will change constantly.  You don't have to do anything with its contents.<br><br>
			
			To use these functions you must include this line in the HTML header
			<div class="code">	
				&lt;script src="your-server-path/jerrysLibrary.js">&lt;/script>
			</div>
			and this line in all php scripts that respond to encrypted messages
			<div class="code">	
				&lt;?php include ('your-server-path/jerrysLibrary.php');
			</div>
		</div>
		<!--  end   -->
		
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'ents')">
		<span style="color: #844">string</span> 
		<b>encryptToServer</b>( dataFromClient )<br>
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'ents')">
		<span style="color: #844">string</span> 
		<b>decryptFromServer</b>( encryptedDataFromServer )<br>

		<div id="ents" class="explain" style="display: none;">








<!--            encryptToServer()   decryptFromServer()                  *
  *************************************************************************
*		code example 													-->




			<center><h3>encryptToServer() &nbsp;&nbsp;&nbsp; decryptFromServer()</h3></center><br>

			This is the only function you need to receive encrypted data from the server.  The minimum needed is this.
			
			<div class="code">	
				var encryptedValue= encryptToServer( plainText);<br>
				var encryptedResponse= synchttp(( 'serverScript.php?handler=' + encryptedValue), '')<br>
				var decryptedResponse= decryptFromServer( encryptedResponse);
			</div>			
			PHP scripts which must decrypt and encrypt must include
			
			<div class="code">	
				&lt;?php include('/your_path/jerrysLibrary.php');
			</div>
			
			The server script decrypts the data sent above like this
			<div class="code">	
				if ( isset( $_GET['handler'])){<br>
				&nbsp;&nbsp;$plaintext= decryptFromClient( $_GET['handler']);<br>
				&nbsp;&nbsp;// do something with $plaintext and generate $response<br>
				&nbsp;&nbsp;echo encryptToClient( $response);<br>
				&nbsp;&nbsp;exit();<br>
				}
			</div>
			
			When passing encrypted data to the server, there is no need to URL encode it, encodeURIComponent().  The encryption library ensures that data uploaded to the server contains only URL safe characters.<br><br>
			
			It is really just that simple.  There is never any need to encodeURIComponent().  All the encryption functions generate only URI safe characters on the uplink side. 
			
			To use these functions you must include this line in the HTML header
			
			<div class="code">	
				&lt;script src="your-server-path/jerrysLibrary.js">&lt;/script>
			</div>
			
			and this line in all php scripts that respond to encrypted messages
			
			<div class="code">	
				&lt;?php include ('your-server-path/jerrysLibrary.php');
			</div>		
			
			<br><br>
			<center><b>A Sample Form Sending and Receiving Encrypted</b></center><br><br>
			
			This sample is an example of both encrypted GET and encrypted POST and of encrypting only the value and of encrypting both the name and value pair.<br><br>
			

			<div class="code">	
				&lt;input type="text" id="testName"><br>
				&lt;input type="password" id="testPass"><br> 
				&lt;input type="checkbox" id="testCheck" onclick= "ge('encResponse').innerHTML= <b>synchttp</b>(( '?testRememberMe='+encryptToServer( checked)), '')"><br> 
				&lt;textarea id="testComment" style="width: 200px; height: 50px;">&lt;/textarea><br><br>
				&lt;button onclick="sendEncryptedForm()"><b>Submit</b>&lt;/button><br><br>

<input type="text" id="testName" > User Name<br>
<input type="password" id="testPass" > Pass Word  
<input type="checkbox" id="testCheck" onclick="makecheckbox()"> remember me<br>
<textarea id="testComment" style="width: 200px; height: 50px;"></textarea><br>
<button onclick="makeform()">Submit</button><br><br>
				
				function makeform(){<br>
				&nbsp;&nbsp;&nbsp;var a= '&testName=' +ge( 'testName').value;<br>
				&nbsp;&nbsp;&nbsp;a+= '&testPass='+ge('testPass').value;<br>
				&nbsp;&nbsp;&nbsp;a+= '&testComment='+ge('testComment').value;<br>
				&nbsp;&nbsp;&nbsp;var enc= encryptToServer( a);<br>
				&nbsp;&nbsp;&nbsp;multihttp( '', ( '&encrypted'+ enc), 'viewResponse');<br>
				}<br><br>
				function viewResponse( a){<br>
					ge('encResponse').innerHTML+= '';<br>		
				}<br>
				<br>


<button style="float: right;" onclick="ge('encResponse').innerHTML= '&nbsp;'">Clear</button>

				&lt;div id="encResponse"><br> 	

<div id="encResponse" style="border: thin black solid; border-radius: 5px; padding: 5px; color: black;">&nbsp;</div>
		
				&lt;/div>
		</div>			
<script>
function makecheckbox(){
	var checked= 'no';
	if ( ge('testCheck').checked){ checked= 'yes';}
	
	var enc= encryptToServer( checked);
	
	var res= synchttp(( '?testRememberMe='+enc), '');
		
	ge('encResponse').innerHTML= decryptFromServer( res);	
}

function makeform(){
	var a= '&testName='+ge('testName').value;
	a+= '&testPass='+ge('testPass').value;
	a+= '&testComment='+ge('testComment').value;
	var enc= encryptToServer( a);
	multihttp( '', ('&encrypted='+ enc), 'viewResponse');
}

function viewResponse( a){
	ge('encResponse').innerHTML= decryptFromServer( unescape( a));		
}
</script>
	
		</div>		
		<!--  end   -->
		
		
		<div class="sec">
			Download, save path and settings
		</div>

		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'downl')">
		<b>Download Instructions</b><br>
		<div id="downl" class="explain" style="display: none;">

			<center><h3>Download Instructions</h3></center><br>

			To use these functions you must download the javascript and PHP libraries<br><br>
			
			<a href="jerrysLibrary.js">jerrysLibrary.js</a><br> 
			<br>
			<a href="jerrysLibrary.php.txt">jerrysLibrary.php</a><br>
			<br>
			save them to your server.  Remember to remove the .txt extension on the file jerrysLibrary.php  <br><br>
			
			Set the path where you saved the files in line 25 of jerrysLibrary.js and set JL_setAjax in line 31 as desired.  'true' if you want to use the AJAX functions.  AJAX must be set to true if cryptography is desired.
			
			<div class="code">
				line 25: var PHPlibpath= 'http://'+window.location.hostname + '/test/jerrysLibrary.php';<br><br> 
				line 31: var JL_setAjax= true;
			</div>
			 
			Set ALLOWXDOMAIN and STOREKEYINSESSION according to your taste.<br><br>
			
		 	Allowing Xdomain is really convenient, but it also opens a certain vulnerability on your server.  Javascript does not allow this because this could be a security issue.  A hacker could send requests to your copy of jerrysLibrary.php having it access web pages to carry out his own nefarious ends.  The web sights accessed would record the IP address of your server not the hacker's.<br><br>
		 	
			This is set true in the downloaded file.

			<div class="code">
				line 20: define ( 'ALLOWXDOMAIN', 'true');  // true / false<br><br>
			</div>
			
			You must include this line in the HTML header of any page using the libraries.
			<div class="code">	
				&lt;script src="your-server-path/jerrysLibrary.js">&lt;/script>
			</div>
			
			and include this line in all php scripts that responds to encrypted messages
			<div class="code">	
				&lt;?php include ('your-server-path/jerrysLibrary.php');
			</div>

		</div>		
		<!--  end   -->
		
		
		<div class="sec">
			AJAX
		</div>

		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'multi')">
		<span style="color: #844">string</span> <b>synchttp</b>( UrlInYourDomainGET, POST )<br>
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'multi')">
		<span style="color: #844">void</span> <b>multihttp</b>( UrlInYourDomainGET, POST, callBackFunctionName )<br>
		<div id="multi" class="explain" style="display: none;">








<!--                synchttp()       multihttp()                         *
 *************************************************************************
*		code example 													-->




			<center><h3>synchttp() &nbsp;&nbsp;&nbsp; multihttp()</h3></center><br>

			These are the two main AJAX functions.<br><br>
			
			Place GET name value pairs in the first argument and POST name value pairs in the second argument.<br><br>
			
			The third argument for multihttp() is the name of a call back function.  When the server responds, the call back function will be called. In the mean time javascript can go on doing what ever else it wants.  Whereas, in synchttp(), javascript is halted until the response and the response is returned as text.  These are not encrypted.  See Cryptography section.
			
			<div class="code">	
				synchhttp( URL, POST)<br><br>
				
				multihttp( URL, POST, 'callBackFunctionName')<br><br>
				
				function callBackFunctionName( response){<br>
				&nbsp;&nbsp;&nbsp;var resp= unescape( response);<br>
				&nbsp;&nbsp;&nbsp;// do anything with resp.  	<br>
				}<br>
			</div>
			
			Try it out.  Since the URL base is our own domain and the server script to which we send this request is the PHP portion of the file for this page, we can simply use '?varName=value' for our GET address. Download 
			<a href="testJerrysLibrary.php.txt">testJerrysLibrary.php</a>, 
			the source file of this page to see the PHP.<br><br> 
			
			Notice that synchhttp() makes javascript wait until the response from the server comes back, while multihttp() can issue multiple calls and do other things.  When the response does come the callback function is called and the response is passed to it escaped.  You write the callback function and pass its name as the third parameter of multihttp(). Always unescape the response in the callback function.<br><br>
			
			The test AJAX server function, found in testJerrysLibrary.php, simply returns the names and values for all GET, POST, COOKIE and SESSION variables that exist.  Notice the pereventCaching GET variable.  It is added by the AJAX library to precent any caching that could be going on at any point along the Internet route.<br><br>
			
			The test AJAX server function also has an intentional delay to showcase the features of multihttp().  In this example a whirl image is placed inside the div until the response arrives.  Of course with synchttp() this would be impossible, because javascript is tied up until the response is received.<br><br>
			
			Click either <b>synchttp(</b> or the <b>multihttp(</b> to try them out.
			
			<div class="code">	

<span onmouseover="ms(2)" onmouseout="ms(1)" onclick="ge('testAJAX').innerHTML= synchttp( '?someGETvar=999', '')">

				document.getElementById('testAJAX').innerHTML= <b>synchttp( '?someGETvar=999', '')</b></span><br><br>


<span onmouseover="ms(2)" onmouseout="ms(1)" onclick="ge('testAJAX').innerHTML= synchttp( '', '&somePOSTvar=777')">
	
				document.getElementById('testAJAX').innerHTML= <b>synchhttp( '', '&somePOSTvar=777')</b></span><br><br>

				
<span onmouseover="ms(2)" onmouseout="ms(1)" onclick="ge('testAJAX').innerHTML='<img src=http://jerrywickey.com/israel/images/whirl.gif>';for(i=1;i<4;i++){multihttp(('?someGETvar='+i), '', 'multihttpCallBackFunctionName')}">
			
<script>
function multihttpCallBackFunctionName( a){
	if (( ''+ge( 'testAJAX').innerHTML).indexOf( 'images/whirl.gif')>-1){
		ge( 'testAJAX').innerHTML= '';
	}
	ge( 'testAJAX').innerHTML+= unescape( a);
}
</script>
				
				document.getElementById('testAJAX').innerHTML='&lt;img src="whirl"><br>
				for ( var i=1; i&lt;3; i++){<br>
				&nbsp;&nbsp;&nbsp<b>;multihttp(( '?someGETvar='+i), '', 'callBackFunctionName' )</b><br>
				}</span><br><br>
				
				function callBackFunctionName( a){<br>
				&nbsp;&nbsp;&nbsp;document.getElementById( 'testAJAX').innerHTML+= unescape( a);<br>
				}<br><br>
				
				&lt;div id="testAJAX">
	

<button style="float: right;" onclick="ge('testAJAX').innerHTML= '&nbsp;'">Clear</button>

<div id="testAJAX" style="border: thin black solid; border-radius: 5px; padding: 5px; color: black;">&nbsp;</div>
				
				&lt;/div>			
			</div>
		</div>
		<!--  end   -->
		
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'mxdom')">
		<span style="color: #844">string</span> 
		<b>Xdomain</b>( anyUrlGET, POST, cookiejar, agent, timeout ) <br>
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'mxdom')">
		<span style="color: #844">void</span> 
		<b>multiXdomain</b>( anyUrlGET, POST, cookiejar, agent, timeout, callBackFunctionName )<br> 
		<div id="mxdom" class="explain" style="display: none;">








<!--               Xdomain()         multiXdomain()                          *
 *************************************************************************
 *		code example 													-->




			<center><h3>Xdomain() &nbsp;&nbsp;&nbsp; multiXdomain()</h3></center><br>

			The two Xdomain functions retrieve the contents of web pages that are not on your domain.<br><br>
			
			Javascript does not allow this because this could be a security issue.  A hacker could send requests to your copy of jerrysLibrary.php having it access web pages to carry out his own nefarious ends.  The web sights accessed would record the IP address of your server not the hacker.  While this function can be convenient, it could also be dangerous.<br><br>
			
			There are three ways to shut down Xdomain access.<br><br>
			
			1) Open the file jerrysLibrary.php and change line number 20 to "define ( 'ALLOWXDOMAIN', 'false')"<br>
			2) Execute the javascript function Xdomain( 'noaccess', '')<br>
			3) Place a file named 'xdomain.txt' with the contents 'no' on your server in the same path as jerrysLibrary.php<br><br>
			
			Any of these three will cause both Xdomain functions to always return false and never access any web page.  The only way to restore access is to delete the file 'xdomain.txt' from your server and restore line 20 to 'true'.<br><br>
			
			Both functions do exactly the same as thing except that multiXdomain does not halt javascript while waiting for the response.  When the response comes it calls the call back function and passes the response as an escaped string to that function. 

			<div class="code">	
				&lt;input id="mxdomainurl" type="text">
<input id="mxdomainurl" type="text" value="http://yahoo.com"> URL<br>

				&lt;input id="mxdomainpost" type="text">
<input id="mxdomainpost" type="text"> send POST data if any <br><br>
			
<span onclick="ge('MXdomain').innerHTML= '<img src=http://jerrywickey.com/israel/images/whirl.gif>';ge('MXdomain').innerHTML= displayCleanPage( Xdomain( ge('mxdomainurl').value, ge('mxdomainpost').value, '', '', ''))" onmouseover="ms(2)" onmouseout="ms(1)">
				<b>ge('MXdomain').innerHTML= "&lt;img src='whirl.gif'>";<br>
				ge('MXdomain').innerHTML= Xdomain( ge('mxdomainurl').value, ge('mxdomainpost').value, '', '', '')</span></b><br><br>	
			</div>
			
			As one sees, even though whirling "working" image is placed in the div that will receive the text, nothing happens.  The computer is frozen until the source code of the page is received.
			
			With multiXdomain() the computer can go on to do other things until the response is received.  The whirling image does show up while during the time the response takes to arrive.  The response which is a the source code of a web page is passed to the callback function when it arrives which places it in the div.<br><br>
			
			<div class="code">
				
<span onclick="	ge('MXdomain').innerHTML= '<img src=http://jerrywickey.com/israel/images/whirl.gif>'; multiXdomain( ge('mxdomainurl').value, ge('mxdomainpost').value, '', '', '', 'mxdomaincallback')" onmouseover="ms(2)" onmouseout="ms(1)">

				<b>ge('MXdomain').innerHTML= "&lt;img src='whirl.gif'>";<br>
				multiXdomain( ( ge('mxdomainurl').value, ge('mxdomainpost').value, '', '', '', 'mxdomaincallback')</b></span> <br><br>
			</div>

			If the response arrived too soon to see the whirl image, put the following URL in the URL input field.  Just click it.<br>
			<div class="code">	
			<span onclick="ge('mxdomainurl').value='http://jerrywickey.com/test/testJerrysLibrary.php?someGETvar=11'"  onmouseover="ms(2)" onmouseout="ms(1)">
			
			<b>getElementById( 'mxdomainurl').value= 'http://jerrywickey.com/test/testJerrysLibrary.php?someGETvar=11'</b></span><br>
			</div>
						
			That is one of the test AJAX server commands and it intentionally delays responding for 1 to 3 seconds.

			<div class="code">	

				<div style="float: right;">
					<button onclick="ge('MXdomain').innerHTML= '&nbsp;'">Clear</button>
				</div>
	
				&lt;div id="MXdomain"> 

<div id="MXdomain" style="border: thin black solid; border-radius: 5px; padding: 5px; color: black;">&nbsp;</div>
				
				&lt;/div><br><br>
				
				function displayCleanPage( a){<br>
				&nbsp;&nbsp;&nbsp;var pagecontents= unescape( a);<br>
				&nbsp;&nbsp;&nbsp;pagecontents= subtute( '&lt;', '&amp;lt;', pagecontents);<br>
				&nbsp;&nbsp;&nbsp;pagecontents= subtute( "\n", '&lt;br>', pagecontents);<br>
				&nbsp;&nbsp;&nbsp;ge('MXdomain').innerHTML= pagecontents;<br>
				}<br>
				<br>
				function mxdomaincallback( a){<br>
				&nbsp;&nbsp;&nbsp;ge('MXdomain').innerHTML= displayCleanPage( a);<br>
				}<br>		
				
<script>
//var randsit= ['yahoo.com', 'msn.com', 'google.com', 'yandex.com'];
//ge( 'mxdomainurl').value= 'http://'+ randsit[ Math.floor( Math.random() * randsit.length)];

function displayCleanPage( a){
	var pagecontents= unescape( a);
	pagecontents= subtute( '<', '&lt;', pagecontents);
	pagecontents= subtute( "\n", '<br>', pagecontents);
	return pagecontents;
}

function mxdomaincallback( a){
	ge('MXdomain').innerHTML= displayCleanPage( a);
}
</script>			
			</div>
		</div>
		<!--  end   -->
		
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'cck')">
		<span style="color: #844">bool</span> <b>setcookie</b>( name, value, time, path, domain ) <br>
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'cck')">
		<span style="color: #844">string</span> <b>getcookie</b>( name ) <br>
		
		<div id="cck" class="explain" style="display: none;">








<!--                   setcookie()  getcookie()                          *
 *************************************************************************
*		code example 													-->




			<center><h3>setcookie()  getcookie()</h3></center><br>

			Set the value or create a cookie on the client from javascript.  You don't have to be too careful with the value types.  They pretty much all get turned into strings, except for in some rare occasions.  Keeping them strings is good practice.<br><br>

			If the cookie doesn't exist, it will be created.  Time is the only important value.  It is relative to now and in seconds.  So you don't have to add to the current timestamp.  A value of 3600 will set the cookie to expire an hour from now.  It is in seconds, not milliseconds. Setting time to zero, erases the cookie.  Path and domain have the same meaning as cookie path and domain.
		
			<div class="code">	
				setcookie( 'nameOf_COOKIE_Variable', 'value', 'time', 'path', 'domain' )<br><br>
				 
				var yourVariable= getcookie( 'nameOf_COOKIE_Variable' ) 
			</div>
			
			Retrieve the value of any COOKIE variable on this client for this domain.  If the variable doesn't exist, it will return NULL string or empty string.

			
			<table width="100%" style="font-size: 1em;">
			<tr><td valign="top"><div class="code">
				&lt;input id="ct1" type="text"><br>
				<input id="ct1" type="text" value="cookiename">

			</div></td><td valign="top"><div class="code">	
				&lt;input id="ct2" type="text"><br>
				<input id="ct2" type="text">

			</div></td></tr>
			</table>	
			
			<div class="code">
				&lt;button onclick="setcookie( ge('ct1').value, ge('ct2').value, 300, '/', '')">

<button onclick="setcookie( ge('ct1').value, ge('ct2').value, 300, '/', ''); alert( 'cookie '+ge('ct1').value+' set to '+ ge('ct2').value+' for five minutes')">Set Cookie</button>

				&lt;/button><br>

				&lt;button onclick="ge( 'ct2').value= getcookie( ge('ct1').value)">

<button onclick="ge( 'ct2').value= getcookie( ge('ct1').value)">Get Cookie</button> 

				&lt;/button> 
			</div>
			
			Make a new cookie name and value.  Place them in their respective text fields.  Set the cookie.  Delete the value but not the name and click Get cookie.  Take note of some actual cookie names from the section on synchttp() and put those names in the name input field then click Get Cookie.<br><br>
			
			A true cookie will retain its value on reload.  Try it.  Just remember that these cookies are set for only five minutes.  Use the function in your own javascript to set it for a different time.
		</div>
		<!--  end   -->
		
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'sck')">
		<span style="color: #844">bool</span> <b>setsession</b>( name, value ) <br>

		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'sck')">
		<span style="color: #844">string</span> <b>getsession</b>( name )<br>

		<div id="sck" class="explain" style="display: none;">








<!--                  setsession()  getsession()                         *
 *************************************************************************
*		code example 													-->




			<center><h3>setsession()  getsession()</h3></center><br>

			Set the value of any SESSION variable on the server from javascript.  If the session variable doesn't exist, it will create it.  You don't have to be too careful with the value types.  They pretty much get turned into strings, except for in some rare occasions.  Keeping them strings is good practice.

			<div class="code">	
				setsession( 'nameOf_SESSION_Variable', 'value')<br><br>
				 
				var yourVariable= getsession( 'nameOf_SESSION_Variable' ) 
			</div>
			
			Retrieve the value of any SESSION variable that the server is keeping for this session.  If the session variable doesn't exist, it will return NULL string or empty string.

			<table width="100%" style="font-size: 1em;">
			<tr><td valign="top"><div class="code">	
				&lt;input id="sct1" type="text"><br>
				<input id="sct1" type="text" value="sessionVariableName">

			</div></td><td valign="top"><div class="code">	
				&lt;input id="sct2" type="text"><br>
				<input id="sct2" type="text">

			</div></td></tr>
			</table>	

			<div class="code">
				&lt;button onclick="setsession( ge('sct1').value, ge('sct2').value)">

<button onclick="setsession( ge('sct1').value, ge('sct2').value); alert('SESSION variable '+ge('sct1').value+ ' set to '+ ge('sct2').value)">Set Session</button>

				&lt;/button><br>
				
				&lt;button onclick="ge( 'sct2').value= getsession( ge('sct1').value)"> 

<button onclick="ge( 'sct2').value= getsession( ge('sct1').value)">Get Session</button> 

				&lt;/button>
			</div>
			Make a new SESSION name and value.  Place them in their respective text fields.  Set the SESSION variable.  Delete the value but not the name and click Get session.  Take note of some actual session variable names from the section on synchttp() and put those names in the name input field then click Get session.<br><br>
			
			A true SESSION variable will retain its value on reload.  Try it.  Just don't close the browser or do something that would cause a new session.
		</div>
		<!--  end   -->
		
		
		<div class="sec">
			Miscellany
		</div>

		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'replacealle')">
		<span style="color: #844">string</span> <b>subtute</b>( replaceThis, withThis, inString )<br>
		<div id="replacealle" class="explain" style="display: none;">








<!--                            subtute()                                *
 *************************************************************************
*		code example 													-->




			<center><h3>subtute()</h3></center><br>

			If you get tired of the limitations of regular expressions, especially if you are working with special characters, this function will replace any string with any other string in any string.<br><br>

			Even "substitute" was too long for me.
			<div class="code">	
				<table width="100%" style="font-size: 1em;">
				<tr><td valign="top"><div class="code">	
					&lt;span id="ts"><br>
<b><span id="ts" style="color: black">ing to trim</span></b><br>
					&lt;/span>
						
				</div></td><td valign="top"><div class="code">
					&lt;input id="ts1" type="text"><br>

<input id="ts1" type="text" value="string to replace" onkeyup="ge('ts').innerHTML= subtute( ge('ts1').value, ge('ts2').value, ge('ts3').value)">

				</div></td><td valign="top"><div class="code">	
					&lt;input id="ts2" type="text"><br>
					
<input id="ts2" type="text" value="with this string" onkeyup="ge('ts').innerHTML= subtute( ge('ts1').value, ge('ts2').value, ge('ts3').value)">

				</div></td><td valign="top"><div class="code">	
					&lt;input id="ts3" type="text"><br>
					
<input id="ts3" type="text" value="in this string" onkeyup="ge('ts').innerHTML= subtute( ge('ts1').value, ge('ts2').value, ge('ts3').value)">

				</div></td></tr>
				</table>	
				<div class="code">
					ge( 'ts').innerHTML=
					subtute( ge( 'ts1').value
					, ge( 'ts2').value
					, ge( 'ts3').value )
				</div>
			</div>
		</div>	
		<!--  end   -->
		
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'trime')">
		<span style="color: #844">string</span> <b>trim</b>( str, trim )<br>
		<div id="trime" class="explain" style="display: none;">








<!--                              trim()                                 *
 *************************************************************************
*		code example 													-->




			<center><h3>trim()</h3></center><br>

			The native javascript function String.trim() works in some browsers and not in others.  trim() in this library works in all browsers old and new.<br>
			<br>
			The second parameter are the characters to be trimmed from both ends.  If an empty string, it trims new line, carriage return space and tab "\n\r\t "
			<div class="code">	

				<table width="100%" style="font-size: 1em;">
				<tr><td valign="top"><div class="code">	
					&lt;span id="tt"><br>
<b><span id="tt" style="color: black">ing to trim</span></b><br>
					&lt;/span>
						
				</div></td><td valign="top"><div class="code">
					&lt;input id="tt1" type="text"><br>

<input id="tt1" type="text" value="string to trim" onkeyup="ge('tt').innerHTML= trim(ge('tt1').value, ge('tt2').value)">

				</div></td><td valign="top"><div class="code">	
					&lt;input id="tt2" type="text"><br>
					
<input id="tt2" type="text" value="of these characters" onkeyup="ge('tt').innerHTML= trim(ge('tt1').value, ge('tt2').value)">

				</div></td></tr>
				</table>	
				<div class="code">
					ge( 'tt').innerHTML=
					trim( ge( 'tt1').value
					, ge( 'tt2').value )
				</div>
			</div>
		</div>
		<!--  end   -->
		
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'eke')">
		<span style="color: #844">bool</span> <b>ekey</b>( this.event)<br>
		<div id="eke" class="explain" style="display: none;">








<!--                            ekey( no_bits )                          *
 *************************************************************************
*		code example 													-->




			<center><h3>ekey()</h3></center><br>


			Returns true if the enter key was pressed.  An example of use:<br><br>
			
			<div class="code">	

<input type="text" onkeyup="if ( ekey( this.event)){ alert( 'The enter key was pressed.  The value entered is \n\n' +this.value); }">

				<br>
				&lt;input type="text" onkeyup="if ( ekey( this.event)){ alert( 'The enter key was pressed.  The value entered is ' +this.value); }">
				
			</div>
		</div>
		<!--  end   -->
		
		
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'getxe')">
		<span style="color: #844">object</span> <b>ge</b>( str )<br>
	
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'getxe')">
		<span style="color: #844">void</span> <b>ms</b>( pointer )<br>
			
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'getxe')">
		<span style="color: #844">int</span> <b>getxpos</b>( obj )<br>
	
		<img src="http://jerrywickey.com/images/expand.gif" class="ex" onmouseover="ms(2)" onmouseout="ms(1)" onclick="expand( this, 'getxe')">
		<span style="color: #844">int</span> <b>getypos</b>( obj )<br>
		<div id="getxe" class="explain" style="display: none;">






<!--               ge()   ms()   getxpos()   getypos()                   *
 *************************************************************************
*		code example 													-->




			<center><h3>ge() &nbsp;&nbsp;&nbsp;  ms() &nbsp;&nbsp;&nbsp; getxpos() &nbsp;&nbsp;&nbsp; getypos()</h3></center><br>
			
			ms( mouse_pointer) is a short cut for document.body.style.cursor<br>			
			2 is 'hand'<br>
			1 is 'normal pointer'<br>
			Use any valid mouse pointer name in addition to 1 and 2<br>
			
			<div class="code">	
				&lt;span onmouseover="ms(2)" onmouseout="ms(1)">

<span onmouseover="ms(2)" onmouseout="ms(1)"><b>Change the cursor on mouse over</b></span>

				&lt;/span>
			</div><br>
			
			ge( element_name ) is a short cut for document.getElementById<br>
			getxpos( element ) returns the x position of the element<br>
			getypos( element ) returns the y position of the element<br><br>
			
			<div class="code">	
				&lt;span id="testmo">
				
				<b><span id="testmo"></span></b>
				
				&lt;/span><br>
				<br>
				&lt;span onmouseover="ge( 'testmo').innerHTML= getxpos( ge( 'testmo'))+ ' x pos'"<br>
				&nbsp;&nbsp;&nbsp;onmouseout=" ge( 'testmo').innerHTML= getypos( ge( 'testmo'))+ ' x pos'" ><br><br>
				

<span onmouseover=" ge( 'testmo').innerHTML= getxpos( ge( 'testmo'))+ ' x pos'" onmouseout=" ge( 'testmo').innerHTML= getypos( ge( 'testmo'))+ ' y pos'">


				<b>Mouse over to show the x pos of this</b>
				</span><br><br>
				
				&lt;/span>
			</div>
		</div>
		<!--  end   -->
		
		

		<a name="tocom">&nbsp;</a><br>
		<div style="float: right; width: 50%; margin-left: 20px; font-size: 0.9em; color: #446;">
			<center><i><b><h3>- Comments -</h3></b></i></center>
			<hr>
			<input id="commentname" type="text" value="<?php echo $_COOKIE['postname']; ?>" style="position: relative; left: 2px; width: 25%; border-radius: 5px;">
			&nbsp;Noms de plume<br>
			
			<input id="commentfollow" type="text" value="<?php echo $_COOKIE['postfollow']; ?>" style="position: relative; left: 2px; width: 25%; border-radius: 5px;">
			&nbsp;Enter your email,  
			<span style="font-size: 0.7em; color: #88a;">
			if you want to follow this conversation. Your email is never shared.</span><br>
			
			<div style="position: relative; margin-top: 5px; width: 100%;">		
				<textarea id="commentcontent" style="width: 50%; height: 4em; border-radius: 5px; border: #aaa thin solid;"></textarea>
				
				<div style="position: absolute; left: 55%; bottom: 0px;">
					<button onclick="submitcomment( ge('commentname').value, ge('commentcontent').value, ge('commentfollow').value)">Post</button> your comment
				</div>
				
				<img id="postcommentsw" src="http://jerrywickey.com/israel/images/whirl.gif" width="30" height="30" style="position: absolute; bottom: 10%; right: 10%; display: ;">

			</div>
			<hr>
			<div id="postcomments">
				<br><br>
			</div>
			<br><br>
		</div>		


		<b><center><i><h3>Any web page can easily employ robust encryption</h3></i>
		Say Good-by to Captcha<br><br>
		Say Goodbye to NSA spying
		</center></b><br>
	
		This article is for programmers -to encourage and help them in the ubiquitous use of robust encryption.<br><br>
	
		Encrypt all AJAX data to and from the server with this free and entirely javascript and PHP library.  There are many reasons for ubiquitous encryption other than data security itself.  The complete and total elimination of Captcha for one.  Look at the source code for the comments section on this page to see why the comments on this page are safe from auto post robots without any sort of Captcha what-so-ever. <br><br>
		  
		No installation needed.  No java applet client download.  No SSL.  No security certificates.  Not even JQuery is needed.  It is a clean, short and simple library that in addition to Encryption also provides AJAX functions including get and set session and cookie.  Simply download the jerrysLibrary.js and jerrysLibrary.php libraries.  Save them to your server and reference them in your code.<br><br>
		
		This page demonstrates all the functions which the library provides.  And the source code of this page provides direct examples of the use of each.  You'll find the code examples clearly marked in the source.  This library was expressly written for ease of use.<br><br>		
		<center><i><b><h3>Given ever increasing security concerns,</h3></b></i></center><br> 
		
		I have decided to add RSA encryption to all client/server communications on my websites.  Everything I write from now on will employ rigorous and ubiquitous encryption.  Everything! not just passwords, but the contents of even every AJAX call.  <br><br>
		
		<center><i><b><h3>Salute to Edward Snowden</h3></b></i></center><br>
		
		This is about more than commercial and banking security.  Regardless of the fact that the NSA is probably uninterested in my particular web traffic, making snooping technically infeasible makes the world a better and safer place for free thinkers and for the exchange of political ideas and ensures the free exchange of ideas without fear that someone, somewhere will decide that this or that information should not be available to the public.  So, encryption is not just about keeping your data safe from hackers, but it is about making the world a better place to live.<br><br>
		
		Encrypting only important documents, merely identifies that document as worth devoting computational time.  Encrypting everything forces everything to be decrypted to determine which documents are important.<br><br>
		
		<center><i><b><h3>The library design goal</h3></b></i></center><br> 
	
		To make encryption easy for web developers, I wrote a javascript and PHP library and offer it to everyone for their use.  I encourage the proliferation of robust encryption technology to keep everyone honest.  And offer this library of easy to use functions to everyone everywhere.  I offer it's use free of charge.  I offer it for alteration, augmentation, bug fixes, or any other changes.<br><br>
	
		This javascript and PHP library endows even tablets and smart phones with full 2048 bit RSA encryption in four very easy to use javascript functions.  Plus the library provides 13 other handy functions for AJAX and to make programming easier.<br><br>
	
		The library is written and utilizes PHP and javascript only.  This keeps it easy to use and implement.  No user has to download software and install it.  Everything can be done with javascript and PHP.<br><br>
		
		Since cryptography also requires AJAX and some other functions that make it all go easier, all the functions are explained and available here for your use.  Please use them freely.<br><br>
	
		<center><i><b><h3>Design considerations</h3></b></i></center><br> 
		
		Upon the initial client http request, the server begins to generate vary large prime numbers in anticipation. The client passes the results of a javascript speed test to the server in its initial handshake.  The server generates as strong an RSA key set as possible for the given speed test.<br><br>
		
		The server and client then use the RSA asymmetrical key set to build up a robust RC4 symmetrical key over multiple steps.  Finally there is a final handshake where the server and client confirm to each other that they are indeed the same machines which first began the negotiation.<br><br>
		
		This is done by decrypting a message and comparing it with the original handshake message.   Re-encrypting it and passing it back confirms that the server and client are in fact the very same which began the key negotiations a few seconds ago.  This Guarantees that no hacker has hijacked the handshaking.  Communication then continues for the session with the secure RC4 key at which the server and client arrived.<br><br>
		
		The message or safe word which persists throughout the communication session is contained in the javascript variable JL_cryname.  The hash of this randomly generated message is the name of a file on the server which contains all the pertinent key information.  Server SESSION variables are not used because they slow the server down and because using the hash of the passed message disassociates any obvious connection between session files on the server and http requests.<br><br>
		
		The challenge for the server is generating large prime numbers.  A 2000 bit prime number can take as much as two minutes to generate in pure PHP.  I solved this simply by having the client make multiple http requests under the same JL_cryname.  This employs many instances of the same prime number generator which dumps their results into the same pool.<br><br>
		
		The client javascript is challenged by the bcmath needed to encrypt in RSA<br><br>
	
		To get things to work on the javascript client I looked for a bcmath package for javascript.  I found a good one but it was still too slow.  I ended up writing my own from scratch.  By ignoring negative numbers, floating point and validation of the arguments, I got it to work better than twice as fast as the best package I looked at and kept is completely javascript.<br><br>
			
	</div>
	
	<div id="secureComIcon"></div>
	
	<div id="linkmess" style="position: absolute; top: 100px; left: 100px; right: 100px; border: red 2px solid; border-radius: 10px; padding: 50px; opacity: 0.6; background: white; display: <?php if (strlen($message)==0){echo 'none';} ?>;">
		<div style="float: right; color: red; font-size: 1.5em;" onclick="ge('linkmess').style.display='none'" onmouseover="ms(2)" onmouseout="ms(1)">
			<b>X</b>
		</div>
		<?php if ( strlen( $message) > 0){ echo $message; } ?>	
	</div>
	
</div>
</body>
</html>

<script>




function cryptoStarted( JL_crybits, JL_cryname, JL_rc4keys){
	submitcomment( 'none', 'page-init', 'none');
	var t= "JL_cryptcomm(( "+JL_crybits+"+'-bit Secure Channel Established'), 8, 'blink')";
	setTimeout( t, 1000);
}

function cryptoUnavalable( JL_crybits, JL_cryname, JL_rc4keys){
	ge('comments').innerHTML= 'A secure channel is not available';
	ge('followers').innerHTML= 'A secure channel is not available';
}




function reportpost( id){
	multihttp(( '?abuse='+ encryptToServer( id)), '', 'alert');
}

function submitcomment( name, text, follow){
	var tl= trim( text,'').length;
	var nl= trim( name,'').length 
	if (( tl > 0 && nl == 0) || ( tl == 0 && nl > 0)){
		alert( 'No name or no text was entered');
		return false;
	} 
	ge('postcommentsw').style.display= '';
	var m= encryptToServer( name+",\t-"+text+",\t-"+follow);
	multihttp( '', ('&submitcomment=' +m), 'respondetosubmit');
}
	
function respondetosubmit( a){
	// decrypt response
	var r= decryptFromServer( unescape( a));
	// display response
	if ( r.indexOf( ' init -->')==-1){
		r= r + ge('postcomments').innerHTML;
	}
	ge('postcomments').innerHTML= r;
	ge('postcommentsw').style.display= 'none';
	ge('commentcontent').value= '';
	// count the number of comments
	var c= 'Be the first to comment!';
	if ( r.indexOf( 'class="comment"') > -1){
		var t= r.split( 'class="comment"');
		c= ( t.length - 1) +' comment';
		if ( parseInt( c) > 1){ c+= 's'; }
	}
	ge('comments').innerHTML= c;
	// retreive the number following
	c= '';
	var f= parseInt( r.substring( 5));
	if ( f>0){ c= f+' following'; }
	ge('followers').innerHTML= c;
}

function expand( obj, par){
	if (( ge( par).style.display+'').indexOf('none')>-1){
		obj.src= 'http://jerrywickey.com/images/xexpand.gif';
		ge( par).style.display= '';				
	}else{
		obj.src= 'http://jerrywickey.com/images/expand.gif';
		ge( par).style.display= 'none';		
	}
}

function wowf(a){
	ge('wow').style.color= wowc[a];
	a--;
	if (a<0){ a= wowc.length-1; }
	setTimeout(('wowf('+a+')'), 100);
}

var wowc= ['#f44', '#f44', '#f44', '#f44', '#f44', '#f44', '#f55', '#f66', '#f77', '#f88', '#f99', '#faa', '#fbb', '#fcc', '#fdd', '#fee', '#fdd', '#fcc', '#fbb', '#faa', '#f99'];

setTimeout("wowf(0)", 5000);
</script>
