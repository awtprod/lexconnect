<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Orders extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['service_documents','street','city','state','zipcode','county','courtcase','fileDate','defendant','plaintiff','reference','court','state','case','company','documents'];

	public static $rules = [
		'defendant' => 'required',
		'plaintiff' => 'required',
		'court' => 'required',
	];

	public static $defendant = [
		'defendant' => 'required',
		'street' => 'required',
		'city' => 'required',
		'state' => 'required',
		'zipcode' => 'required'

	];

	public static $file = [
		'service_documents' =>  'mimes:pdf|max:20000',
	];
	


	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'orders';

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

	public function isValidDefendant()
	{
		$messages = array(
				'required' => 'This field is required.',
		);

		$validation = Validator::make($this->attributes, static::$defendant, $messages);

		if ($validation->passes()) return true;

		$this->errors = $validation->messages();

		return false;
	}

	public function validFile()
	{

		$validation = Validator::make($this->attributes, static::$file);

		if ($validation->passes()) return true;

		$this->errors = $validation->messages();

		return false;
	}

    /*
	public function SelectServer($zipcode)
	{
	if(!empty($zipcode)){
		$input1 = urlencode($zipcode);
	}
	else{
	$input1 = urlencode($this->attributes["zipcode"]);
	}
	$req = "http://api.geosvc.com/rest/usa/{$input1}/nearby?pt=vendor&d=20&apikey=60e6b26c492541e0946cc43f57f33489&format=json";
	$result = (array) json_decode(file_get_contents($req), true);
	if(empty($result)){
		return 1;
	}
	$distance = array();
	foreach($result as $select){
	
		$score = DB::table('company')->where('id', $select["UserData"])->pluck('score');
		$distance[$select["UserData"]] = (1-($score))*($select["Distance"]["Value"]);
		
	}
	$server = array_keys($distance, min($distance));
        dd($server);
	foreach($server as $servers){
		return $servers;
	}

	}
    */


}
