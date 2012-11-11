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
	print_pre($message);

	// decode the message
	$newMeetingMessage = json_decode( $message->body, true );
	
	$meeting = new Meeting( val( $newMeetingMessage, 'id' ) );
	
	// pre-load info
	$meeting->loadInfo();
	
	$meeting->kickOff();
	
	// delete the message
//	$ironmq->deleteMessage( 'new-meetings', $message->id);
}

// process responses
foreach( (array)$responseQueue as $message )
{
	echo "Processing response:<br />";
	print_pre($message);

	// decode the message
	$response = json_decode( $message->body, true );
	
	$meeting = new Meeting( val( $response, 'meeting' ) );
	
	// pre-load info
	$meeting->loadInfo();

	$meeting->processResponse( val( $response, 'response' ), val( $response, 'user' ) );

	// delete the message
	$ironmq->deleteMessage( 'new-meetings', $message->id );
}

echo '<p>Finished.</p>';