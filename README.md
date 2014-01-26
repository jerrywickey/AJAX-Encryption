AJAX-Encryption
===============
Library for easy encryption of all AJAX data communication between client and server on any web page.  Library employs javascript and PHP

Link to http://jerrywickey.com/test/viewJerrysLibrary.php to view it in action and for instruction and explaination.  The file source code gives full examples of each funtion.

Javascript Cryptography functions
bool   initCrypto( numberOfBits)
string encryptToServer( str)
string decryptFromServer( str)

Javascript AJAX functions
bool   setcookie( aname, avalue, time, path, adomain)
string getcookie( aname)
bool   setsession( aname, avalue)
string getsession( aname)
string synchttp( where, post)
bool   multihttp( where, post, callBackFunctionName)
string Xdomain( aurl, post, cookiejar, agent, timeout)
bool   multiXdomain( aurl, post, cookiejar, agent, timeout, callBackFunctionName)

PHP Cryptography functions
string decryptFromClient( $str)
string encryptToClient( $str)
string RC4crypt( $str, $keyFileName)
string RSAencrypt( $num, $keyFileName)
string RSAdecrypt( $num, $keyFileName)
