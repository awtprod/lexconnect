<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['email','email_confirmation','role','name','password','activation_code','activation','password_confirmation','company', 'user_role', 'company_id'];
	
	public static $rules = [
		'password' => 'confirmed|min:8',
		'name' => 'required'
	
		
	];
	
		public static $rulesall = [
		'email' => 'required|confirmed|email|unique:users',
		'name' => 'required',
		'password' => 'confirmed|min:8'
	];
	
	public static $passrules = [
		'password' => 'required|confirmed|min:8'
		];
	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function isValid()
	{
		
		$validation = Validator::make($this->attributes, static::$rules);
		
		if ($validation->passes()) return true;
		
		$this->errors = $validation->messages();
		
		return false;
	}
		public function isValidPassword()
	{
		
		$passvalidation = Validator::make($this->attributes, static::$passrules);
		
		if ($passvalidation->passes()) return true;
		
		$this->errors = $passvalidation->messages();
		
		return false;
	}
		public function isValidAll()
	{
		
		$allvalidation = Validator::make($this->attributes, static::$rulesall);
		
		if ($allvalidation->passes()) return true;
		
		$this->errors = $allvalidation->messages();
		
		return false;
	}

		public function getFullNameAttribute()
	{
		return $this->attributes['fname'] .' '. $this->attributes['lname'];
	}

}
