<?php

class User
{
	private static $ourPhoneNumbers = array(
		'19183763307',
		'19183763309',
		'19183767984',
		'19187169428',
		'19185746923' );
	
	private static $ourEmailAddresses = array(
		'reply1@meetable.io',
		'reply2@meetable.io',
		'reply3@meetable.io',
		'reply4@meetable.io',
		'reply5@meetable.io' );

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
	 *
	 * @return int ID
	 */
	function id() {
		return $this->id;
	}
	
	/**
	 * Caches all of the columns from the main Users table
	 *
	 * @return bool Indicates if the information was loaded successfully
	 */
	function loadInfo()
	{
		if( $this->id() == -1 )
			return false;
			
		$info = Database::select(
			'Users',
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
	 * Gets a column from the main Users table
	 *
	 * @param $piece Column name
	 *
	 * @return string|null Requested info or not found
	 */
	function info( $piece )
	{
		if( $this->infoLoaded || isset( $this->info[ $piece ] ) )
			return (isset( $this->info[ $piece ] )) ? $this->info[ $piece ] : null;
			
		$value = Database::select(
			'Users',
			$piece,
			array(
				'where' => array(
					'id' => $this->id ),
				'single' => true ) );

		$this->info[ $piece ] = $value;

		return $value;
	}
	
	/**
	 * Gets the user name
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
	 * Gets a unique number/email to contact the user with
	 *
	 *
	 */
	function getUniqueFrom( $meeting, $type )
	{
		$valid = array();
		$fromField = '';
		
		if( $type == 'phone' )
		{
			// our numbers
			$valid = self::$ourPhoneNumbers;
			shuffle($valid); // shuffle
			
			$fromField = 'smsFrom';
		} elseif( $type == 'email' )
		{
			// our e-mail addresses
			$valid = self::$ourEmailAddresses;
			
			$fromField = 'emailFrom';
		}
		else
			return false;
		
		// check if the user already has a from setup for this meeting
		if( $from = Database::select(
			'Attendees',
			$fromField,
			array(
				'where' => array(
					'meeting' => $meeting->id(),
					'user' => $this->id ),
				'single' => true ) ) )
			return $from;
		
		// choose a valid unused candidate
		$from = reset( $valid );
		$found = false;
		
		foreach( $valid as $f )
		{
			// check that the address is not in use by the attendee with:
			// i) unsolved meeting
			// ii) unexpired meeting
			if( Database::select(
				'Attendees AS a1',
				'count(*)',
				array(
					'where' => array(
						'a1.user' => $this->id,
						"NOT EXISTS (
							SELECT *
							FROM Attendees AS a2 JOIN Meetings as m ON a2.meeting = m.id JOIN Time_Frame as t ON m.time_frame = t.id
							WHERE ( m.status <> 0 OR t.end > '" . time() . "' ) AND ( a2.active = 1 AND a2.user = a1.user AND a1.$fromField = '$f' ) )"
					),
					'single' => true ) ) > 0 )
			{
				$from = $f;
				$found = true;
				break;
			}
		}

		if( !$found )
			return false;
		
		// save for the future
		Database::update(
			'Attendees',
			array(
				'meeting' => $meeting->id(),
				'user' => $this->id,
				$fromField => $from ),
			array( 'meeting', 'user' ) );
		
		return $from;
	}

	////////////////////
	// SETTERS
	////////////////////

	static function create( $name, $phone, $email )
	{
		// check if the user already exists
		$uid = $uid = Database::select(
			'Users',
			'id',
			array(
				'where' => array(
					'name' => $name,
					'phone' => $phone,
					'email' => $email ),
				'single' => true ) );
		if( $uid > 0 )
			return $uid;
			
		if( Database::insert(
			'Users',
			array(
				'name' => $name,
				'phone' => $phone,
				'email' => $email,
				'created' => time() ) ) )
			return Database::lastInsertID();
		
		return false;
	}
	
	////////////////////
	// UTILITIES
	////////////////////
	
	/**
	 * Sends the user a message about a meeting
	 *
	 */
	function message( $messageID, $meeting, $method = 'both', $solution = null )
	{
		// get all of the contact methods for the user
		$phone = $this->info( 'phone' );
		$email = $this->info( 'email' );
		
		$creatorName = $meeting->creator()->name();
		$userName = $this->name();
		$meetingName = $meeting->name();
		$meetingRange = $meeting->getValidTimeFrame(true);
		$meetingLength = $meeting->meetingLength( true );
		$userRange = $meeting->getUserTimeFrame( $this, true );
		$attendees = $meeting->attendees();
		global $reply;
		$attNames = array();
		foreach( $attendees as $a )
			$attNames[] = $a->name();
		
		$attendeeNames = implode( ', ', $attNames );
		
		if( $phone && in_array( $method, array( 'both', 'phone' ) ) )
		{
			// get a phone number to contact this user from
			$from = $this->getUniqueFrom( $meeting, 'phone' );
			
			if( !$from )
			{
				// send the user an error message
				$messageID = 'exceeded-max-meetings';
				$from = reset( self::$ourPhoneNumbers );
			}
			
			// generate the message
			$message = str_replace(
				array( '{CREATOR_NAME}', '{USER_NAME}', '{MEETING_NAME}', '{MEETING_RANGE}', '{MEETING_LENGTH}', '{USER_RANGE}', '{SOLUTION}', '{ATTENDEE_NAMES}' ),
				array( $creatorName, $userName, $meetingName, $meetingRange, $meetingLength, $userRange, $solution, $attendeeNames ),
				MeetableMessages::$smsMessages[ $messageID ] );
			
			echo 'Sending ' . $this->name() . " a text message for $messageID from $from<br />";
			
			// break up message if > 160 characters
			$messages = array( $message );
			
			if( strlen( $message ) > 160 )
			{
				// break up into messages of 150 characters + (page/total pages)
			    /// lets use 152 characters and keep room for message number like (1/10),
			    /// we can have upto 99 parts of the message (99/99)
			
			    $messagesSplit = str_split($message , 152); 
			    $how_many = count($messagesSplit);
			    $messages = array();
			    foreach($messagesSplit as $index => $m)
			        $messages[] = "(".($index+1)."/".$how_many.") ".$m;
			}
			
			// instantiate a new Twilio Rest Client
			require_once 'Twilio.php';
			$client = new Services_Twilio( 'ACef536c27dac7efda7fe758844e6665ef', 'dd5ca9994edbb122adcd294c81c8524a' );
			
			foreach( $messages as $message )
			{
				// send the message
				$sms = $client->account->sms_messages->create(
					$from, // from phone number (ours)
					$phone, // the number we are sending to (theirs)
					$message // the sms body
				);
			}
		}
		
		if( $email && in_array( $method, array( 'both', 'email' ) ) )
		{
			// get a phone number to contact this user from
			$from = $this->getUniqueFrom( $meeting, 'email' );
			
			if( !$from )
			{
				// send the user an error message
				$messageID = 'exceeded-max-meetings';
				$from = reset( self::$ourEmailAddresses );
			}			
			
			// generate the message
			$message = str_replace(
				array( '{CREATOR_NAME}', '{USER_NAME}', '{MEETING_NAME}', '{MEETING_RANGE}', '{MEETING_LENGTH}', '{USER_RANGE}', '{SOLUTION}', '{ATTENDEE_NAMES}', '{REPLY}' ),
				array( $creatorName, $userName, $meetingName, $meetingRange, $meetingLength, $userRange, $solution, $attendeeNames, $reply ),
				MeetableMessages::$emailMessages[ $messageID ] );
			
			echo 'Sending ' . $this->name() . ' an e-mail for ' . $messageID . '<br />';
			
			// instantiate PHPMailer
			$mail = new Mail;
			
			// basic e-mail info
			$mail->From = $from;
			$mail->FromName = $meeting->creator()->name();
			$mail->Subject = str_replace(
				array( '{CREATOR_NAME}', '{USER_NAME}', '{MEETING_NAME}', '{MEETING_RANGE}', '{MEETING_LENGTH}', '{USER_RANGE}', '{SOLUTION}', '{ATTENDEE_NAMES}', '{REPLY}' ),
				array( $creatorName, $userName, $meetingName, $meetingRange, $meetingLength, $userRange, $solution, $attendeeNames, $reply ),
				MeetableMessages::$emailSubjects[ $messageID ] );
			
			// text body
			$mail->AltBody = $message;
			
			// html body
			$mail->MsgHTML( nl2br($message) );
			
			// send it to the user
			$mail->AddAddress( $email );
			
			// send the e-mail
			return $mail->Send();
		}
	}
}