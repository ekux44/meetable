<?php

class Meeting
{
	static function create( $data )
	{
		// validate the input
		
		// add the meeting to the database
			echo 'before';	
		// add the meeting to the message queue
		require("phar://iron_mq.phar");
echo 'after';
		$ironmq = new IronMQ();
		
		// put the message on the queue
		$ironmq->postMessage("meetings", $meetingID );
		
		return true;
		
		/*
		// Get a message
		$msg = $ironmq->getMessage("meetings");
		
		// Delete the message
		$ironmq->deleteMessage("my_queue", $msg->id);
		*/
	}
}