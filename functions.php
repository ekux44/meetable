<?php

function redirect ($page)
{
	ob_end_clean();
	if( substr( $page, 0, 7 ) != 'http://' && substr( $page, 0, 8 ) != 'https://' )
	{
		$page = $_SERVER['HTTP_HOST'] . dirname ($_SERVER['PHP_SELF']) . '/' . urldecode( $page ); // removed basename()
		$page = urlPrefix() . preg_replace('/\/{2,}/','/',$page);
	}
	header ("Location: " . $page);
	exit();
}

function val( $a = array(), $k = '' )
{
	return (isset( $a[ $k ] )) ? $a[$k] : null;
}

function requestVar( $name, $get = true, $post = true, $session = false, $cookie = false )
{
	$return = null;
	if( $get && isset( $_GET[ $name ] ) )
		$return = $_GET[ $name ];
	else if( $post && isset( $_POST[ $name ] ) )
		$return = $_POST[ $name ];
	else if( $session && isset( $_SESSION[ $name ] ) )
		$return = $_SESSION[ $name ];
	else if( $cookie && isset( $_COOKIE[ $name ] ) )
		$return = $_COOKIE[ $name ];		
		
	return $return;
} // requestVar

function what()
{
	return requestVar( 'what', true, true );
} // what

function curPageURL() {
	$pageURL = 'http';
	if (isset( $_SERVER['HTTPS'] ) && $_SERVER["HTTPS"] == "on") 
		$pageURL .= "s";
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80")
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].'/'.$_SERVER["REQUEST_URI"];
	else
		$pageURL .= $_SERVER["SERVER_NAME"].'/'.$_SERVER["REQUEST_URI"];
	
	return $pageURL;
}

function toBytes($str){
	// normalize and strip off any b's
	$str = str_replace( 'b', '', strtolower(trim($str)));
	// last letter
	$last = $str[strlen($str)-1];
	// get the value
	$val = substr( $str, 0, strlen($str) - 1 );
	switch($last) {
		case 't': $val *= 1024;
		case 'g': $val *= 1024;
		case 'm': $val *= 1024;
		case 'k': $val *= 1024;
	}
	return $val;
}

function formatNumberAbbreviation($number, $decimals = 1)
{
	if( $number == 0 )
		return "0";
		
	if( $number < 0 )
		return $number;
		
    $abbrevs = array(
    	24 => "Y",
    	21 => "Z",
    	18 => "E",
    	15 => "P",
    	12 => "T",
    	9 => "B",
    	6 => "M",
    	3 => "K",
    	0 => ""
    );

    foreach($abbrevs as $exponent => $abbrev)
    {
        if($number >= pow(10, $exponent))
        {
        	$remainder = $number % pow(10, $exponent) . ' ';
        	$decimal = ($remainder > 0) ? round( round( $remainder, $decimals ) / pow(10, $exponent), $decimals ) : '';
            return intval($number / pow(10, $exponent)) + $decimal . $abbrev;
        }
    }
}

//from php.net user comments 
function set_cookie_fix_domain($Name, $Value = '', $Expires = 0, $Path = '', $Domain = '', $Secure = false, $HTTPOnly = false)
{
	if (!empty($Domain))
	{
	  // Fix the domain to accept domains with and without 'www.'.
	  if (strtolower(substr($Domain, 0, 4)) == 'www.')  $Domain = substr($Domain, 4);
	  $Domain = '.' . $Domain;
 
	  // Remove port information.
	  $Port = strpos($Domain, ':');
	  if ($Port !== false)  $Domain = substr($Domain, 0, $Port);
	}
 
	header('Set-Cookie: ' . rawurlencode($Name) . '=' . rawurlencode($Value)
						  . (empty($Expires) ? '' : '; expires=' . gmdate('D, d-M-Y H:i:s', $Expires) . ' GMT')
						  . (empty($Path) ? '' : '; path=' . $Path)
		. (empty($Domain) ? '' : '; domain=' . $Domain)
		. (!$Secure ? '' : '; secure')
		. (!$HTTPOnly ? '' : '; HttpOnly'), false);
}

function guid( )
{
	if( function_exists( 'com_create_guid' ) )
		return trim( '{}', com_create_guid() );
	else
	{
		// mt_srand( (double)microtime() * 10000 ); optional for php 4.2.0+
		$charid = strtoupper( md5( uniqid( rand( ), true ) ) );
		// chr(45) = "-"
		$uuid = //chr(123)// "{"
				substr($charid, 0, 8).chr(45)
				.substr($charid, 8, 4).chr(45)
				.substr($charid,12, 4).chr(45)
				.substr($charid,16, 4).chr(45)
				.substr($charid,20,12);
				//.chr(125);// "}"
		return $uuid;
	}
}

function print_pre($item)
{
	echo '<pre>';
	print_r($item);
	echo '</pre>';
}

function unsetSessionVar( $param )
{
	unset( $_SESSION[ $param ] );
}

function urlParam( $id )
{
	global $urlParams;
	return (isset($urlParams[$id])) ? $urlParams[$id] : null;
}