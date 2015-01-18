<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Rules extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['affidavit', 'mailing', 'filing_client', 'filing_vendor','service_client','service_vendor'];
	
	public static $rules = [
		'date' => 'required|date',
		'time' => 'required',
		'served_upon' => 'required',
		'description' => 'required'
		];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rules';

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
	
	public function DecOfMailing($state){
	$mailing = Rules::whereName($state)->pluck('mailing');
	
	if($mailing == 'yes'){
		Return TRUE;
	}
	else{
		Return FALSE;
	}
	}



}
