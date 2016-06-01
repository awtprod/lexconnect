<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class DocumentsServed extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['documentServed','date', 'time', 'order_id',];
	
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

    public function saveDocType($docArray){

		//Set doc type
		if($docArray["document"]["type"] == "other"){

			$document = $docArray["document"]["other"];
		}
		else{

			$document = $docArray["document"]["type"];
		}

		//Check to see if doc type has already been added to Order
		$prevAddedDoc = DocumentsServed::whereDocument($document)
										->whereOrderId($docArray["orderId"])->first();

		//If doc type has not been added, add new doc type
        if(empty($prevAddedDoc)){

			$documentsServed = new DocumentsServed;
            $documentsServed->document = $document;
            $documentsServed->order_id = $docArray["orderId"];
            $documentsServed->save();
        }

		return true;
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
