<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ClientRates extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['client','state','discount','runMax','runSurcharge','runFlat','serviceMax','serviceSurcharge','serviceFlat','postMax','postSurcharge','postFlat'];
	
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
	protected $table = 'clientrates';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

    public function insertDocs($docArray){

    foreach($docArray["input"]["documents"] as $document){

        if(!empty($docArray["input"]["$document"])){

            $documentsServed = new DocumentsServed;
            $documentsServed->document = $docArray["input"]["$document"];
            $documentsServed->orderId = $docArray["orderId"];
            $documentsServed->save();
        }
    }
    }

	public function isValid()
	{
		
		$validation = Validator::make($this->attributes, static::$rules);
		
		if ($validation->passes()) return true;
		
		$this->errors = $validation->messages();
		
		return false;
	}



}
