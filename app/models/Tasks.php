<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class Tasks extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['proof','service','priority','process','defendant','street','street2','city','state','zipcode','name','company', 'date', 'recording', 'case', 'documents','completion','group','days','total_days','window','deadline','window'];
	
	public static $rules = [
		'defendant' => 'required',
		'street' => 'required',
		'city' => 'required',
		'state' => 'required',
		'zipcode' => 'required|min:5|max:5'
	];
	
	public static $file_rules = [
		'date' => 'required',
		'case' => 'required',
		'documents' => 'required|mimes:pdf|max:10000'
		];
	public static $rec_rules = [
		'date' => 'required',
		'recording' => 'required',
		'documents' => 'required|mimes:pdf|max:10000'
		];

	public static $proof = [
		'proof' => 'required|mimes:pdf|max:1000'
		];
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tasks';

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
	public function ValidRec()
	{
		$validation = Validator::make($this->attributes, static::$rec_rules);
		
		if ($validation->passes()) return true;
		
		$this->errors = $validation->messages();
		
		return false;	
	}

	public function ValidProof()
	{
		$validation = Validator::make($this->attributes, static::$proof);

		if ($validation->passes()) return true;

		$this->errors = $validation->messages();

		return false;
	}
	
	public function ServerScore($id){
			$tasks = Tasks::whereId($id)->first();
			$y = date("Y", strtotime($tasks->deadline));
			$m = date("m", strtotime($tasks->deadline));
			$d = date("d", strtotime($tasks->deadline));
			$difference = Carbon::now()->diffInDays(Carbon::createFromDate($y, $m, $d),false);
			if($difference < 0){
			$abs_difference = abs($difference);
			DB::table('company')->where('id', $tasks->vendor)->increment('total_points', $abs_difference);
			}
			else{
			DB::table('company')->where('id', $tasks->vendor)->increment('points', $difference);
			DB::table('company')->where('id', $tasks->vendor)->increment('total_points', $difference);		
			}	
	}

	//For updating all tasks (inclusive of current task)
	public function TaskForecast($id){
			$taskFirst = Tasks::whereId($id)->first();
			Cache::put('days', Carbon::now()->addDays($taskFirst->days), 5);
			$taskFirst->deadline = Cache::get('days');
			$taskFirst->save();

		//Gather upcoming tasks

		$nextTasks = Tasks::whereJobId($taskFirst->job_id)
				->whereNull('completion')
				->where('id', '!=', $id)->orderBy('sort_order', 'asc')->get();

		if(!empty($nextTasks)){
			//Update upcoming tasks

			foreach($nextTasks as $nextTask){

				$taskNext = Tasks::whereId($nextTask->id)->first();

				//Update status of next task
				$days = Cache::get('days')->addDays($taskNext->days);
				$taskNext->deadline = $days;
				$taskNext->save();
				Cache::put('days', $days, 5);
			}
		}
		Cache::forget('days');
	}

	//For updating all tasks (exclusive of current task)
	public function TaskReproject($id){
			$taskFirst = Tasks::whereId($id)->first();
			
			//Save process and date to cache to update the deadlines of upcoming tasks
			Cache::put('days', $taskFirst->deadline, 5);

			//Gather upcoming tasks

			$nextTasks = Tasks::whereJobid($taskFirst->job_id)
								->whereNull('completion')
								->where('id', '!=', $id)->orderBy('sort_order', 'asc')->get();

			if(!empty($nextTasks)){
			//Update upcoming tasks

			foreach($nextTasks as $nextTask){

			$taskNext = Tasks::whereId($nextTask->id)->first();

			//Update status of next task
			$days = Cache::get('days')->addDays($taskNext->days);
			$taskNext->deadline = $days;
			$taskNext->save();
			Cache::put('days', $days, 5);
			}
			}
			Cache::forget('days');
	}	
	public function TaskComplete($id)
	{
		//Retrieve Current Task
		$tasksFirst = Tasks::whereId($id)->first();

		//Convert deadline date to Carbon-friendly form
		$y = date("Y", strtotime($tasksFirst->deadline));
		$m = date("m", strtotime($tasksFirst->deadline));
		$d = date("d", strtotime($tasksFirst->deadline));

		//Save completion date for task
		$tasksFirst->completion = Carbon::now();
		$tasksFirst->completed_by = Auth::user()->id;
		$tasksFirst->save();

		//Find difference between completion and schedule deadline
		$difference = Carbon::now()->diffInDays(Carbon::createFromDate($y, $m, $d), false);

		//If difference is positive, task is late, decrease server score
		if ($difference < 0) {
			$absDifference = abs($difference);
			DB::table('company')->where('id', $tasksFirst->vendor)->increment('total_points', $absDifference);
		} //Else on-time, increase score
		else {
			DB::table('company')->where('id', $tasksFirst->vendor)->increment('points', $difference);
			DB::table('company')->where('id', $tasksFirst->vendor)->increment('total_points', $difference);
		}

		//Check if job is on hold
		$job = Jobs::whereId($tasksFirst->job_id)->first();

		if($job->status != 1){

			return false;
		}
		else {
			//Save date to cache to update the deadlines of upcoming tasks
			Cache::put('days', Carbon::now(), 5);
			Cache::increment('step');

			//Determine if this was the last task for the job
			$nextTasks = Tasks::whereJobId($tasksFirst->job_id)
					->whereNull('completion')
					->orderBy('sort_order', 'asc')->get();

			//If it is last task, return back to controller
			if (is_null($nextTasks->first())) {

				$this->jobs->JobComplete($tasksFirst->job_id);
			}
				//Duplicate job completion process? Remove if this doesn't break code
				/*
				//Mark Job as complete
				$job = Jobs::whereId($tasksFirst->job_id)->first();
				$job->completed = Carbon::now();
				$job->save();

				//Check to see if any dependent processes
				$depProcess = Dependent::wherepredProcess($job->process)->get();

				//Find jobs on pending completion of prior job, if any processes
				if (!empty($depProcess)) {

					$depJobs = array();

					foreach ($depProcess as $process) {

						$depJobs[$process->dep_process] = Jobs::whereProcess($process->dep_process)
								->whereOrderId($job->order_id)
								->whereStatus(2)->get();
					}

					//Check to see if any additional dependent jobs, if any
					if (!empty($depJobs)) {

						foreach ($depProcess as $proces) {

							foreach ($depJobs[$process->dep_process] as $depJob) {

								$addProcesses = Dependent::wheredepProcess($depJob->process)
										->where('process', '!=', $job->process)->get();

								//If additional dependent processes exist, check for existing jobs

								if (!empty($addProcesses)) {

									$addJob = array();

									foreach ($addProcesses as $addProcess) {

										$addJob = Jobs::whereProcess($addProcess->pred_process)
												->whereNull('completed')
												->whereorderId($job->order_id)->get();
									}

									if (!empty($addJob)) {

									} //If no active dependent jobs, remove hold on task(s)
									else {

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
				*/

				Return true;
			}

			//Determine if there are any dependent jobs
			$predProcesses = Dependent::wheredepProcess($tasksFirst->process)->get();

			//Find active processes

			foreach ($predProcesses as $predProcess) {

				$active = Jobs::whereProcess($predProcess)->whereNull('completed')->get();

			}
			if (empty($active)) {
				//Update upcoming tasks
				$first = true;

				foreach ($nextTasks as $nextTask) {
					$curTask = Tasks::whereId($nextTask->id)->first();

					//Update status of next task
					if ($first == true) {

						$days = Cache::get('days')->addDays($curTask->days);
						$curTask->deadline = $days;
						$curTask->status = 1;
						$curTask->save();

						$first = false;
						Cache::put('days', $days, 5);
					} //Update remaining tasks
					else {
						$days = Cache::get('days')->addDays($curTask->days);
						$curTask->deadline = $days;
						$curTask->save();
						Cache::put('days', $days, 5);
					}
				}
			}
			Cache::forget('days', 'step');
		}




	public function CreateTasks($sendTask)
	{

		//Find default process for county
		$default = Counties::whereState($sendTask['state'])->whereCounty($sendTask['county'])->pluck($sendTask['process']);

        //Find process is still active
		$process = Processes::whereName($default)->first();

		//If process is not active, find first active process for service type
		if(empty($process)){

			$process = Processes::whereService($sendTask['process'])->orderBy('id', 'asc')->first();
		}
		if(empty($process)){

			$process = Processes::whereName(str_replace('_', ' ', $sendTask['process']))->orderBy('id', 'asc')->first();
		}

		//Find steps
        $steps = Template::whereProcess($process->id)->where('judicial','Both')->orWhere('judicial',$sendTask['judicial'])->orderBy('sort_order', 'asc')->get();

        $first = 'true';

        //Save Task List
		foreach($steps as $step){

        $tasks = new Tasks;
		$tasks->job_id = $sendTask['jobs_id'];
		$tasks->order_id = $sendTask['orders_id'];
		$tasks->service = $sendTask['process'];
		$tasks->process = $step->name;
		$tasks->priority = $sendTask['priority'];

			if($step->group == "Vendor") {
                $tasks->group = $sendTask['vendor'];
            }

            if($step->group == "Admin"){

                $tasks->group = 1;

            }
            if($step->group == "Client"){
                $tasks->group = $sendTask['client'];
            }
		$tasks->sort_order = $step->sort_order;
          if($first == "true") {
                $tasks->status = 1;
                $first = 'false';
            }
            else{


                $tasks->status = 0;
           }

		if($sendTask['priority'] == 'Routine') {
			$tasks->deadline = Carbon::now()->addDays($step->RoutineOrigDueDate);
			$tasks->days = $step->RoutineNewDueDate;

		}
		elseif($sendTask['priority'] == 'Rush') {
			$tasks->deadline = Carbon::now()->addDays($step->RushOrigDueDate);
				$tasks->days = $step->RushNewDueDate;

		}
		elseif($sendTask['priority'] == 'SameDay') {
				$tasks->deadline = Carbon::now()->addDays($step->SameDayOrigDueDate);
				$tasks->days = $step->SameDayNewDueDate;

		}

			$tasks->window = $step->window;
			$tasks->save();


		}
		Return $process->id;
	}




	public function QAFail ($taskDeatils){

		$tasks = new Tasks;
		$tasks->job_id = $taskDeatils['jobId'];
		$tasks->order_id = $taskDeatils['orderId'];
		$tasks->vendor = 1;
		$tasks->process = 1;
		$tasks->step = 4;
		$tasks->days = 3;
		$tasks->status = 0;
		$tasks->deadline = Carbon::now()->addDays(3);
		$tasks->save();
	}




}
