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
		'test@test.com' );

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
		// TODO
		$from = reset( $valid );
		
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
		if( $uid = Database::select(
			'Users',
			'id',
			array(
				'where' => array(
					'name' => $name,
					'phone' => $phone,
					'email' => $email ),
				'single' => true ) ) )
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
	function message( $messageID, $meeting )
	{
		// get all of the contact methods for the user
		$phone = $this->info( 'phone' );
		$email = $this->info( 'email' );
		
		if( $phone )
		{			
			// get a phone number to contact this user from
			$from = $this->getUniqueFrom( $meeting, 'phone' );
			
			// generate the message
			include_once 'messages.php';
			$body = str_replace(
				array( '{CREATOR_NAME}', '{USER_NAME}', '{MEETING_NAME}' ),
				array( $meeting->creator()->name(), $this->name(), $meeting->name() ),
				$messages[ $messageID ] );
			
			
			echo 'Sending ' . $this->name() . " a text message for $messageID from $from<br />";			
			
			// instantiate a new Twilio Rest Client
			require 'Twilio.php';
			$client = new Services_Twilio( 'ACef536c27dac7efda7fe758844e6665ef', 'dd5ca9994edbb122adcd294c81c8524a' );
			
			// send the message
			$sms = $client->account->sms_messages->create(
				$from, // from phone number (ours)
				$phone, // the number we are sending to (theirs)
				$message // the sms body
			);
		}
		
		if( $email )
		{
			echo 'Sending ' . $this->name() . ' an e-mail for ' . $messageID . '<br />';
		
		
		}
	}
}