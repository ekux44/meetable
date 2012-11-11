<?php

class User
{
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
	 * Caches all of the columns from the main List table
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
	 * Gets a column from the main List table
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
			echo 'Sending ' . $this->name() . ' a text message<br />';
			
			// get a phone number to contact this user from
		}
		
		if( $email )
		{
			echo 'Sending ' . $this->name() . ' an e-mail<br />';
		
		
		}
	}
}