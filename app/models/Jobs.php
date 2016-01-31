<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class Jobs extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['service','priority','defendant','street','street2','city','state','county','zipcode','order_id','notes'];
	
	public static $rules = [
		'defendant' => 'required',
		'street' => 'required',
		'city' => 'required',
		'zipcode' => 'required|min:5|max:5',
	];
	public static $file_rules = [
		'Executed_proof' => 'mimes:pdf|max:10000',
        'Executed_Declaration' => 'mimes:pdf|max:10000',
		];

	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jobs';

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
	public function ValidFile()
	{
		
		$validation = Validator::make($this->attributes, static::$file_rules);
		
		if ($validation->passes()) return true;
		
		$this->errors = $validation->messages();
		
		return false;
	}

	public function addressVerification($input){

		// Customize this (get ID/token values in your SmartyStreets account)
		$authId = urlencode("e7bbbae3-ebf8-4909-91bb-3de8c08b3047");
		$authToken = urlencode("XfHAxXlymvPsLfi0X6UQ");

// Address input
		$input1 = urlencode($input["street"]);
		$input2 = urlencode($input["street2"]);
		$input3 = urlencode($input["city"]);
		$input4 = urlencode($input["state"]);
		$input5 = urlencode($input["zipcode"]);

// Build the URL
		$req = "https://api.smartystreets.com/street-address/?street={$input1}&street2={$input2}&city={$input3}&state={$input4}&zipcode={$input5}&auth-id={$authId}&auth-token={$authToken}";

// GET request and turn into associative array
		$result = (array) json_decode(file_get_contents($req), true);

		return $result;
	}

	public function SelectServer($serverData)
	{
	if(!empty($serverData['zipcode'])){
		$input1 = urlencode($serverData['zipcode']);
	}
	else{
	$input1 = urlencode($this->attributes["zipcode"]);
	}
	$req = "http://api.geosvc.com/rest/usa/{$input1}/nearby?pt=vendor&d=20&apikey=60e6b26c492541e0946cc43f57f33489&format=json";
	$result = (array) json_decode(file_get_contents($req), true);
	if(empty($result)){
		return 1;
	}
	$vendor = array();

	foreach($result as $key => $select){

     //Find status of server
     $suspended =  DB::table('company')
                                      ->where('id', $select["UserData"])
                                      ->where('status', 1)->pluck('status');

	//Find job
		$job = Jobs::whereId($serverData['jobId'])->first();

     //Find if server has been previously assigned to job
     if($serverData['jobId'] != 'NULL') {

         $previousAssignment = DB::table('tasks')
             ->where('group', $select["UserData"])
             ->where('job_id', $serverData['jobId'])->pluck('group');
     }

	//Retrieve rates for vendor
		$rates = DB::table('vendorrates')->where('vendor', $select["UserData"])
										->where('state', $serverData['state'])
										->where('county', $serverData['county'])->first();

	//If server does not serve area, remove
		if(empty($rates)){

			unset($result[$key]);

		}
		else {
			//Set variables
			$flatVar = $serverData['process'] . 'Flat';
			$rate = $rates->$flatVar;

			$baseVar = $serverData['process'] . 'Base';
			$base = $rates->$baseVar;

			$mileVar = $serverData['process'] . 'Mileage';
			$mileage = $rates->$mileVar;

			//Determine cost for job

			if (empty($rate) OR $rate == '0') {

				$vendor["rate"][$select["UserData"]] = ($base) + (($mileage) * ($select["Distance"]["Value"]));

				if($serverData['priority'] == "Rush" OR $serverData['priority'] == "SameDay"){

					$surVar = $serverData['process'] . $serverData['priority'];
					$vendor["rate"][$select["UserData"]] = ($rates->$surVar) + (($base) + (($mileage) * ($select["Distance"]["Value"])));
				}

			}
			elseif($serverData['priority'] == "Rush" OR $serverData['priority'] == "SameDay"){

				$surVar = $serverData['process'] . $serverData['priority'];
				$vendor["rate"][$select["UserData"]] = ($rates->$surVar) + $rates->$flatVar;
			}

			//Find client
			$client = DB::table('company')->where('name', $serverData['client'])->first();

			//Find maximum rate set by client
			$maxRate = DB::table('clientrates')->where('client', $client->id)->where('state', $job->state)->pluck($serverData['process'] . 'Max');

			//Remove unqualified servers
			if (!empty($suspended)) {

				unset($result[$key]);

			} elseif (!empty($previousAssignment)) {

				unset($result[$key]);

			} elseif ($vendor["rate"][$select["UserData"]] > $maxRate) {

				unset($result[$key]);

			} else {
				$score = DB::table('company')->where('id', $select["UserData"])->pluck('score');

				$vendor["weight"][$select["UserData"]] = (((0.45) * ((1 - ($score)) * ($select["Distance"]["Value"]))) + ((0.55) * ((1 - ($score)) * $vendor["rate"][$select["UserData"]])));
			}
		}
	}
	$server = array_keys($vendor["weight"], min($vendor["weight"]));

		$data = array();

	foreach($server as $servers){

		$data["server"] = $servers;
		$data["rate"] = $vendor["rate"][$servers];

		return $data;
	}

	}

	public function ReAssignServer($newServer){

		//Find previous server
		$previousServer = Tasks::whereJobId($newServer['jobId'])->OrderBy('sort_order', 'asc')->first();

		//Create task for new server
		$newTask = new Tasks;
		$newTask->job_id = $newServer['jobId'];
		$newTask->order_id = $newServer['orderId'];
		$newTask->group = $newServer['vendor'];
		$newTask->process = $previousServer->process;
		$newTask->sort_order = $previousServer->sort_order;
		$newTask->days = $previousServer->days;
		$newTask->window = $previousServer->window;
		$newTask->status = 1;
		$newTask->deadline = Carbon::now()->addDays($previousServer->days);
		$newTask->save();

		//Assign Job to new server
		$assignJob = Jobs::whereId($newServer['jobId'])->first();

		$assignJob->vendor = $newServer['vendor'];
		$assignJob->save();

		//Find upcoming tasks
		$futureTasks = Tasks::whereJobId($newServer['jobId'])->whereGroup($previousServer->group)->whereNULL('completion')->get();

		//Loop through all tasks
		foreach($futureTasks as $futureTask) {

			$task = Tasks::whereId($futureTask->id)->first();
			$task->group = $newServer['vendor'];
			$task->save();

		}

		return $newTask->id;

	}

	public function JobComplete($id){

		//Mark Job as complete
		$job = Jobs::whereId($id)->first();
		$job->completed = Carbon::now();
		$job->save();

		//Check to see if any dependent processes
		$depProcess = Dependent::wherepredProcess($job->process)->get();

		//Find jobs on pending completion of prior job, if any processes
		if(!empty($depProcess)) {

			$depJobs = array();

			foreach ($depProcess as $process) {

				$depJobs[$process->dep_process] = Jobs::whereProcess($process->dep_process)
													->whereOrderId($job->order_id)
													->whereStatus(0)->get();
			}

		//Check to see if any additional dependent jobs, if any
			if(!empty($depJobs)){

				foreach($depProcess as $proces){

					foreach($depJobs[$process->dep_process] as $depJob){

						$addProcesses = Dependent::wheredepProcess($depJob->process)
												->where('process', '!=', $job->process)->get();

						//If additional dependent processes exist, check for existing jobs

						if(!empty($addProcesses)){

							$addJob = array();

							foreach($addProcesses as $addProcess){

								$addJob = Jobs::whereProcess($addProcess->pred_process)
												->whereNull('completed')
												->whereorderId($job->order_id)->get();
							}

								if(!empty($addJob)){

								}

							//If no active dependent jobs, remove hold on task(s)
								else{

									$depTask = Tasks::wherejobId($depJob->id)
													->whereNull('completion')
													->orderBy('sort_order', 'asc')->first();

									$depTask->status = 1;
									$depTask->save();

									$this->tasks->Forecast($depTask->id);
								}
						}

					}
				}

			}
		}


	}
	
	public function HoldJob($id){
	
	$job = Jobs::whereId($id)->first();
	$job->status = 1;
	$job->save();
	
	return true;
	
	}
	
	public function RemoveHold($id){
	
	$job = Jobs::whereId($id)->first();
	$job->status = 0;
	$job->save();
	
	return true;
		
	}
	
	public function CancelJob($id){
		
	//Find what step the job is on
	$task = Tasks::whereJobId($id)->whereNULL('completion')->first();
	
	//If filing task, complete all tasks
	if($task->process <= 5){
	
	//Save completion date for task
	$task->completion = Carbon::now();
	$task->save();	
	
	//Save process to cache to complete upcoming tasks
	Cache::put('process', $task->process, 5);
	Cache::increment('process');
	
	//Determine if this was the last task for the job
	$tasks_final = Tasks::OrderBy('id', 'desc')
						->whereJobId($task->job_id)
						->whereProcess(Cache::get('process'))->first();
			
	//If it is not the last task, complete remaining tasks
	if(!empty($tasks_final)){
		
			$first = true;
			
			for($process = Cache::get('process'); $process<=10; $process++){
			$tasks_next = Tasks::OrderBy('id', 'desc')
						->whereJobId($task->job_id)
						->whereProcess($process)->first();
			if(!empty($tasks_next)){
			
			//Update status of next task
			if($first){
			$tasks_next->completion = Carbon::now();
			$tasks_next->save();	
			$first = false;
			}
			//Update remaining tasks
			else{
			$tasks_next->completion = Carbon::now();
			$tasks_next->save();	
			}
			}
			}
			Cache::forget('process');
			}
	//Complete Job
	$job = Jobs::whereId($id)->first();
	$job->status = 2;
	$job->completed = Carbon::now();
	$job->save();
	
	return true;
	}
	
	//If service task, complete all but proof task
	elseif($task->process == 6){
		
	//Save completion date for task
	$task->completion = Carbon::now();
	$task->save();
	
	return true;
	
	}
	
	}

}
