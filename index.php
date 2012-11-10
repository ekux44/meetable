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
	$data = array(
		'name' => '',
		'length' => '',
		'timeRange' => array(),
		'attendeeNames' => array( 'jared' ),
		'attendeeEmails' => array('jared@nfuseweb.com'),
		'attendeePhones' => array('9186051721'),
		'creatorName' => '',
		'creatorEmail' => '',
		'creatorPhone' => '',
		'narrowToOne' => 0
	);

	include 'home.php';
break;
}