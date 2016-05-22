<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Locations extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait, SoftDeletingTrait;
	public $timestamps = true;
	protected $fillable = ['geo_id','company_id','street','street2','city','state','zipcode', 'name'];

	protected $dates = ['deleted_at'];
	
	public static $rules = [
		'name' => 'required',
		'street' => 'required',
		'city' => 'required',
		'state' => 'required',
		'zipcode' => 'required'
		];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'locations';

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

	public function postLocation($data)
	{


// set up the curl resource
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.geosvc.com/rest/udp?apikey=60e6b26c492541e0946cc43f57f33489");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data)
		));

// execute the request

		$output = curl_exec($ch);

// close curl resource to free up system resources
		curl_close($ch);

		return $output;

	}

	public function deleteLocation($data)
	{

		// set up the curl resource
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.geosvc.com/rest/udp/".$data."?apikey=60e6b26c492541e0946cc43f57f33489");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");


// execute the request

		$output = curl_exec($ch);

// close curl resource to free up system resources
		curl_close($ch);

//If output is empty, location was deleted
		if(empty($output)){
			return true;

		}
		else{

			return false;

		}

	}



}
