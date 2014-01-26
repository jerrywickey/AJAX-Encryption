AJAX-Encryption
===============
Library for easy encryption of all AJAX data communication between client and server on any web page.  Library is javascript and PHP

Link to http://jerrywickey.com/test/viewJerrysLibrary.php to view the library in action and for instruction and explaination.  The page source code gives full examples of each funtion.

Javascript functions
bool   initCrypto( numberOfBits);  string encryptToServer( str);  string decryptFromServer( str);  bool   setcookie( aname, avalue, time, path, adomain);  string getcookie( aname);  bool   setsession( aname, avalue);  string getsession( aname);  string synchttp( where, post);  bool   multihttp( where, post, callBackFunctionName);  string Xdomain( aurl, post, cookiejar, agent, timeout);  bool   multiXdomain( aurl, post, cookiejar, agent, timeout, callBackFunctionName)

PHP functions
string decryptFromClient( $str);  string encryptToClient( $str);  string RC4crypt( $str, $keyFileName);  string RSAencrypt( $num, $keyFileName);  string RSAdecrypt( $num, $keyFileName)
