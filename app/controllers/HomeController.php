<?php

class HomeController extends BaseController {

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
	//Find current tasks
	
	$tasks = Tasks::whereNULL('completion')->whereStatus(1)->take(10)->orderBy('deadline', 'desc')->get();


	/*
	//Build tasks list
	$tasklist = array();
	
	foreach($openjobs as $job){
		
		$vendor = DB::table('company')->where('id', $job->vendor)->pluck('name');
		
		$tasklist[$job->job_id]['task'] = $job->service;
		$tasklist[$job->job_id]['priority'] = $job->priority;
		$tasklist[$job->job_id]['defendant'] = $job->defendant;
		$tasklist[$job->job_id]['deadline'] = date("m/d/y", strtotime($job->deadline));
		$tasklist[$job->job_id]['id'] = $job->job_id;
		$tasklist[$job->job_id]['vendor'] = $vendor;
		$tasklist[$job->job_id]['order_id'] = $job->order_id;
	}
	*/
	Return View::make('home.admin')->with(['tasks' => $tasks]);
		
	}
	
	//If User is Vendor
	
	elseif(Auth::user()->user_role=='Vendor'){
		
	//Find current tasks

	$tasks = Tasks::whereNULL('completion')->whereStatus(1)->whereGroup(Auth::user()->company_id)->take(10)->orderBy('deadline', 'desc')->get();

/*

		//Build tasks list
	$tasklist = array();
	
	foreach($openjobs as $job){
		$tasklist[$job->job_id]['task'] = $this->tasks->TaskStatus($job->id);
		$tasklist[$job->job_id]['link'] = $this->tasks->TaskLink($job->id);
		$tasklist[$job->job_id]['deadline'] = date("m/d/y", strtotime($job->deadline));
		$tasklist[$job->job_id]['id'] = $job->job_id;
		$tasklist[$job->job_id]['defendant'] = $job->defendant;
	}
	*/
	Return View::make('home.vendor')->with(['tasks' => $tasks]);
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
