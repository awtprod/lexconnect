<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Servee extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['date','personal'];
	
	public static $rules = [
		'date' => 'required|date',
		'time' => 'required',
		'served_upon' => 'required'
		];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'servee';

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

	public function createServee ($data){

		$servee = new Servee;
		$servee->defendant = $data["defendant"];
		$servee->user = Auth::user()->id;
		$servee->client = $data["company"];
		$servee->order_id = $data["orders_id"];
		$servee->status = 0;
		$servee->save();

		return $servee->id;

	}



}
