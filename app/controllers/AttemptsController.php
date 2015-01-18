<?php
use Carbon\Carbon;
\Carbon\Carbon::setToStringFormat('m/d/y');
class AttemptsController extends \BaseController {
	protected $attempts;
	
	public function __construct (Attempts $attempts, Tasks $tasks)
	{
	
		$this->attempts = $attempts;
		$this->tasks = $tasks;
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
            'job' => Input::get('job'),
        ]);	
//Update server score
	$this->tasks->ServerScore(Input::get('tasks_id'));

	//Redirect based on service status
        if (Input::get('non-serve') === 'yes') {
        	return Redirect::route('tasks.complete')->with('attempts', 1)->with('tasks_id', Input::get('tasks_id'))->with('_token', Input::get('_token'));
    	} else {
    		return Redirect::route('tasks.complete')->with('attempts', 2)->with('tasks_id', Input::get('tasks_id'))->with('_token', Input::get('_token'));
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
