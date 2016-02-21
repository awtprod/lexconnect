<?php
use Carbon\Carbon;
\Carbon\Carbon::setToStringFormat('m/d/y');
class AttemptsController extends \BaseController {
	protected $attempts;

	public function __construct (Servee $Servee, Documents $Documents, User $user, Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, Processes $processes, Steps $steps, Template $template, Counties $counties)
	{

		$this->orders = $orders;
		$this->tasks = $tasks;
		$this->reprojections = $reprojections;
		$this->jobs = $jobs;
		$this->invoices = $invoices;
		$this->DocumentsServed = $DocumentsServed;
		$this->Processes = $processes;
		$this->Steps = $steps;
		$this->Template = $template;
		$this->Counties = $counties;
		$this->User = $user;
		$this->Documents = $Documents;
		$this->Servee = $Servee;
	}

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


		if ( ! $this->attempts->fill($input)->isValid())
			{
				return Redirect::back()->withInput()->withErrors($this->attempts->errors);	
			}
//Save attempt
        Attempts::create([
            'date' => Input::get('date'),
            'time' => Input::get('time'),
            'description' => Input::get('description'),
            'job' => Input::get('jobId'),
        ]);	
//Update server score
	$this->tasks->ServerScore(Input::get('taskId'));

	//Redirect based on service status
        if (Input::get('non-serve') === 'yes') {

			//Find job
			$job = Jobs::whereId(Input::get('job'))->first();

			//Find servee
			$servee = Servee::whereId($job->servee_id)->first();

			//Mark servee as "non-serve" or set status to "2"
			$servee->status = '2';
			$servee->save();

			//Complete task
            $this->tasks->TaskComplete(Input::get('taskId'));

            return Redirect::to('/');
    	} else {

            $this->tasks->TaskForecast(Input::get('taskId'));

            return Redirect::to('/');
    	}
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
