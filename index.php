<?php

// Command Line Calls only
if( php_sapi_name() == 'cli' )
	chdir(__DIR__);

// Configuration		
DEFINE( 'HOST_NAME', 'meetable.io' );
DEFINE( 'SITE_TITLE', 'Meetable' );
DEFINE( 'SMTP_FROM_ADDRESS', 'no-reply@meetable.io' );
DEFINE( 'SMTP_USERNAME', 'meetable' );
DEFINE( 'SMTP_PASSWORD', '7nHHCkavhtv' );
DEFINE( 'SMTP_PORT', 587 );
DEFINE( 'SMTP_HOST', 'smtp.sendgrid.net' );

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
// sms response
case 'sms':	
	// TODO: sanitize phone numbers
	$from = val( $_REQUEST, 'From' );
	$to = val( $_REQUEST, 'To' );
	
	// figure out if the message is legitimate and what meeting it belongs to
	if( $info = Database::select(
		'Attendees JOIN Users ON id = user',
		'meeting,user',
		array(
			'where' => array(
				'user = id',
				'smsFrom' => $to,
				'phone' => $from ),
			'singleRow' => true ) ) )
	{
		$body = val( $_REQUEST, 'Body' );
		
		/* add the response to the message queue */
		$ironmq = new IronMQ();
		
		// put the message on the queue
		$ironmq->postMessage("responses", array(
			'body' => json_encode( array(
				'meeting' =>  $info['meeting'],
				'user' => $info['user'],
				'response' => $body ) ) ) );
	}
	
	// return no message
	header("content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8" ?><Response></Response>';
break;
// e-mail response
case 'email':

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