<?php

class Meeting
{
	static function create( $data )
	{
		/* validate the input */
		
		// check for a valid name
		// TODO
				
		// check for a valid length
		// TODO
				
		// check for a valid time range
		// TODO				
		
		// ensure there is at least 1 attendee
		if( count( $data[ 'attendeeNames' ] ) == 0 )
		{
			// TODO: error message
			return false;
		}
		
		foreach( $data[ 'attendeeNames' ] as $k => $name )
		{
			// validate and sanitize the attendee names
			// TODO
			
			// ensure that there is at least 1 e-mail or phone number per attendee
			// figure out which fields are used
			// TODO

			// validate and sanitize the attendee phone numbers
			// TODO
			
			// validate and sanitize the attendee e-mails
			// TODO
		}
		
		// validate and sanitize the creator
		// ensure e-mail or phone number is filled out
		// figure out which contact method was used
		// validate and sanitize the phone number
		// validate and sanitize the e-mail
		// TODO
		
		/* add the meeting to the database */
		
		// add the creator
		$creatorID = User::create( $data[ 'creatorName' ], $data[ 'creatorPhone' ], $data[ 'creatorEmail' ] );
				
		// add the time frame
		Database::insert(
			'Time_Frame',
			array(
				'start' => strtotime( $data[ 'start' ] ),
				'end' => strtotime( $data[ 'end' ] ) ) );

		$timeFrameID = Database::lastInsertId();
		
		// create the meeting
		Database::insert(
			'Meetings',
			array(
				'name' => $data[ 'name' ],
				'creator' => $creatorID,
				'created' => time(),
				'status' => 0,
				'time_frame' => $timeFrameID,
				'exact' => (isset($data[ 'narrowToOne' ]) && $data[ 'narrowToOne' ] )?1:0 ) );

		$meetingID = Database::lastInsertId();
				
		// add the attendees
		foreach( $data[ 'attendeeNames' ] as $k => $name )
		{
			$uid = User::create( $name, $data[ 'attendeePhones' ][ $k ], $data[ 'attendeeEmails' ][ $k ] );
			
			Database::insert(
				'Attendees',
				array(
					'meeting' => $meetingID,
					'user' => $uid,
					'active' => 1 ) );
		}
		
		/* add the meeting to the message queue */
		$ironmq = new IronMQ();
		
		// put the message on the queue
		$ironmq->postMessage("new-meetings", "new-meeting-$meetingID" );
		
		return true;
		
		/*
		// Get a message
		$msg = $ironmq->getMessage("meetings");
		
		// Delete the message
		$ironmq->deleteMessage("my_queue", $msg->id);
		*/
	}
}