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

	public function status($id){

		//Find job info
		$job = Jobs::whereId($id)->first();

		//Find task info
		$task = Tasks::wherejobId($id)
						->whereNULL('completion')->orderBy('sort_order', 'asc')->first();

		//Find servee info
		$servee = Servee::whereId($job->servee_id)->first();

		//Determine current status of job
		if($job->status == 0){

			return "On Hold";
		}
		elseif($job->status == 1){

			return $task->process;
		}
		elseif($job->status == 2){

			return "Job Canceled";
		}
		elseif($job->completed AND ($servee->status == 1  OR $servee->status == 2)){

			return "Completed";
		}
		elseif($servee->status == 1){

			return "Served";
		}
		elseif($servee->status == 2){

			return "Non Served";
		}

	}

	public function actions($id){


		//Find job info
		$job = Jobs::whereId($id)->first();

		//if job associated with a servee
		if($job->servee_id != 0){

			//Find servee info
			$servee = Servee::whereId($job->servee_id)->first();

			//If job is on hold
			if ($job->status == 0) {

				return $selections = array('1' => 'Resume', '2' => 'Cancel');
			}

			//If job is active
			if ($job->status == 1) {

				return $selections = array('0' => 'Hold', '2' => 'Cancel');
			}

			//If job is canceled or defendant is marked as served/non-served
			if($job->status == 2 OR $servee->status == 1 OR $servee->status == 2){

				return $selections = array('3' => 'Submit New Address');

			}

		}
		else {

			//If job is on hold
			if ($job->status == 0) {

				return $selections = array('1' => 'Resume', '2' => 'Cancel');
			}

			//If job is active
			if ($job->status == 1) {

				return $selections = array('0' => 'Hold', '2' => 'Cancel');
			}

		}

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
