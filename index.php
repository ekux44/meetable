<?php

include_once('functions.php');

$url = $_SERVER['REQUEST_URI'];
$strippedURL = current(explode('?', $url));
$urlParams = explode('/', $strippedURL);

if( isset( $urlParams[0] ) && $urlParams[0] == '' )
	unset($urlParams[0]);
	
echo urlParam( 1 );