<?php

// Command Line Calls only
if( php_sapi_name() == 'cli' )
	chdir(__DIR__);
		
DEFINE( 'HOST_NAME', 'hack.nfuseweb.com' );
ini_set('default_charset', 'utf-8');
include_once('functions.php');

// autoloader
spl_autoload_register(function($classname) {
	$filename = $classname.'.php';

	if( file_exists( $filename ) )
		include $filename;
});

$url = val( $_SERVER, 'REQUEST_URI' );
$strippedURL = current(explode('?', $url));
$urlParams = explode('/', $strippedURL);

if( isset( $urlParams[0] ) && $urlParams[0] == '' )
	unset($urlParams[0]);

Database::initialize();

// process message queues
if( ( php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']) ) || urlParam( 1 ) == 'cron' )
{
	include 'cron.php';
	exit;
}

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
		'start' => val( $_POST, 'start-date' ) . ' ' . val( $_POST, 'start-time' ),
		'end' => val( $_POST, 'end-date' ) . ' ' . val( $_POST, 'end-time' ),
		'timeRange' => array(
			'start-date' => val( $_POST, 'start-date' ),
			'start-time' => val( $_POST, 'start-time' ),
			'end-date' => val( $_POST, 'end-date' ),
			'end-time' => val( $_POST, 'end-time' ) ),
		'attendeeNames' => val( $_POST, 'attendeeNames' ),
		'attendeeEmails' => val( $_POST, 'attendeeEmails' ),
		'attendeePhones' => val( $_POST, 'attendeePhones' ),
		'creatorName' => val( $_POST, 'creatorName' ),
		'creatorEmail' => val( $_POST, 'creatorEmail' ),
		'creatorPhone' => val( $_POST, 'creatorPhone' ),
		'narrowToOne' => val( $_POST, 'narrowToOne' )
	);
	
	// create the meeting
	if( Meeting::create( $data ) )
	{
		// success
	}
	else
	{
		// error
	}
	
	include 'home.php';
break;
// display the home page
default:
	$data = array(
		'name' => '',
		'length' => '',
		'timeRange' => array(
			'start-date' => date( 'm/d/Y', time() ),
			'start-time' => date( 'h:i a', time() ),
			'end-date' => date( 'm/d/Y', strtotime( '+1 week' ) ),
			'end-time' => date( 'h:i a', strtotime( '+1 week' ) ) ),
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