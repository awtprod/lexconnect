<?php
use Carbon\Carbon;
\Carbon\Carbon::setToStringFormat('m/d/y');
class AttemptsController extends \BaseController {
	protected $attempts;
	

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('attempts.create')->with('tasks_id', Session::get('tasks_id'))->with('job_id', Session::get('job_id'))->with('token', Input::get('_token'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$task = Tasks::whereId(Input::get('taskId'))->first();
		$order = Orders::whereId($task->order_id)->first();
		$job = Jobs::whereId(Input::get('jobId'))->first();

//Save attempt
		if($input["attempt-result"] == "attempt" OR $input["attempt-result"] == "non-served") {
			Attempts::create([
				'date' => Input::get('date'),
				'time' => Input::get('time'),
				'description' => Input::get('description'),
				'job' => Input::get('jobId'),
			]);

			//Redirect based on service status
			if ($input["attempt-result"] == "non-served") {


				//Create task to invoice job
				$this->tasks->CreateTasks(['judicial' => 'judicial', 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $order->id, 'county' => $order->county, 'process' => 'invoice', 'priority' => 'Routine', 'client' => 'Admin', 'state' => $order->state]);


				//Find servee
				$servee = Servee::whereId($job->servee_id)->first();

				//Mark servee as "non-serve" or set status to "2"
				$servee->status = '2';
				$servee->save();

				//Save reason for non-serve to job
				if($input["reason"]=="other"){
						$job->reason = $input["reason_other"];
				}
				else{
						$job->reason = $input["reason"];
				}

				//If new address is proved, save to job
				if($input["new_address_give"]=="true"){
						$job->moved_street = $input["Street"];
						$job->moved_street2 = $input["Street2"];
						$job->moved_city = $input["City"];
						$job->moved_state = $input["State"];
						$job->moved_zipcode = $input["Zipcode"];
				}
						$job->save();

				//Complete task
				$this->tasks->TaskComplete(Input::get('taskId'));
			}
			else {

				$this->tasks->TaskForecast(Input::get('taskId'));
			}
		}
		elseif($input["attempt-result"] == "served"){

			//Create task to invoice job
			$this->tasks->CreateTasks(['judicial' => 'judicial', 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $order->id, 'county' => $order->county, 'process' => 'invoice', 'priority' => 'Routine', 'client' => 'Admin', 'state' => $order->state]);
			
			$serve = new Serve;
			$serve->date = Input::get('date');
			$serve->time = Input::get('time');
			$serve->serve_type = Input::get('serve_type');
			$serve->served_upon = Input::get('served_upon');
			$serve->age = Input::get('age');
			$serve->gender = Input::get('gender');
			$serve->race = Input::get('race');
			$serve->height = Input::get('height');
			$serve->weight = Input::get('weight');
			$serve->relationship = Input::get('relationship');
			$serve->hair = Input::get('hair');
			$serve->glasses = Input::get('glasses');
			$serve->moustache = Input::get('Moustache');
			$serve->beard = Input::get('beard');
			$serve->job_id = $task->job_id;
			$serve->order_id = $task->order_id;
			$serve->servee_id = $job->servee_id;

			if($input["location"]=="other"){

				$serve->street = $input["New_Street"];
				$serve->street2 = $input["New_Street2"];
				$serve->city = $input["New_City"];
				$serve->county = $input["county"];
				$serve->state = $input["New_State"];
				$serve->zipcode = $input["New_Zipcode"];
			}
			else{
				$serve->street = $job->street;
				$serve->street2 = $job->street2;
				$serve->city = $job->city;
				$serve->county = $job->county;
				$serve->state = $job->state;
				$serve->zipcode = $job->zipcode;
			}
			$serve->save();

			//Find servee
			$servee = Servee::whereId($job->servee_id)->first();

			//Mark servee as "served" or set status to "1"
			$servee->status = '1';
			$servee->save();

//Complete Task
			$this->tasks->TaskComplete(Input::get('taskId'));

			//Determine if Dec of Mailing is needed
			if (Input::get('serve_type') != 'Personal') {


                  if(States::whereAbbrev($order->state)->pluck('mailing')){

					  $county = Orders::whereId($task->order_id)->first();

                  //If Dec of Mailing is needed, launch tasks
					$this->tasks->CreateTasks(['judicial' => 'judicial', 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $task->order_id, 'county' => $county, 'process' => 'mailing', 'priority' => 'Routine', 'client' => 'Admin', 'state' => $order->state]);

                      }
                  }
		}

		//Update server score
		$this->tasks->ServerScore(Input::get('taskId'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
