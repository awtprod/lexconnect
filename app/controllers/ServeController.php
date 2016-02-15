<?php

class ServeController extends \BaseController {
	public function __construct (Tasks $tasks, Orders $orders, Jobs $jobs, Serve $serve, Rules $rules)
	{
	
		$this->tasks = $tasks;
		$this->orders = $orders;
		$this->jobs = $jobs;
		$this->serve = $serve;
		$this->rules = $rules;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		Return "blah!";
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function add()
	{
		Return View::make('serve.create')->with('tasks_id', Input::get('tasks_id'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		if ( ! $this->serve->fill($input)->isValid())
	{
		return Redirect::back()->withInput()->withErrors($this->serve->errors);	
	}
	
	$task = Tasks::whereId(Input::get('taskId'))->first();
	$job = Jobs::whereId(Input::get('jobId'))->first();
    $order = Orders::whereId($task->order_id)->first();
//Save serve
            $serve = new Serve;
            $serve->date = Input::get('date');
            $serve->time = Input::get('time');
        if (Input::get('sub-serve') === 'yes') {
            $serve->sub_served = '1';
        }
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
            $serve->save();

		//Find servee
		$servee = Servee::whereId($job->servee_id)->first();

		//Mark servee as "served" or set status to "1"
		$servee->status = '1';
		$servee->save();

//Complete Task
	$this->tasks->TaskComplete(Input::get('taskId'));

	//Determine if Dec of Mailing is needed
        if (Input::get('sub-serve') === 'yes') {
      /*
       //Determin state that case is filed in
        $state = Orders::whereId($job->order_id)->pluck('state');
        
        if($this->rules->DecOfMailing($state)){
        
        //If Dec of Mailing is needed, launch tasks
	$send_task = array('jobs_id' => $job->id, 'vendor' => $task->vendor, 'orders_id' => $task->order_id, 'court' =>$order->court, 'process' => $task->process);
	$this->tasks->DecOfMailing($send_task);

            //Reforecast all tasks

            $this->tasks->TaskReproject(Input::get('taskId'));

        	}
        */}
	
	return Redirect::route('jobs.show', $job->id);
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
