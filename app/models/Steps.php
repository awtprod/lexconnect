<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class Steps extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['name', 'process', 'status', 'sortOrder','RoutineOrigDueDate','RushOrigDueDate','SameDayOrigDueDate','RoutineNewDueDate','RushNewDueDate','SameDayNewDueDate','window','group'];
	
	public static $rules = [
		'defendant' => 'required',
		'street' => 'required',
		'city' => 'required',
		'state' => 'required',
		'zipcode' => 'required|min:5|max:5'
	];

	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'steps';

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
