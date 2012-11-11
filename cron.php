<?php

echo "Processing Message Queues.<br /><br />";

/*
 get N messages from each queue:
 i) new meetings
 ii) responses
*/

$ironmq = new IronMQ();

$newMeetingQueue = $ironmq->getMessages( 'new-meetings', 5 );
$responseQueue = $ironmq->getMessages( 'responses', 5 );

// process new meetings
foreach( (array)$newMeetingQueue as $message )
{
	echo "Processing new meeting:<br />";

	// decode the message
	$newMeetingMessage = json_decode( $message->body, true );
	
	$meeting = new Meeting( val( $newMeetingMessage, 'id' ) );
	
	// pre-load info
	$meeting->loadInfo();
	
	print_pre($meeting->name());
	
	if( $meeting->kickOff() )
	{
		// delete the message
		//$ironmq->deleteMessage( 'new-meetings', $message->id);
	}
}

echo '<br />';

// process responses
foreach( (array)$responseQueue as $message )
{
	echo "Processing response:<br />";

	// decode the message
	$response = json_decode( $message->body, true );

	print_pre($message->body);
	
	$meeting = new Meeting( val( $response, 'meeting' ) );
	$user = new User( val( $response, 'user' ) );
	
	// pre-load info
	$meeting->loadInfo();
	$user->loadInfo();

	if( $meeting->processResponse( val( $response, 'response' ), $user ) )
	{
		// delete the message
		//$ironmq->deleteMessage( 'responses', $message->id );
	}
}

echo '<p>Finished.</p>';