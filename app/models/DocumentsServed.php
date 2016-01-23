<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DocumentsServed extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['documentServed','date', 'time', 'orderId',];
	
	public static $rules = [
		'documentServed' => 'min:1'
		];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'documentsserved';

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

	public function isValid($input)
	{
		$validation = Validator::make(
				[ 'documentServed' => $input["documentServed"] ],
				[ 'documentServed' => 'min:1' ]
		);
		
		if ($validation->passes()) return true;
		
		$this->errors = $validation->messages();
		
		return false;
	}



}
