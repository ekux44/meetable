<?php

class Meeting
{
	private $id;	
	private $info;
	private $infoLoaded;

	/**
	 * Constructor
	 *
	 * @param int|string $id List ID
	 */
	function __construct($id = -1) {
		if( is_numeric( $id ) )
			$this->id = $id;
		else
			$this->id = -1;
	}
	
	//////////////////////////////
	// GETTERS
	//////////////////////////////

	/**
	 * Gets the list ID
	 * @return int ID
	 */
	function id() {
		return $this->id;
	}
	
	/**
	 * Caches all of the columns from the main Meeting table
	 * @return bool Indicates if the information was loaded successfully
	 */
	function loadInfo()
	{
		if( $this->id() == -1 )
			return false;
			
		$info = Database::select(
			'Meetings',
			'*',
			array(
				'where' => array(
					'id' => $this->id ),
				'singleRow' => true ) );
		
		foreach( (array)$info as $key => $item )
			$this->info[ $key ] = $item;

		$this->infoLoaded = true;
		return true;
	}
	
	/**
	 * Gets a column from the main Meetings table
	 * @param $piece Column name
	 * @return string|null Requested info or not found
	 */
	function info( $piece )
	{
		if( $this->infoLoaded || isset( $this->info[ $piece ] ) )
			return (isset( $this->info[ $piece ] )) ? $this->info[ $piece ] : null;
			
		$value = Database::select(
			'Meetings',
			$piece,
			array(
				'where' => array(
					'id' => $this->id ),
				'single' => true ) );

		$this->info[ $piece ] = $value;

		return $value;
	}
	
	/**
	 * Gets the meeting name
	 *
	 * @param boolean $strip_chars true if 
	 *
	 * @return string|false Name of the list or error
	 */
	function name( $htmlentities = true )
	{
		$name = stripslashes( $this->info( 'name' ) );
		return ($htmlentities) ? htmlentities( $name ) : $name;
	}
	
	function attendees()
	{
		$attendees = Database::select(
			'Attendees',
			'user',
			array(
				'where' => array(
					'meeting' => $this->id,
					'active' => 1 ),
				'fetchStyle' => 'singleColumn' ) );
			
		$return = array();
		
		foreach( $attendees as $attendee )
		{
			$user = new User( $attendee );
			$user->loadInfo();
			$return[] = $user;
		}
		
		return $return;
	}

	//////////////////////////////
	// SETTERS
	//////////////////////////////
	
	function kickOff()
	{
		// get all of the attendees
		$attendees = $this->attendees();
		
		// send the initial message to all parties
		foreach( $attendees as $attendee )
		{
			$attendee->message( 'initial-meeting', $this );
		}
	}
	
	function processResponse( $response, $method, $from )
	{
	
	}

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
		$ironmq->postMessage("new-meetings", array(
			'body' => json_encode( array(
				'id' =>  $meetingID,
				'attempts' => 0 ) ) ) );
		
		return true;
	}
}