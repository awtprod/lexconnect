<?php
use Carbon\Carbon;
class ReprojectionsController extends \BaseController {
	public function __construct (Orders $orders, Tasks $tasks, Jobs $jobs, Reprojections $reprojections)
	{
	
		$this->orders = $orders;
		$this->tasks = $tasks;
		$this->jobs = $jobs;
		$this->reprojections = $reprojections;

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
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
	//Validate form data
		if ( ! $this->reprojections->fill($input)->isValid())
	{
		return Redirect::back()->withInput()->withErrors($this->reprojections->errors);	
	}
	//Check to see if new deadline is within max days allowed
	
	$task = DB::table('tasks')->where('id', Input::get('task_id'))->first();
	
	//If task reprojection days = 0. Set max reprojection to 3
	if($task->days == 0){
	$days = 3;	
	}
	else{
	$days = $task->days;
	}
	
	//Determine number of days reprojected
	$y = date("Y", strtotime(Input::get('reprojected')));
	$m = date("m", strtotime(Input::get('reprojected')));
	$d = date("d", strtotime(Input::get('reprojected')));
	$difference = Carbon::now()->diffInDays(Carbon::createFromDate($y, $m, $d));

	//If difference is greater than max reprojection days, fail reprojection
	if($difference > $days){
		return Redirect::back()->withInput()->withErrors(['reprojected' => 'Reprojection exceeded number of days allowed']);		
	}
	
	$job = DB::table('jobs')->where('id', $task->job_id)->first();
	
	//Save Reprojection
	$reprojection = new Reprojections;
	$reprojection->task_id = $task->id;
	$reprojection->job_id = $job->id;
	$reprojection->order_id = $job->order_id;
	$reprojection->servee_id = $job->servee_id;
	$reprojection->reprojected = Input::get('reprojected');
	$reprojection->description = Input::get('description');
	$reprojection->save();
	
	//Reforecast Task
	DB::table('tasks')->where('id', $task->id)->update(array('deadline' => Input::get('reprojected')));
	
	//Reforecast upcoming Tasks
	$this->tasks->TaskReproject($task->id);	
	
	Return Redirect::route('jobs.index');
	
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//Find all reprojections for specific task
		$reprojections = Reprojections::whereTaskId($id)->get();
		
		return View::make('reprojections.index')->with(['reprojections' => $reprojections])->with('task_id', $id);
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
