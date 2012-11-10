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
// new meeting
case 'new':
	$data = array(
		'name' => val( $_POST, 'name' ),
		'length' => val( $_POST, 'length' ),
		'timeRange' => array( 'start' => val( $_POST, 'start' ), 'end' => val( $_POST, 'end' ) ),
		'attendeeNames' => val( $_POST, 'attendeeNames' ),
		'attendeeEmails' => val( $_POST, 'attendeeEmails' ),
		'attendeePhones' => val( $_POST, 'attendeePhones' ),
		'creatorName' => val( $_POST, 'creatorName' ),
		'creatorEmail' => val( $_POST, 'creatorEmail' ),
		'creatorPhone' => val( $_POST, 'creatorPhone' ),
		'narrowToOne' => val( $_POST, 'narrowToOne' )
	);
	
	include 'home.php';
break;
// display the home page
default:
	$data = array(
		'name' => '',
		'length' => '',
		'timeRange' => array( 'start' => time(), 'end' => strtotime( '+1 week' ) ),
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