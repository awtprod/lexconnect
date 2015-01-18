<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Attempts extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['date', 'time', 'description', 'job'];
	
	public static $rules = [
		'date' => 'required|date',
		'time' => 'required',
		'description' => 'required'
		];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'attempts';

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
