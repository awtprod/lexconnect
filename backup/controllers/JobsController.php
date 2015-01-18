<?php

class JobsController extends \BaseController {
	public function __construct (Orders $orders, Tasks $tasks, Jobs $jobs, Invoices $invoices)
	{
	
		$this->orders = $orders;
		$this->tasks = $tasks;
		$this->jobs = $jobs;
		$this->invoices = $invoices;

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role=='Admin'){
		$jobs = DB::table('jobs')->OrderBy('id', 'asc')->get();
		$job2 = array();
		foreach($jobs as $job){
		$tasks = DB::table('tasks')->OrderBy('process', 'asc')
						->where('job_id', $job->id)
						->where('completion', NULL)->first();
		if(empty($tasks)){
		}
		else{
		$job2[$job->id]['task'] = $this->tasks->TaskStatus($tasks->process);
		$job2[$job->id]['link'] = $this->tasks->TaskLink($tasks->id);
		$job2[$job->id]['deadline'] = date("m/d/y", strtotime($tasks->deadline));
		$job2[$job->id]['id'] = $job->id;
		$job2[$job->id]['defendant'] = $job->defendant;
		}
		}
		}
		elseif(Auth::user()->user_role=='Vendor'){
		$jobs = DB::table('jobs')->OrderBy('id', 'asc')
								->where('vendor', Auth::user()->company_id)
								->where('completed', NULL)->get();
		$job2 = array();
		foreach($jobs as $job){
		$tasks = DB::table('tasks')->OrderBy('process', 'asc')
						->where('job_id', $job->id)
						->where('completion', NULL)->first();
		$tasks_vendor = DB::table('tasks')->OrderBy('process', 'asc')
						->where('job_id', $job->id)
						->where('completion', NULL)
						->where('vendor', Auth::user()->company_id)->first();
		//If Waiting for Documents to be filed/uploaded
		if(empty($tasks)){
		}
		elseif($tasks != $tasks_vendor){
		$job2[$job->id]['task'] = $this->tasks->TaskStatus($tasks_vendor->process)."<br>(Pending Prior Task)";
		$job2[$job->id]['link'] = NULL; 
		$job2[$job->id]['deadline'] = date("m/d/y", strtotime($tasks_vendor->deadline));
		}	
		elseif($tasks->status == 0){
		$job2[$job->id]['task'] = "On Hold";
		$job2[$job->id]['link'] = NULL;
		$job2[$job->id]['deadline'] = date("m/d/y", strtotime($tasks_vendor->deadline));
		}
		else{
		$job2[$job->id]['task'] = $this->tasks->TaskStatus($tasks->process);
		$job2[$job->id]['link'] = $this->tasks->TaskLink($tasks->id);
		$job2[$job->id]['deadline'] = date("m/d/y", strtotime($tasks->deadline));
		}
		$job2[$job->id]['id'] = $job->id;
		$job2[$job->id]['defendant'] = $job->defendant;
		}
		}
		if(Auth::user()->user_role=='Admin' OR Auth::user()->user_role=='Vendor'){
					return View::make('jobs.index', array('job' => $job2));
		}
		else{
			Return "Not Authorized To View!";

		}
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if(Session::get('edit')==TRUE){
		$input = Cache::get('input');
		$orders_id = $input["orders_id"];
		if(!empty($input)){

			View::share('input', $input);
		}
		}
		if(empty($input)){
			$orders_id = Input::get('orders_id');
		}
		if(empty($orders_id)){
			$orders_id = Session::get('orders_id');
		}
		if(Auth::user()->user_role=='Admin' OR Auth::user()->user_role=='Client'){
		$jobs = DB::table('jobs')->where('order_id', $orders_id)
								->whereNotNull('street')->orderBy('id', 'asc')->get();
		$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');
		Return View::make('jobs.create')->with('orders_id', $orders_id)->with(['states' => $states])->with(['jobs' => $jobs]);
	}
		else{
		Return redirect::to('login');	
		}
		}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function verify()
	{
		//retrieve data from input form
		$input = Input::all();
		Cache::put('input', Input::all(),5);
		$orders_id = Input::get('orders_id');
		$servee_id = Input::get('servee_id');
		
		//get data is from new serve address from
		if(!empty($servee_id)){
				
		//Retrieve previously attempted addresses
		$serveejobs = DB::table('jobs')->where('servee_id', Input::get('servee_id'))->orderBy('created_at', 'asc')->get();

		View::share(['serveejobs' => $serveejobs]);
		}
		
		
		if ( ! $this->jobs->fill($input)->isValid())
	{
		return Redirect::back()->withInput()->withErrors($this->jobs->errors);	
	}
		//Retrieve previously entered defendants
		$jobs = DB::table('jobs')
					->where('order_id', Input::get('orders_id'))
					->whereNotNull('street')->orderBy('id', 'asc')->get();
					
		// Customize this (get ID/token values in your SmartyStreets account)
		$authId = urlencode("e7bbbae3-ebf8-4909-91bb-3de8c08b3047");
		$authToken = urlencode("XfHAxXlymvPsLfi0X6UQ");

// Address input
		$input1 = urlencode(Input::get('street'));
		$input2 = urlencode(Input::get('street2'));
		$input3 = urlencode(Input::get('city'));
		$input4 = urlencode(Input::get('state'));
		$input5 = urlencode(Input::get('zipcode'));

// Build the URL
		$req = "https://api.smartystreets.com/street-address/?street={$input1}&street2={$input2}&city={$input3}&state={$input4}&zipcode={$input5}&auth-id={$authId}&auth-token={$authToken}";

// GET request and turn into associative array
		$result = (array) json_decode(file_get_contents($req), true);
		if(!empty($result)){

		Return View::make('jobs.verify', ['result' => $result])->with(['jobs' => $jobs])->with(['input' => $input]);

	}
		else {
			Return View::make('jobs.verify')->with(['jobs' => $jobs])->with(['input' => $input]);
		}

	}
	public function store()
	{
		//Return to new defendant form
		if(Input::get('edit_create')){
			Return Redirect::route('jobs.create')->with('edit', TRUE);
		}
		
		//Return to new serve address form
		elseif(Input::get('edit_add')){
			Return Redirect::route('jobs.add')->with('edit', TRUE)->with(['input' => Input::all()]);
			
		}
		elseif(Input::get('verify')){
		$client = DB::table('orders')->where('id', Input::get('orders_id'))->pluck('company');
		
		$servee_id = Input::get('servee_id');
		if(empty($servee_id)){
			
		$servee_id = DB::table('servee')->insertGetId(
			array('order_id' => input::get('orders_id'), 'client' => $client, 'user' => Auth::user()->id, 'defendant' => input::get('defendant'))
			);
		
		//Mark to send back to new defendant form
		
		$new = TRUE;
		}
		else{
		
		$new = FALSE;
		}
		
		$server = $this->orders->SelectServer(Input::get('zipcode'));
		$street2 = Input::get('street2');
		$job = new Jobs;
		$job->servee_id = $servee_id;
		$job->vendor = $server;
		$job->client = $client;
		$job->order_id = input::get('orders_id');
		$job->defendant = input::get('defendant');
		$job->street = input::get('street');
		if(!empty($street2)){
		$job->street2 = Input::get('street2');
		}
		$job->city = input::get('city');
		$job->state = input::get('state');
		$job->zipcode = input::get('zipcode');
		$job->save();
		
		$send_task = array('jobs_id' => $job->id, 'vendor' => $server, 'orders_id' => Input::get('orders_id'));
		$this->tasks->ServiceTasks($send_task);
		Cache::forget('input');
		
		if($new == TRUE){
		Return Redirect::route('jobs.create')->with('orders_id', Input::get('orders_id'));
		}
		else{
		Return Redirect::route('orders.show')->with('orders_id', Input::get('orders_id'));	
		}
		
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

		if(!is_numeric($id)){
		$id = Session::get('job_id');
		}
		$tasks = DB::table('tasks')->where('job_id', $id)->OrderBy('process', 'asc')->get();
		$jobs = DB::table('jobs')->OrderBy('id', 'asc')->where('id', $id)->first();
		$process = DB::table('tasks')->OrderBy('process', 'asc')
						->where('job_id', $id)
						->where('completion', NULL)->pluck('process');
		if($process == 7){
		
		$servers = DB::table('users')->where('company_id', $jobs->vendor)->orderBy('name', 'asc')->lists('name', 'name');
		View::share(['servers' => $servers]);
		}
		
		if(!empty($jobs->proof)){
		View::share('proof', $jobs->proof);	
		}
		
		if(Auth::user()->user_role=='Admin' OR Auth::user()->company_id==$jobs->vendor){
		$jobtask = array();
		foreach ($tasks as $task){
			$jobtask[$task->id]['description'] = $this->tasks->TaskStatus($task->process);
			$jobtask[$task->id]['deadline'] = date("m/d/y", strtotime($task->deadline));
		}
		return View::make('jobs.show', ['jobtask' => $jobtask])->with('jobs', $jobs)->with('process', $process);
		}
		else{
			return 'Not Authorized to View Page!';
		}

	}

	public function proof(){
	
		//Retrieve Uploaded Docs
		$input = Input::all();
		$id = Input::get('job_id');
		//Validate File
		if ( ! $this->jobs->fill($input)->ValidFile())
	{
		return Redirect::back()->withInput()->withErrors($this->jobs->errors);	
	}
		//Save File
		$destinationPath = public_path().'/proofs';
		$file = str_random(6);
		$filename = Input::get('job_id').'_'.$file.'.pdf';
		Input::file('proof')->move($destinationPath, $filename);
		
		//Update Table
		$jobs = Jobs::whereId(Input::get('job_id'))->first();
		$jobs->proof = $filename;
		$jobs->save();
		
		//Complete Task
		$task = DB::table('tasks')->where('job_id', Input::get('job_id'))
					  ->where('completion', NULL)->orderBy('completion', 'asc')->first();
		
		$complete = $this->tasks->TaskComplete($task->id);
		
		//Mark Job as Complete
		if($complete == TRUE){
		$jobs->completed = Carbon::now();
		$jobs->save();
		
		//Create Invoice
		$this->invoices->CreateInvoice(Input::get('job_id'));
		
		}		
		
		return Redirect::route('jobs.index');
	}
	public function declaration()
	{
		//Retrieve Uploaded Docs
		$input = Input::all();
		$id = Input::get('job_id');
		//Validate File
		if ( ! $this->jobs->fill($input)->ValidFile())
	{
		return Redirect::back()->withInput()->withErrors($this->jobs->errors);	
	}
		//Save File
		$destinationPath = public_path().'/declarations';
		$file = str_random(6);
		$filename = Input::get('job_id').'_'.$file.'.pdf';
		Input::file('declaration')->move($destinationPath, $filename);
		
		//Update Table
		$jobs = Jobs::whereId(Input::get('job_id'))->first();
		$jobs->declaration = $filename;
		$jobs->save();
		
		//Complete Task
		$task = DB::table('tasks')->where('job_id', Input::get('job_id'))
					  ->where('completion', NULL)->orderBy('completion', 'asc')->first();
		
		$complete = $this->tasks->TaskComplete($task->id);
		
		//Mark Job as Complete
		if($complete == TRUE){
		$jobs->completed = Carbon::now();
		$jobs->save();
		
		//Create Invoice
		$this->invoices->CreateInvoice(Input::get('job_id'));		
		
		}
		return Redirect::route('jobs.index');		
	}
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function add()
	{

		if(Auth::user()->user_role=='Admin' OR Auth::user()->user_role=='Client'){
		//If editing form
		$input = Session::get('input');

		if(!empty($input)){

			View::share('input', $input);
				
		}

		//Retrieve Order Information	
		$id = DB::table('servee')->where('id', Input::get('servee_id'))->first();
		
		if(empty($id)){
		
		$id = DB::table('servee')->where('id', $input["servee_id"])->first();
		}
		
		//Retrieve previously attempted addresses
		$jobs = DB::table('jobs')->where('servee_id', $id->id)->orderBy('created_at', 'asc')->get();

		//Retrieve states names
		$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');
		
		Return View::make('jobs.new')->with(['states' => $states])->with(['servee' => $id])->with(['jobs' => $jobs]);
	}
		else{
		Return redirect::to('login');	
		}
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
	public function status(){
	
	$status = Input::get('status');
	
	if($status == 0){
	//Remove Hold
	
	$this->jobs->RemoveHold(Input::get('id'));
	
	return Redirect::back();	
	
	}
	if($status == 1){
	//Place Job on hold
	
	$this->jobs->HoldJob(Input::get('id'));

	
	return Redirect::back();
	
	}
	
	if($status == 2){
	//Cancel Job
	
	//Find current task
	$task = DB::table('tasks')->where('job_id', Input::get('id'))->whereNULL('completion')->first();
	
	if(($task->process > 1 AND $task->process < 5) OR $task->process >5){

	$this->invoices->CreateInvoice(Input::get('id'));
	}
	
	$this->jobs->CancelJob(Input::get('id'));
	
	
	
	return Redirect::back();
		
	}
	
	}

}
