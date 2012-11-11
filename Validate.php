<?php

class Validate
{
	/**
	* Validates an e-mail address
	*
	* @param string $email e-mail address
	* @param boolean $checkRegistered check if the e-mnail address is already registered
	* @param boolean $checkBanned check if the e-mail address has been banned
	*
	* @return boolean success
	*/
	static function email( &$email )
	{
		$email = strtolower($email);

		if( filter_var($email, FILTER_VALIDATE_EMAIL) === false )
			return false;
		
		return true;
	}
	
	/**
	* Validates a phone number
	*
	* @param int $phone phone number
	* @param boolean $null_allowed true if the phone number is allowed to be empty
	* @param string $type phone number type
	*
	* @return int updated phone number
	*/
	static function phone( &$phone, $null_allowed = true )
	{	
		if ($phone == '' && $null_allowed) return true;
		
		$phone = preg_replace("/[[:^digit:]]/", '', $phone);
		
		if( strlen($phone) < 10 || !is_numeric($phone) )
			return false;
		
		if( strlen( $phone ) == 10 )
			$phone = '1' . $phone;

		return true;
	}	
}