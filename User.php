<?php

class User
{
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
}