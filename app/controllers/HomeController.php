<?php

class HomeController extends BaseController {
	public function __construct (Orders $orders, Tasks $tasks, Jobs $jobs, Invoices $invoices)
	{
	
		$this->orders = $orders;
		$this->tasks = $tasks;
		$this->jobs = $jobs;
		$this->invoices = $invoices;

	}
	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function redirect()
	{
		
		if(Auth::check()){
			
		return Redirect::route('home.index');
			
		}
		else{
			
		return View::make('hello');
		}
	}
	
	public function index()
	{
	
	//If User is Admin
	if(Auth::user()->user_role=='Admin'){
		
	}
	
	//If User is Vendor
	
	elseif(Auth::user()->user_role=='Vendor'){
		
	//Find current tasks
	
	$openjobs = DB::table('jobs')
				->join('tasks', function($opentasks)
			{
				$opentasks->on('jobs.id', '=', 'tasks.job_id')
				->where('jobs.vendor', '=', Auth::user()->company_id)
				->where('jobs.status', '=', 0)
				->whereNULL('jobs.completed')
				->whereNULL('tasks.completion')
				->where('tasks.status', '=', 1)
				->where('tasks.vendor', '=', Auth::user()->company_id);
			})
			->select('tasks.job_id', 'jobs.defendant', 'tasks.id', 'tasks.process', 'tasks.deadline')
			->orderBy('tasks.deadline', 'asc')
			->get();

			
	//Build tasks list
	$tasklist = array();
	
	foreach($openjobs as $job){
		$tasklist[$job->job_id]['task'] = $this->tasks->TaskStatus($job->process);
		$tasklist[$job->job_id]['link'] = $this->tasks->TaskLink($job->id);
		$tasklist[$job->job_id]['deadline'] = date("m/d/y", strtotime($job->deadline));
		$tasklist[$job->job_id]['id'] = $job->job_id;
		$tasklist[$job->job_id]['defendant'] = $job->defendant;
	}
	
	Return View::make('home.vendor')->with(['job' => $tasklist]);
	}
	
	//If User is Client
	
	elseif(Auth::user()->user_role=='Client'){
		
	//Find Open Orders
	
	$openorders = DB::table('orders')
					->where('company', Auth::user()->company)
					->whereNULL('completed')->orderBy('created_at', 'asc')->get();
					
	Return View::make('home.client')->with(['openorders' => $openorders]);
		
	}
	
	//If Not Logged In, return to login screen
	
	else{
	
	return redirect::route('login');
	
	}
		
	}

}
