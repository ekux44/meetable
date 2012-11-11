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

$success = false;
$error = false;

switch( urlParam( 1 ) )
{
// api call
case 'api':
	$version = urlParam( 2 );
	
	switch( urlParam( 3 ) )
	{
	case 'meeting':
		switch( urlParam( 4 ) )
		{
		case 'new':
			$decode = json_decode(val($_POST,'data'),true);
			// convert objects to array
			
			$decode['attendeeNames'] = array();
			$decode['attendeeEmails'] = array();
			$decode['attendeePhones'] = array();
			
			foreach( $decode['attendees'] as $a )
			{
				$decode['attendeeNames'][] = $a[ 'name' ];
				$decode['attendeeEmails'][] = $a[ 'email' ];
				$decode['attendeePhones'][] = $a[ 'phone' ];
			}

			$decode['creatorName'] = $decode['creator']['name'];
			$decode['creatorEmail'] = $decode['creator']['email'];
			$decode['creatorPhone'] = $decode['creator']['phone'];
			
			// create the meeting
			if( Meeting::create( $decode ) )
			{
				echo 'success';
			}
			else
			{
				echo $error;
			}
		break;
		}
	break;
	}
break;
// sms response
case 'sms':	
	// sanitize phone numbers
	$from = val( $_REQUEST, 'From' );
	if( !Validate::phone( $from ) )
		exit;
	$to = val( $_REQUEST, 'To' );
	if( !Validate::phone( $to ) )
		exit;
	
	// figure out if the message is legitimate and what meeting it belongs to
	if( $info = Database::select(
		'Attendees JOIN Users ON id = user',
		'meeting,user',
		array(
			'where' => array(
				'user = id',
				'smsFrom' => $to,
				'phone' => $from,
				'active' => 1 ),
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
				'response' => $body,
				'method' => 'phone' ) ) ) );
	}
	
	// return no message
	header("content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8" ?><Response></Response>';
break;
case 'testDates':
	$meeting = new Meeting( 33 );

	$testCases = array(
		1 => array( // NOTE: this test does not work after 11:30 PM
			'start' => strtotime( '+30 minutes' ),
			'end' => strtotime( '+45 minutes' ) ),
		2 => array(
			'start' => strtotime( '+1 day' ),
			'end' => strtotime( '+1 day, 30 minutes' ) ),
		3 => array( // NOTE: this test does not work after 11:30 PM
			'start' => strtotime( '+2 days' ),
			'end' => strtotime( '+2 days, 30 minutes' ) ),
		4 => array(
			'start' => strtotime( '+2 days' ),
			'end' => strtotime( '+3 days, 30 minutes' ) ),
		5 => array( // NOTE: this test does not work after 11:30 PM
			'start' => strtotime( '+8 days' ),
			'end' => strtotime( '+8 days, 30 minutes' ) ),
		6 => array( // NOTE: does not work when < 10 days left in month
			'start' => strtotime( '+8 day' ),
			'end' => strtotime( '+10 days' ) ),
		7 => array( // NOTE: this test does not work after 11:30 PM
			'start' => strtotime( '+32 days' ),
			'end' => strtotime( '+32 days, 30 minutes' ) ),
		8 => array( // NOTE: this test does not work when there are < 34 days left in year
			'start' => strtotime( '+32 days' ),
			'end' => strtotime( '+34 days' ) ),
		9 => array(
			'start' => strtotime( '+2 years' ),
			'end' => strtotime( '+2 years, 30 minutes' ) ),
		10 => array(
			'start' => strtotime( '+2 years' ),
			'end' => strtotime( '+3 years' ) ) );
	
	foreach( $testCases as $case => $range )
	{
		echo "Testing case $case<br />";
		echo "Result: " . $meeting->humanReadableRange( $range ) . '<br />';
	}
	
	$testCases = array(
		'4',
		'3:30',
		'5:26',
		'10:30 am',
		'4pm',
		'4pm pst',
		'friday',
		'friday 4pm',
		'friday 4pm pst',
		'november 9',
		'nov 9 4pm',
		'november 9 5pm',
		'november 9 5pm pst',
		'november 9, 2014',
		'november 9, 2014 5pm',
		'november 9, 2014 6pm pst',
		'4pm to 5pm',
		'penis',
		'blah',
		'is this a time?'
	);
	
	foreach( $testCases as $time )
	{
		echo "Testing case $time " . (($meeting->isValidTime( $time )) ? '<strong>succeeds</strong>' : '<em>fails</em>') . '<br />';
		echo "Resulting timestamp is " . $meeting->humanReadableRange( $meeting->generateTimeRange( $time ) ) . '<br /><br />';
	}	
	
break;
// e-mail response
case 'email':

break;
// new meeting
case 'new':
	$data = array(
		'name' => val( $_POST, 'name' ),
		'length' => val( $_POST, 'length' ),
		'start' => strtotime( val( $_POST, 'start-date' ) . ' ' . val( $_POST, 'start-time' ) ),
		'end' => strtotime( val( $_POST, 'end-date' ) . ' ' . val( $_POST, 'end-time' ) ),
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
		$success = true;
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
			'start-date' => date( 'm/d/Y', strtotime( '+30 minutes' ) ) ,
			'start-time' => date( 'h:i a', strtotime( '+30 minutes' ) ),
			'end-date' => date( 'm/d/Y', strtotime( '+1 hour' ) ),
			'end-time' => date( 'h:i a', strtotime( '+ 1 hour' ) ) ),
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