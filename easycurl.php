<?php 
// development version of easycurl.php, by slavik, latest version at https://github.com/avirus/easyphp-curl
// function ASKHOST
// input: 
// $url - URL
// array $options_array
// post_data - post data 
// auth_data - "user:password"
// cert_pem - certificate pem file path
// cert_pwd - certificate password
// cookie - cookie in format "name1=value1; name2=value2"
// timeout - timeout in milliseconds 
// headers - http headers
// debug - true, returns array(data,headers,http_code,d,curl_info_array) / false - returns only data
// proxy - proxy string 1.2.3.208:3128
// output: data, or array(data,http_code,d,curl_info_array)
// curl_info_array -=> http://php.net/manual/ru/function.curl-getinfo.php

function askhost($url, $options_array=array()) {
    $proxystring="";
    $srvd=FALSE;
    $srvauth="";
    $certpem="";
    $certpwd="1";
    $tmoutms = 60000;
    $headers="";
    $httpcode_needed=false;
    $cookie;
    
    if (isset($options_array["post_data"])) $srvd=$options_array["post_data"];
    if (isset($options_array["proxy"])) $proxystring=$options_array["proxy"];
    if (isset($options_array["post_data"])) $srvd=$options_array["post_data"];
    if (isset($options_array["auth_data"])) $srvauth=$options_array["auth_data"];
    if (isset($options_array["cert_pem"])) $certpem=$options_array["cert_pem"];
    if (isset($options_array["cert_pwd"])) $certpwd=$options_array["cert_pwd"];
    if (isset($options_array["timeout"])) $tmoutms=$options_array["timeout"];
    if (isset($options_array["headers"])) $headers=$options_array["headers"];
    if (isset($options_array["debug"])) $httpcode_needed=$options_array["debug"];
    if (isset($options_array["cookie"])) $cookie=$options_array["cookie"];
    
	$fp=curl_init();
	$verbose = fopen('php://temp', 'rw+');
	if (0!=strlen($srvauth)) {
		curl_setopt($fp, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($fp, CURLOPT_USERPWD, $srvauth);
	}
	if (FALSE!==$srvd) {
		curl_setopt($fp, CURLOPT_POST, TRUE);
		@curl_setopt($fp, CURLOPT_POSTFIELDS, $srvd);
	}
	if (0!=strlen($certpem)) {
		curl_setopt($fp, CURLOPT_SSLCERT, $certpem);
		curl_setopt($fp, CURLOPT_SSLCERTPASSWD, $certpwd);
		curl_setopt($fp, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($fp, CURLOPT_SSL_VERIFYHOST, FALSE);
	}
	if (0!=strlen($proxystring)) {
	    curl_setopt($fp, CURLOPT_PROXY, $proxystring);
	}	
	if (0!=strlen($cookie)) {
	curl_setopt($fp, CURLOPT_COOKIE, $cookie);
	}
	curl_setopt($fp, CURLOPT_URL, $url);
	curl_setopt($fp, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($fp, CURLOPT_NOPROGRESS, TRUE);
	@curl_setopt($fp, CURLOPT_TIMEOUT, ($tmoutms/1000));
	@curl_setopt($fp, CURLOPT_CONNECTTIMEOUT_MS, $tmoutms);
	if (""!=$headers) curl_setopt($fp, CURLOPT_HTTPHEADER,$headers);
	curl_setopt($fp, CURLOPT_VERBOSE, TRUE);
	curl_setopt($fp,CURLOPT_STDERR, $verbose);
		//      curl_setopt($fp, CURLOPT_CERTINFO, TRUE);
	curl_setopt($fp, CURLOPT_HEADER,TRUE);
	$data = curl_exec($fp);
	list($header, $body) = explode("\r\n\r\n", $data, 2);
	$httpcode = curl_getinfo($fp, CURLINFO_HTTP_CODE);
	$cinfo=curl_getinfo($fp,1);
	curl_close($fp);
	rewind($verbose);
	$verb=stream_get_contents($verbose);
	//!rewind($verbose)
	if ($httpcode_needed) return array("data"=>$body,"headers"=>$header, "httpcode"=>$httpcode, "d"=>$verb, "curl_info_array"=>$cinfo);
	return $body; 	//otherwise
};
?>
