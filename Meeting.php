<?php

class Meeting
{
	private static $commands = array(
		'man',
		'kill',
		'busy',
		'status',
		'reply',
		'attendees'
	);

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
	
	/**
	 * Gets the creator of the meeting
	 *
	 *
	 */
	function creator()
	{
		return new User( Database::select(
			'Attendees',
			'user',
			array(
				'where' => array(
					'creator' => 1,
					'meeting' => $this->id ),
				'single' => true ) ) );
	}
	
	/**
	 * Gets the attendees of the meeting
	 *
	 */
	function attendees( $includeCreator = true )
	{
		$where = array(
			'meeting' => $this->id,
			'active' => 1 );
			
		if( !$includeCreator )
			$where[ 'creator' ] = 0;
		
		$attendees = Database::select(
			'Attendees',
			'user',
			array(
				'where' => $where,
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
	
	/**
	 * Checks if the meeting is still active
	 *
	 *
	 */
	function active()
	{
		// a meeting is active if it is marked unsolved and it is not too late for the meeting to happen
		return $this->info( 'status' ) == 0 &&
			Database::select(
				'Time_Frame AS f JOIN Meetings AS m ON m.time_frame = f.id',
				'f.start',
				array(
					'where' => array(
						'm.id' => $this->id ),
					'single' => true ) ) > time();
	}
	
	function getInitialTimeFrame()
	{
		return Database::select(
			'Time_Frame AS f JOIN Meetings AS m ON m.time_frame = f.id',
			'f.start,f.end',
			array(
				'where' => array(
					'm.id' => $this->id ),
				'singleRow' => true ) );
	}
	
	/**
	 * Get the time frame for which there are no conflicts
	 *
	 *
	 */
	function getValidTimeFrame( $humanReadable = false )
	{
		// get our initial parameters
		$initialTimeFrame = $this->getInitialTimeFrame();
		
		// start with the initial parameters
		$return = array(
			'start' => $initialTimeFrame[ 'start' ],
			'end' => $initialTimeFrame[ 'end' ] );
		
		if( $initialTimeFrame[ 'start' ] == $initialTimeFrame[ 'end' ] )
			return $return;
		
		// get the time frames of all the users
		$timeFrames = Database::select(
			'Time_Frame AS f JOIN Attendees AS a ON a.time_frame = f.id',
			'f.start,f.end',
			array(
				'where' => array(
					'a.meeting' => $this->id,
					'a.active' => 1 ) ) );

		foreach( $timeFrames as $frame )
		{
			if( $frame[ 'start' ] < $frame[ 'end' ] )
			{
				// update the start time if it shrinks the window
				if( $frame[ 'start' ] > $return[ 'start' ] )
					$return[ 'start' ] = $frame[ 'start' ];
				
				// update the end time if it shrinks the window
				if( $frame[ 'end' ] < $return[ 'end' ] )
					$return[ 'end' ] = $frame[ 'end' ];
					
				// there is an inconsistency, no possible time frame
				if( $frame[ 'start' ] > $return[ 'end' ] || $frame[ 'end' ] < $return[ 'start' ] )
				{
					if( $humanReadable )
						return 'no valid times';
					else
						return false;
				}
			}
		}
		
		if( $humanReadable )
			return $this->humanReadableRange( $return );
		else
			return $return;
	}
	
	function getUserTimeFrame( $user, $humanReadable = false )
	{
		// get the time frames of the users
		$timeFrame = Database::select(
			'Time_Frame AS f JOIN Attendees AS a ON a.time_frame = f.id',
			'f.start,f.end',
			array(
				'where' => array(
					'a.meeting' => $this->id,
					'a.user' => $user->id() ),
				'singleRow' => true ) );

		if( $humanReadable )
			return $this->humanReadableRange( $timeFrame );
		else
			return $timeFrame;
	}
	
	/**
	 * Gets the meeting length
	 *
	 *
	 */
	function meetingLength( $humanReadable = false )
	{
		$length = $this->info( 'length' );
		
		if( $humanReadable )
		{
			if( $length % 60 == 0 )
				return ($length / 60) . ' hour' . (($length > 60)?'s':'');
			else if( $length % 30 == 0 && $length > 60 )
				return number_format( ($length / 30), 1 ) . ' hours';
			else
				return $length . ' minutes';
		}
		else
			return $length;
	}
	
	/**
	 * Checks if there is a solution for the problem and returns it
	 *
	 *
	 */
	function solution()
	{
		/* Possible solutions states:
			i) solved
			ii) partially solved (everyone has pitched in)
			iii) solvable (in progress)
			iv) unsolvable
		*/
		
		// get the current time frame
		$timeRange = $this->getValidTimeFrame();
		
		// if no time range, we are screwed
		if( !$timeRange )
		{
			return array( 'status' => 'unsolvable' );
		}
		// an exact solution exists
		else if( $timeRange[ 'start' ] == $timeRange[ 'end' ] )
		{
			return array( 'status' => 'solved', 'solution' => $timeRange[ 'start' ] );
		}
		// check if everyone has submitted a time
		else if( Database::select(
			'Attendees',
			'count(*)',
			array(
				'where' => array(
					'meeting' => $this->id,
					'creator' => 0,
					'active' => 1,
					'time_frame > 0' ),
				'single' => true ) ) ==
			Database::select(
				'Attendees',
				'count(*)',
				array(
					'where' => array(
						'meeting' => $this->id,
						'creator' => 0,
						'active' => 1 ),
					'single' => true ) ) )
		{	
			// automatically pick a solution?
			if( $this->info( 'narrowToOne' ) == 0 )
			{
				// todo: we could make this random
				$solution = $timeRange[ 'start' ];
				
				return array( 'status' => 'solved', 'solution' => $solution );
			}
			// offer the creator a chance to solve
			else
			{
				return array( 'status' => 'partially-solved' );
			}
		}
		// not everyone has pitched in
		else
		{
			return array( 'status' => 'solvable' );
		}
	}

	//////////////////////////////
	// SETTERS
	//////////////////////////////
	
	function kickOff()
	{
		// get all of the attendees
		$attendees = $this->attendees( false );
		
		// send the initial message to all parties
		foreach( $attendees as $attendee )
		{
			$attendee->message( 'meeting-kick-off', $this );
		}
		
		return true;
	}
	
	/**
	 * Processes a reply to an e-mail or SMS
	 *
	 *
	 */
	function processResponse( $response, $user, $method )
	{
		// check that the meeting is still active
		if( !$this->active() )
		{
			// notify the user of their mistake
			$user->message( 'bad-input', $this, $method );
			
			return true;
		}
		
		/*
			What does the response want to do? Possibilities:
			i) command
			ii) time
			iii) otherwise, error
		*/
		
		$exp = explode( ' ', $response );
		$command = strtolower( trim( reset( $exp ) ) );
		unset( $exp[ 0 ] );
		$body = implode( $exp );

		// command
		if( $this->isValidCommand( $command, $body, $user ) )
		{
			$this->processCommand( $command, $body, $user, $method );
		}
		// time
		if( $this->isValidTime( $response ) )
		{
			$this->registerTime( $response, $user, $method );
		}
		// error
		else
		{
			// notify the user of their mistake
			$user->message( 'bad-input', $this, $method );
		}
		
		return true;
	}

	/**
	 * Creates a meeting
	 *
	 *
	 */
	static function create( $data )
	{
		/* validate the input */
		
		// check for a valid name
		if( !isset( $data[ 'name' ] ) || empty( $data[ 'name' ] ) )
		{
			global $error;
			$error = 'You forgot to include a meeting name.';
			return false;
		}
				
		// check for a valid length
		if( !isset( $data[ 'length' ] ) || !is_numeric( $data[ 'length' ] ) || $data[ 'length' ] == 0 )
		{
			global $error;
			$error = 'You forgot to set the length of the meeting.';
			return false;
		}
				
		// check for a valid time range
		if( !isset( $data[ 'start' ] ) || !is_numeric( $data[ 'start' ] ) || $data[ 'start' ] < time()
			|| !isset( $data[ 'end' ] ) || !is_numeric( $data[ 'end' ] ) || $data[ 'end' ] < time()
			|| $data[ 'start' ] > $data[ 'end' ] )
		{
			global $error;
			$error = 'Please pick a valid time range.';
			return false;
		}
		
		// ensure there is at least 1 attendee
		if( !isset( $data[ 'attendeeNames' ] ) || count( $data[ 'attendeeNames' ] ) == 0 )
		{
			global $error;
			$error = 'Please add at least one attendee.';
			return false;
		}
		
		foreach( $data[ 'attendeeNames' ] as $k => $name )
		{
			// validate and sanitize the attendee names
			if( empty( $name ) )
			{
				global $error;
				$error = 'You forgot to give the name of at least one of the attendees.';
				return false;
			}
			
			// ensure that there is at least 1 e-mail or phone number per attendee
			// figure out which fields are used
			$phone = $data[ 'attendeePhones' ][ $k ];
			$email = $data[ 'attendeeEmails' ][ $k ];
			if( empty( $phone ) && empty( $email ) )
			{
				global $error;
				$error = 'You forgot to tell us how to contact an attendee.';
				return false;
			}

			// validate and sanitize the attendee phone numbers
			if( $phone && !Validate::phone( $phone ) )
			{
				global $error;
				$error = 'There was an invalid phone number for one of the attendees. Please use a 10-digit phone number.';
				return false;
			}
			
			// validate and sanitize the attendee e-mails
			if( $email && !Validate::email( $email ) )
			{
				global $error;
				$error = 'There was an invalid e-mail address for one of the attendees.';
				return false;
			}
			
			$data[ 'attendeePhones' ][ $k ] = $phone;
			$data[ 'attendeeEmails' ][ $k ] = $email;
		}
		
		// validate and sanitize the creator name
		if( !isset( $data[ 'creatorName' ] ) || empty( $data[ 'creatorName' ] ) )
		{
			global $error;
			$error = 'You forgot to tell your attendees your name so they know who is inviting them.';
			return false;
		}
		
		// ensure e-mail or phone number is filled out
		// figure out which contact method was used
		$phone = $data[ 'creatorPhone' ];
		$email = $data[ 'creatorEmail' ];
		if( empty( $phone ) && empty( $email ) )
		{
			global $error;
			$error = 'You forgot to give us a way to contact you.';
			return false;
		}

		// validate and sanitize the phone number
		if( $phone && !Validate::phone( $phone ) )
		{
			global $error;
			$error = 'Your phone number was invalid.';
			return false;
		}
		
		// validate and sanitize the e-mail
		if( $email && !Validate::email( $email ) )
		{
			global $error;
			$error = 'Your e-mail address was invalid.';
			return false;
		}
		
		$data[ 'creatorPhone' ] = $phone;
		$data[ 'creatorEmail' ] = $email;
		
		/* add the meeting to the database */
						
		// add the initial time frame
		Database::insert(
			'Time_Frame',
			array(
				'start' => $data[ 'start' ],
				'end' => $data[ 'end' ] ) );

		$timeFrameID = Database::lastInsertId();
		
		// create the meeting
		Database::insert(
			'Meetings',
			array(
				'name' => $data[ 'name' ],
				'created' => time(),
				'status' => 0,
				'length' => $data[ 'length' ],
				'time_frame' => $timeFrameID,
				'exact' => (isset($data[ 'narrowToOne' ]) && $data[ 'narrowToOne' ] )?1:0 ) );

		$meetingID = Database::lastInsertId();
		
		// add the creator as a user
		$creatorID = User::create( $data[ 'creatorName' ], $data[ 'creatorPhone' ], $data[ 'creatorEmail' ] );
		
		// add the creator as an attendee
		Database::insert(
			'Attendees',
			array(
				'meeting' => $meetingID,
				'user' => $creatorID,
				'active' => 1,
				'creator' => 1 ) );
				
		// add the attendees
		foreach( $data[ 'attendeeNames' ] as $k => $name )
		{
			$uid = User::create( $name, $data[ 'attendeePhones' ][ $k ], $data[ 'attendeeEmails' ][ $k ] );
			
			Database::insert(
				'Attendees',
				array(
					'meeting' => $meetingID,
					'user' => $uid,
					'active' => 1,
					'creator' => 0 ) );
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
	
	///////////////
	// UTILITIES
	//////////////
	
	function humanReadableRange( $return )
	{
		/*
			Possible cases:
			1) today
			2) tomorrow
			3) within the week, same day
			4) within the week, different days
			5) within the month, same day
			6) within the month, different days
			7) within the year, same day
			8) within the year, different days
			9) not within the year, same day
			10) not within the year, different days 
		*/

		if( $return[ 'start' ] == $return[ 'end' ] )
			return 'at ' . date( 'l, F j, Y h:i A', $return[ 'start' ] );
		
		// are the dates today?
		if( $return[ 'end' ] - time() <= 3600*24 && date( 'd', $return[ 'end' ] ) == date( 'd' ) )
		{
			// i.e. today between 1:30 pm and 3:30 pm 
			return 'today between ' . date( 'g:i A', $return[ 'start' ] ) . ' and ' . date( 'g:i A', $return[ 'end' ] );
		}
		// are the dates tomorrow
		else if( $return[ 'end' ] - time() <= 3600*24*2 && date( 'd', $return[ 'end' ] ) == date( 'd', strtotime('tomorrow') ) )
		{
			// i.e. tomorrow between 1:30 pm and 3:30 pm
			return 'tomorrow between ' . date( 'g:i A', $return[ 'start' ] ) . ' and ' . date( 'g:i A', $return[ 'end' ] );
		}
		// are the dates within a week away and 6 DATES away
		else if( $return[ 'end' ] - time() <= 3600*24*7 && date( 'j', $return[ 'end' ] ) - date( 'j' ) <= 6 )
		{
			// is the range on the same day?
			if( date( 'Ymd', $return[ 'start' ] ) == date( 'Ymd', $return[ 'end' ] ) )
			{
				// i.e. Friday between 1:30 pm and 3:30 pm
				return date( 'l \b\e\t\w\e\e\n g:i A', $return[ 'start' ] ) . ' and ' . date( 'g:i A', $return[ 'end' ] );
			}
			// nope
			else
			{
				// i.e. between Friday 3:30 pm and Saturday 1:30 pm
				return 'between ' . date( 'l g:i A', $return[ 'start' ] ) . ' and ' . date( 'l g:i A', $return[ 'end' ] );
			}
		}
		// are the dates within this month?
		else if( $return[ 'end' ] - time() <= 3600*24*31 && date( 'm' ) == date( 'm', $return[ 'end' ] ) )
		{
			// is the range on the same day?
			if( date( 'Ymd', $return[ 'start' ] ) == date( 'Ymd', $return[ 'end' ] ) )
			{
				// i.e. Friday the 6th between 1:30 pm and 3:30 pm
				return date( 'l \t\h\e jS', $return[ 'start' ] ) . ' between ' . date( 'g:i A', $return[ 'start' ] ) . ' and ' . date( 'g:i A', $return[ 'end' ] );
			}
			// nope
			else
			{
				// i.e. between Friday the 6th at 1:30 pm and Monday the 9th at 3:30 pm
				return 'between ' . date( 'l \t\h\e jS \a\t g:i A', $return[ 'start' ] ) . ' and ' . date( 'l \t\h\e jS \a\t g:i A', $return[ 'end' ] );
			}
		}
		// are the dates within this year?
		else if( $return[ 'end' ] - time() <= 3600*24*365 && date( 'Y' ) == date( 'Y', $return[ 'end' ] ) )
		{
			// is the range on the same day?
			if( date( 'Ymd', $return[ 'start' ] ) == date( 'Ymd', $return[ 'end' ] ) )
			{
				// i.e. Friday, November 6th between 1:30 pm and 3:30 pm
				return date( 'l, F jS \b\e\t\w\e\e\n g:i A', $return[ 'start' ] ) . ' and ' . date( 'g:i A', $return[ 'end' ] );
			}
			// nope
			else
			{
				// i.e. between Friday, November 6th at 1:30 pm and Monday, November 9th at 3:30 pm
				return 'between ' . date( 'l, F jS \a\t g:i A', $return[ 'start' ] ) . ' and ' . date( 'l, F jS \a\t g:i A', $return[ 'end' ] );
			}
		}
		// the dates far far away, give them the full spiel
		else
		{
			// is the range on the same day?
			if( date( 'Ymd', $return[ 'start' ] ) == date( 'Ymd', $return[ 'end' ] ) )
			{
				// i.e. November 6th, 2012 between 1:30 pm and 3:30 pm
				return date( 'F j, Y \b\e\t\w\e\e\n g:i A', $return[ 'start' ] ) . ' and ' . date( 'g:i A', $return[ 'end' ] );
			}
			// nope
			else
			{
				// i.e. between November 6th, 2012 at 1:30 pm and Monday, November 9th at 3:30 pm
				return 'between ' . date( 'F j, Y \a\t g:i A', $return[ 'start' ] ) . ' and ' . date( 'F j, Y \a\t g:i A', $return[ 'end' ] );
			}
		}
	}
	
	/**
	 * Checks if a given command is valid
	 *
	 *
	 */
	private function isValidCommand( $command, $body, $user )
	{
		return in_array( strtolower( $command ), self::$commands );
	}
	
	/**
	 * Checks if a given time is valid
	 *
	 *
	 */
	function isValidTime( $time )
	{
		/* Possible inputs:
			i) time
				- 4 (DOES NOT WORK WITH strtotime())
				- 3:30
				- 4pm
				- 4pm pst
				- friday
				- friday 4pm
				- friday 4pm pst
				- november 9
				- nov 9 4pm (DOES NOT WORK WITH strtotime())
				- november 9 5pm (DOES NOT WORK WITH strtotime())
				- november 9 5pm pst (DOES NOT WORK WITH strtotime())
				- november 9, 2014
				- november 9, 2014 5pm
				- november 9, 2014 6pm pst
			ii) time range
				valid times seperated by to (i.e. 4pm to 5pm)
		*/
		
		// check for a time range
		$exp = explode( ' to ', $time );
		if( count( $exp ) == 2 )
		{
			// validate each side of the " to "
			return $this->isValidTime( $exp[ 0 ] ) && $this->isValidTime( $exp[ 1 ] );
		}
		// must be a time
		else
		{
			$time = trim( $time );
			
			// check if the string can be converted to a timestamp
			if( strtotime( $time ) )
				return true;
			// check for just a number (1<time<12)
			else if( is_numeric( $time ) && $time > 0 && $time <= 12 )
				return true;
			else
			{
				$exp = explode( ' ', $time );
				
				// check for month + day + time by splitting between two
				if( strtotime( implode( ' ', array( val( $exp, 0 ), val( $exp, 1 ) ) ) ) && strtotime( implode( ' ', array( val( $exp, 2 ), val( $exp, 3 ) ) ) ) )
					return true;
			}
		}
		
		return false;
	}
	
	function generateTimeRange( $time )
	{
		// get our initial parameters
		$initialTimeFrame = $this->getInitialTimeFrame();
				
		$return = array( 'start' => $initialTimeFrame[ 'start' ], 'end' => $initialTimeFrame[ 'end' ] );
		
		// check for a time range
		$exp = explode( ' to ', $time );
		if( count( $exp ) == 2 )
		{
			// validate each side of the " to "
			$return[ 'start' ] = $this->getTime( $exp[ 0 ] );
			$return[ 'end' ] = $this->getTime( $exp[ 1 ] );
		}
		// single value range
		else
		{
			$return[ 'end' ] = $return[ 'start' ] = $this->getTime( $time );
		}
		
		return $return;
	}
	
	private function getTime( $time )
	{
		$time = trim( $time );
		
		// check if the string can be converted to a timestamp
		$strTime = strtotime( $time );
		if( $strTime )
			return $strTime;
		// check for just a number (1<time<12)
		else if( is_numeric( $time ) && $time > 0 && $time <= 12 )
			return strtotime( $time . ':00' );
		else
		{
			$exp = explode( ' ', $time );
			
			// check for month + day + time by splitting between two
			$strTime = strtotime( implode( ' ', array( val( $exp, 0 ), val( $exp, 1 ) ) ) . ', ' . date( 'Y' ) . ' ' . implode( ' ', array( val( $exp, 2 ), val( $exp, 3 ) ) ) );
			if( $strTime )
				return $strTime;
		}
		
		return time();
	}
	
	/** 
	 * Processes a command for a given user
	 *
	 */
	private function processCommand( $command, $body, $user, $method )
	{
		switch( $command )
		{
			// kill the meeting
			case 'kill':
				if( $this->creator()->id() == $user->id() )
				{
					Database::update(
						'Meetings',
						array(
							'meeting' => $this->id ),
						array( 'meeting' ) );
					foreach( $this->attendees() as $a )
					{
						if( $a->id() == $user->id() )
							continue;
							
						$a->message( 'cancel', $this );
					}
				}
			break;
			// lists the available commands
			case 'man':
				$user->message( 'man', $this, $method );
			break;
			case 'status':
				$user->message( 'status', $this, $method );
			break;
			case 'attendees':
				$user->message( 'attendees', $this, $method );
			break;
			case 'busy':
				// mark the user as busy
				if( $this->creator()->id() == $user->id() )
					return true;
				
				Database::update(
					'Attendees',
					array(
						'user' => $user->id(),
						'meeting' => $this->id,
						'active' => 0 ),
					array( 'user', 'meeting' ) );
			break;
			case 'reply':
					global $reply;
					$reply = $body;
				foreach( $this->attendees() as $a )
				{
					if( $a->id() == $user->id() )
						continue;
						
					$a->message( 'reply', $this );
				}
			break;
		}
	}
	
	/** 
	 * Registers a time for a given user
	 *
	 */
	private function registerTime( $time, $user, $method)
	{
		$timeAlreadyUpdated = false;
		
		// are we talking to the creator?
		if( $user->id() == $this->creator()->id() )
		{
			// what is the solution state?
			$solution = $this->solution();
			$proposedTime = $this->getTime( $time );
			$bounds = $this->getValidTimeFrame();
			
			// set the time range to the creator's choice, within the bounds
			if( ( $solution['status'] == 'partially-solved' && $proposedTime > $bounds[ 'start' ] && $proposedTime < $bounds[ 'end' ] ) ||
			// the creator has free reign on the time as long as it is in the future
				( $solution['status'] == 'unsolvable' && $proprosedTime > time() ) )
			{
				// set the time for the meeting
				Database::update(
					'Time_Frame',
					array(
						'id' => $this->info('time_frame'),
						'start' => $proposedTime,
						'end' => $proposedTime ),
					array( 'id' ) );
					
				$timeAlreadyUpdated = true;
			}
			// hopefully we do not get here
			else if( $solution['status'] == 'solved' )
			{
				return true;
			}
			else
			{
				// give the user an error message
				$user->message( 'bad-time', $this, $method );
			
				return true;
			}
		}
		else
		{
			// generate the time range
			$timeRange = $this->generateTimeRange( $time );
			
			// TODO: the current bounds should exclude the user's previous guess
			
			// check that we are within the bounds
			$bounds = $this->getValidTimeFrame();

			if( $timeRange[ 'start' ] <= $timeRange[ 'end' ] && ( $timeRange[ 'start' ] > $bounds[ 'end' ] || $timeRange[ 'end' ] < $bounds[ 'start' ] ) )
			{
				// give the user an error message
				$user->message( 'bad-time', $this, $method );
				
				return true;
			}
						
			// TODO: this would be more efficient if time ranges were built into the attendee and meeting objects
				
			// delete any previous time ranges for the user
			Database::delete(
				'Time_Frame',
				array(
					'id' => Database::select(
						'Attendees',
						'time_frame',
						array(
							'where' => array(
								'user' => $user->id() ),
							'single' => true ) ) ) );
			
			// insert the time range
			Database::insert(
				'Time_Frame',
				array(
					'start' => $timeRange[ 'start' ],
					'end' => $timeRange[ 'end' ] ) );
			
			// update the attendee with the new time range id
			Database::update(
				'Attendees',
				array(
					'user' => $user->id(),
					'meeting' => $this->id,
					'time_frame' => Database::lastInsertId() ),
				array( 'user', 'meeting' ) );
				
			// confirm the time with the user
			$user->message( 'confirm-time', $this, $method );				
		}
	
		/*
			How does this time entry affect the problem? Does it make it:
			i) solved
			ii) partially solved (everyone has pitched in)
			iii) solvable (in progress)
			iv) unsolvable
		*/
		
		$solution = $this->solution();
		
		// solved
		// ** THIS IS WHERE WE WANT TO END UP
		if( $solution[ 'status' ] == 'solved' )
		{
			// mark the project as solved
			Database::update(
				'Meetings',
				array(
					'id' => $this->id,
					'status' => 1 ),
				array( 'id' ) );
			
			// set the time
			if( $timeAlreadyUpdated )
				Database::update(
					'Time_Frame',
					array(
						'id' => $this->info('time_frame'),
						'start' => $solution['solution'],
						'end' => $solution['solution'] ),
					array( 'id' ) );
			
			// confirm the time with everyone
			foreach( $this->attendees() as $attendee )
			{
				$attendee->message( 'solution-found', $this, 'both', date( 'l, F j, Y g:i A', $solution['solution'] ) );
			}
			
			return true;
		}
		else if( $solution[ 'status' ] == 'partially-solved' )
		{
			// give the organizer the ability to choose within range of available times
			$this->creator()->message( 'partially-solved-choice', $this );
			
			return true;
		}
		// solvable
		else if( $solution[ 'status' ] == 'solvable' )
		{
			// give everyone a status update with the current time window except the current user
			foreach( $this->attendees() as $attendee )
			{
				if( $attendee->id() == $user->id() )
					continue;
			
				$attendee->message( 'status', $this );
			}
			
			return true;
		}
		// unsolvable
		else
		{
			// shit
			
			// give the creator the opportunity to manually set the time or cancel the meeting
			$this->creator()->message( 'unsolvable-choice', $this );
			
			return true;
		}
				
		return true;
	}
}