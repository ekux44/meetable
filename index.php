<?php

DEFINE( 'HOST_NAME', 'hack.nfuseweb.com' );
ini_set('default_charset', 'utf-8');
include_once('functions.php');

$url = $_SERVER['REQUEST_URI'];
$strippedURL = current(explode('?', $url));
$urlParams = explode('/', $strippedURL);

if( isset( $urlParams[0] ) && $urlParams[0] == '' )
	unset($urlParams[0]);

require 'Database.php';
Database::initialize();

switch( urlParam( 1 ) )
{
// api call
case 'api':
	$version = urlParam( 2 );
	print_pre('api call - version ' . $version);
break;
// display the home page
default:
	include 'home.php';
break;
}