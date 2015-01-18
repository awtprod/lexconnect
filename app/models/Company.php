<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Company extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	
	protected $fillable = ['address','name','city','state','zip_code','phone','email', 'v_c'];
	
	public static $rules = [
		'name' => 'required',
		'address' => 'required',
		'city' => 'required',
		'state' => 'required',
		'zip_code' => 'required',
		'phone' => 'required',
		'v_c' => 'required',
		'email' => 'required'
	];
	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'company';

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
}
