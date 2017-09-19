<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class Jobs extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['service','priority','defendant','personal','add_servee','street','street2','city','state','county','zipcode','moved_street','moved_street2','moved_city','moved_state','moved_zipcode','order_id','notes'];
	
	public static $rules = [
		'defendant' => 'required',
		'street' => 'required',
		'city' => 'required',
		'zipcode' => 'required|min:5|max:10',
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

	public function createJob ($data){

		$job = new Jobs;

		if(!empty($data["serveeId"])){
		$job->servee_id = $data["serveeId"];
		}
		if(!empty($data["servee"]["personal"])){
		$job->personal = "yes";
		}
		$job->defendant = $data["defendant"];
		$job->vendor = $data["server"];
		$job->client = $data["client"];
		$job->order_id = $data["orders_id"];
		$job->service = $data["service"];
		$job->priority = $data["priority"];
		$job->status = $data["status"];
		$job->street = $data["street"];
		$job->city = $data["city"];
		$job->county = $data["county"];
		$job->state = $data["state"];
		$job->zipcode = $data["zip"];
		$job->save();

		return $job;

	}

	public function depProcess ($process, $orderId){

		$depProcesses = Dependent::wheredepProcess($process)->get();

		//If additional dependent processes exist, check for existing jobs

		if(!empty($depProcesses)){

			$addJob = array();

			foreach($depProcesses as $depProcess){

				$addJob = Jobs::whereProcess($depProcess->pred_process)
						->whereNull('completed')
						->whereorderId($orderId)->get();
			}

			if(!empty($addJob)){

				return true;
			}

			//If no active dependent jobs, remove hold on task(s)
			else{

				return false;
			}
		}
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

	//Find nearby vendors
	$zipcode = substr($serverData['zipcode'],0,5);

	$req = "http://api.geosvc.com/rest/usa/{$zipcode}/nearby?pt=vendor&d=20&apikey=60e6b26c492541e0946cc43f57f33489&format=json";
	$result = (array) json_decode(file_get_contents($req), true);

		//If no servers, assign to Admin
	if(empty($result)){


		$data = array();

		$data["server"] = 1;
		$data["rate"] = 95;
		$data["addServeeRate"] = 0;
		$data["personalRate"] = 0;
		$data["freePgs"] = 0;
		$data["pageRate"] = 0;

		return $data;
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

			//Find current page rate for vendor
			$pages = VendorRates::whereVendor($select["UserData"])->whereState($serverData['state'])->whereCounty($serverData['county'])->first();

			//Set page variables
			$vendor["freePgs"][$select["UserData"]] = $pages->free_pgs;

			$vendor["pageRate"][$select["UserData"]] = $pages->pg_rate;

			//Find if pagecount is within free page limit
			if($serverData["numPgs"] > $pages->free_pgs AND $pages->free_pgs != 0){

				//if not determine total charge
				$pageRate = ($serverData["numPgs"] - $pages->free_pgs) * ($pages->pg_rate);

			}
			//If within free range, set rate at 0
			else{

				$pageRate = 0;
			}

			//Calculate cost based on flat rate
			$flatVar = $serverData['process'] . 'Flat';
			$vendor["vendorCost"][$select["UserData"]] = $rates->$flatVar;
			$vendor["rate"][$select["UserData"]] = $vendor["vendorCost"][$select["UserData"]] + $pageRate;


			//If vendor doesn't have a flat rate, determine cost
			if (empty($vendor["vendorCost"][$select["UserData"]]) OR $vendor["vendorCost"][$select["UserData"]] == '0') {

			//Determine base rate
				$baseVar = $serverData['process'] . 'Base';
				$base = $rates->$baseVar;

			//Determine mileage rate
				$mileVar = $serverData['process'] . 'Mileage';
				$mileage = $rates->$mileVar;

			//Determine total cost for vendor
				$vendor["vendorCost"][$select["UserData"]] = ($base) + (($mileage) * ($select["Distance"]["Value"]));

			//Add cost of copies to rate
				$vendor["rate"][$select["UserData"]] = $vendor["vendorCost"][$select["UserData"]] + $pageRate;
			}

			//If job is a rush, add surcharge
			if($serverData['priority'] == "Rush" OR $serverData['priority'] == "SameDay"){

				$surcharge = $serverData['process'] . $serverData['priority'];
				$vendor["rate"][$select["UserData"]] += $rates->$surcharge;
				$vendor["vendorCost"][$select["UserData"]] += $rates->$surcharge;
			}

			//if multiple servees at same address, add to rate
			$vendor["addServeeRate"][$select["UserData"]] = 0;

			if($serverData["add_servee"] == "true"){

			//Find additional servee rate
			$vendor["addServeeRate"][$select["UserData"]] = $rates->add_servee;

			//If rate is set to 0, add full rate
				if($vendor["addServeeRate"][$select["UserData"]] == 0){

				$vendor["addServeeRate"][$select["UserData"]] = $vendor["vendorCost"][$select["UserData"]];

				}

			//Determine cost for additional servees
			//$vendor["rate"][$select["UserData"]] += ($vendor["addServeeRate"][$select["UserData"]] * ($serverData["numServees"] - 1)) + (($serverData["numServees"] - 1) * $pageRate);

			}

			//Add cost of service requiring personal service
			$vendor["personalRate"][$select["UserData"]] = $rates->personal;

			if(!empty($serverData["numPersonal"])){

				$vendor["rate"][$select["UserData"]] += $vendor["personalRate"][$select["UserData"]] * $serverData["numPersonal"];

			}


			//Find client
			$client = Company::whereId($serverData['client'])->first();

			//Find maximum rate set by client
			$maxRate = ClientRates::where('client', $client->id)->where('state', $serverData['state'])->pluck($serverData['process'] . 'Max');

			//If client hasn't set max rate, set value at $100
			if(empty($maxRate)){

				$maxRate = 100;
			}



			//Remove unqualified servers
			if (!empty($suspended)) {

				unset($result[$key]);

			} elseif (!empty($previousAssignment)) {

				unset($result[$key]);

			} elseif ($vendor["rate"][$select["UserData"]] > $maxRate) {

				unset($result[$key]);

			} else {
				$score = DB::table('company')->where('id', $select["UserData"])->pluck('score');

				$vendor["weight"][$select["UserData"]] = (((0.45) * ($select["Distance"]["Value"])) + ((0.55) * ($vendor["rate"][$select["UserData"]])));
			}
		}
	}

	//If no qualified servers, assign to admin
	if(empty($result)){

		$data = array();

		$data["server"] = 1;
		$data["rate"] = 0;
		$data["addServeeRate"] = 0;
		$data["personalRate"] = 0;
		$data["freePgs"] = 0;
		$data["pageRate"] = 0;

		return $data;
	}

	$server = array_keys($vendor["weight"], min($vendor["weight"]));

		$data = array();

	foreach($server as $servers){

		$data["server"] = $servers;
		$data["rate"] = $vendor["vendorCost"][$servers];
		$data["addServeeRate"] = $vendor["addServeeRate"][$servers];
		$data["personalRate"] = $vendor["personalRate"][$servers];
		$data["freePgs"] = $vendor["freePgs"][$servers];
		$data["pageRate"] = $vendor["pageRate"][$servers];

		return $data;
	}

	}

	public function ReAssignServer($newServer){

		//Find previous server
		$previousServer = Tasks::whereJobId($newServer['jobId'])->OrderBy('sort_order', 'asc')->first();

		//Find process is still active
		$process = Processes::whereName('Accept Job')->first();

		//Find steps
		$step = Template::whereProcess($process->id)->where('judicial','Both')->orderBy('sort_order', 'asc')->first();

		//Determine if Assign step has already been created
		$task = Tasks::whereJobId($newServer['jobId'])->whereSortOrder($step->sort_order)->first();

		if(!empty($task)){

			$task->vendor = $newServer['vendor'];
			$task->status = 1;
			$task->deadline = Carbon::now()->addDays($previousServer->days);
			$task->completion = "";
			$task->completed_by = "";
			$task->save();
		}

		//Create task for new server
		$newTask = new Tasks;
		$newTask->job_id = $newServer['jobId'];
		$newTask->order_id = $newServer['orderId'];
		$newTask->group = $newServer['vendor'];
		$newTask->process = $process->id;
		$newTask->sort_order = $step->sort_order;
		$newTask->days = $step->RoutineOrigDueDate;
		$newTask->window = $step->window;
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

	public function TotalRate($data)
	{

		//Find service rate
		$rate = ClientRates::whereClient($data["client"])->pluck($data["process"] . 'FeeRate');


		//Find app rate
		$appFee = $data["rate"] * $rate;

		//Determine total to charge client
		$clientRate = ($data["rate"]) + ($data["rate"] * $rate);

		return $clientRate;
	}

	public function JobComplete($id){
		dd($id);


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

public 	function Email($template, $job, $vendor, $subject){

	Mail::send($template, ['job' => $job], function($message) use ($vendor, $subject){
		$message->from('no-reply@lexsend.com', 'LexSend');
		$message->to('awtprod@gmail.com')
			->subject($subject);
	});

}
	
public function vendorNotification($data){



	//Get job info
	$job = Jobs::whereId($data["job"])->first();

	//Get email
	$vendor = Company::whereId($job->vendor)->first();

	//Send email notification

	//Hold notification
	if($data["action"] == '0'){

		$this->Email('emails.hold', $job, $vendor, 'Job Hold Notification');

	}
	//Resume notification
	elseif($data["action"] == '1'){

		$this->Email('emails.resume', $job, $vendor, 'Resume Job Notification');

	}

	//Cancel notification
	elseif($data["action"] == '2'){

		$this->Email('emails.cancel', $job, $vendor, 'Job Cancel Notification');

	}

	/*

	//Find previous task
	$sortOrder = Tasks::wherejobId($job->id)
					    ->whereNotNull('completion')->orderBy('sort_order', 'asc')->pluck('sort_order');

	if(empty($sortOrder)){

		$sortOrder = Tasks::wherejobId($job->id)->orderBy('sort_order', 'asc')->pluck('sort_order');

	}

	//Add one to sort order
	$sortOrder--;

	//Create hold task for vendor
	$tasks = new Tasks;
	$tasks->job_id = $job->id;
	$tasks->order_id = $job->order_id;
	$tasks->service = $job->service;
	$tasks->process = $data["type"];
	$tasks->priority = "Routine";
	$tasks->group = $job->vendor;
	$tasks->sort_order = $sortOrder;
	$tasks->status = 1;
	$tasks->deadline = Carbon::now();
	$tasks->days = 0;
	$tasks->save();
	
	*/
}

}
