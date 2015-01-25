<?php

class OrdersController extends \BaseController {
	protected $order;
	
	public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices)
	{
	
		$this->orders = $orders;
		$this->tasks = $tasks;
		$this->reprojections = $reprojections;	
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
		$orders = DB::table('orders')->OrderBy('id', 'asc')->where('completed', NULL)->get();
		}
		else{
		$orders = DB::table('orders')->OrderBy('id', 'asc')
				->where('completed', NULL)
				->where('company', Auth::user()->company)->get();
		}
		return View::make('orders.index', array('orders' => $orders));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');

		$courts = DB::table('courts')->orderBy('court', 'asc')->lists('court', 'court');

		if(Auth::user()->user_role=='Admin'){
			$company = DB::table('company')->orderBy('name', 'asc')->lists('name', 'name');
		}
		else{
			$company = Auth::user()->company;
		}

		if(Auth::user()->user_role=='Admin' OR Auth::user()->user_role=='Client'){
		Return View::make('orders.create', array('states' => $states, 'courts' => $courts, 'company' => $company));
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
	 public function getCourts($id)
	 {
	$states = DB::table('states')->where('id', $id)->pluck('abbrev');
	$courts = DB::table('courts')->where('state', $states)->get();
        $options = array();

        foreach ($courts as $court) {
            $options += array($court->id => $court->court);
        }

        return Response::json($options);
	 }
	 public  function clear()
	 {
	 	Session::forget('input');
	 	Return Redirect::back();
	 }
	public function verify()
	{
		$input = Input::all();
		dd($input);
	}
	public function store()
	{
		$input = Input::all();
		
		if ( ! $this->orders->fill($input)->isValid())
	{
		return Redirect::back()->withInput()->withErrors($this->orders->errors);	
	}
		$orders = new Orders;
		$orders->plaintiff = Input::get('plaintiff');
		$orders->defendant = Input::get('defendant');
		$orders->reference = Input::get('reference');
		$orders->case = Input::get('case');
		$orders->state = Input::get('state');
		$orders->court = Input::get('court');
		$orders->user = Auth::user()->id;
		$orders->company = Input::get('company');
		$orders->save();
		$orders_id =  $orders->id;
		
		if (Input::get('filing') === 'yes' OR Input::get('recording') === 'yes') {
		$zipcode = DB::table('courts')->where('court', Input::get('court'))->pluck('zip'); 
		$server = $this->orders->SelectServer($zipcode);
			
		$jobs_id = DB::table('jobs')->insertGetId(
			array('client' => Input::get('company'), 'vendor' => $server, 'order_id' => $orders_id, 'defendant' => input::get('court'), 'state' => input::get('state'), 'zipcode' => $zipcode)
			);
		$send_task = array('jobs_id' => $jobs_id, 'vendor' => $server, 'filing' => Input::get('filing'), 'recording' => Input::get('recording'), 'orders_id' => $orders_id);
		$this->tasks->FilingTasks($send_task);	
		}
			Return Redirect::route('orders.show')->with('orders_id', $orders_id); 
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//If Order is not passed by URL, retrieve from session
		if(!is_numeric($id)){
		$id = Session::get('orders_id');

		}
		if(empty($id)){
		Cache::get('orders_id');	
		}
		Cache::put('orders_id', $id, 30);
		//Retrieve Order
		$showorders = $this->orders->whereId($id)->first();
		
		//Check if user is Admin or Client
		if(Auth::user()->company==$showorders->company OR Auth::user()->user_role=='Admin'){
		
		//If Admin, find all defendants
		if(Auth::user()->user_role=='Admin'){
		$viewservees = DB::table('servee')->where('order_id', $id)->orderBy('id', 'asc')->get();
		}
		else{
		//find all defendants in this case
		$viewservees = DB::table('servee')
			->where('order_id', $id)
			->where('client', Auth::user()->company)->orderBy('id', 'asc')->get();
		}
		$progress = array();
		$completed = array();
		//Loop through defendants and display latest status
		foreach($viewservees as $viewservee){

		//grab latest job
		$job = DB::table('jobs')->where('servee_id', $viewservee->id)->orderBy('completed', 'asc')->first();
		

					
		if($job->completed == NULL){
		//Pass Job Id for holds/cancels
		$progress[$viewservee->id]['job'] = $job->id;
		$progress[$viewservee->id]['order'] = $id;
		

		//get status of latest task
		$task = DB::table('tasks')->where('job_id', $job->id)
					->where('completion', NULL)->orderBy('completion', 'asc')->first();
		if(!empty($task)){
			
		
		//Determine if job or order is on hold		
		if($job->status == 1){
		$progress[$viewservee->id]['hold'] = "Job On Hold";
		$progress[$viewservee->id]['description'] = $this->tasks->TaskStatus($task->process);
		$progress[$viewservee->id]['status'] = '1';
		$progress[$viewservee->id]['deadline'] = date("m/d/y", strtotime($task->deadline));
		}
		

		//If Waiting for Documents to be filed/uploaded
		elseif($task->status == 0 AND $job->status == 0){
			$progress[$viewservee->id]['description'] = "Waiting for Documents";
		$progress[$viewservee->id]['status'] = $this->reprojections->Status($task->id);
		$progress[$viewservee->id]['deadline'] = date("m/d/y", strtotime($task->deadline));		
		}

		elseif($task->process < 7 AND $task->status !== 0 AND $job->status == 0){
		//Current status of the current task			
		$progress[$viewservee->id]['description'] = $this->tasks->TaskStatus($task->process);
		$progress[$viewservee->id]['status'] = $this->reprojections->Status($task->id);
		$progress[$viewservee->id]['deadline'] = date("m/d/y", strtotime($task->deadline));		
		}
	
		//Due date of the current task
		elseif($task->process < 7){
		$progress[$viewservee->id]['deadline'] = date("m/d/y", strtotime($task->deadline));		
		}
		}
		}
		else{

		//Display Completed Serves
		
		$served = DB::table('serve')->where('servee_id', $viewservee->id)->first();
			
			//Determine if defendant was served
			if(!empty($served)){
				$completed[$viewservee->id]['defendant'] = $job->defendant;
				$completed[$viewservee->id]['served_upon'] = $served->served_upon;
				$completed[$viewservee->id]['date'] = date("m/d/y", strtotime($served->date));	
				$completed[$viewservee->id]['time'] = date("h:i A", strtotime($served->time));	
				$completed[$viewservee->id]['street'] = $job->street;
				$completed[$viewservee->id]['city'] = $job->city;	
				$completed[$viewservee->id]['state'] = $job->state;	
				$completed[$viewservee->id]['zipcode'] = $job->zipcode;	
				$completed[$viewservee->id]['proof'] = $job->proof;	
			//Determine if personal or sub-served
				if($served->sub_served == 0){
				$completed[$viewservee->id]['description'] = "Personal Service";
				}
				else{
				$completed[$viewservee->id]['description'] = "Substitute Service";
				$completed[$viewservee->id]['declaration'] = $job->declaration;
				}
			}
			//Cancelled Job
			elseif($job->status == 2){

			$completed[$viewservee->id]['defendant'] = $job->defendant;
			$completed[$viewservee->id]['description'] = "Job Canceled";	
			$completed[$viewservee->id]['served_upon'] = "N/A";
			$completed[$viewservee->id]['date'] = date("m/d/y", strtotime($job->completed));	
			$completed[$viewservee->id]['time'] = date("h:i A", strtotime($job->completed));
			$completed[$viewservee->id]['street'] = $job->street;
			$completed[$viewservee->id]['city'] = $job->city;	
			$completed[$viewservee->id]['state'] = $job->state;	
			$completed[$viewservee->id]['zipcode'] = $job->zipcode;
			$completed[$viewservee->id]['proof'] = NULL;
			}
			//Non-Serve
			else{
			//Find date and time on non-serve
			$non = DB::table('attempts')->where('job', $job->id)->latest('date')->first();
			
			$completed[$viewservee->id]['defendant'] = $job->defendant;
			$completed[$viewservee->id]['description'] = "Non-Serve";
			$completed[$viewservee->id]['served_upon'] = "N/A";
			$completed[$viewservee->id]['date'] = date("m/d/y", strtotime($non->date));	
			$completed[$viewservee->id]['time'] = date("h:i A", strtotime($non->time));
			$completed[$viewservee->id]['street'] = $job->street;
			$completed[$viewservee->id]['city'] = $job->city;	
			$completed[$viewservee->id]['state'] = $job->state;	
			$completed[$viewservee->id]['zipcode'] = $job->zipcode;
			$completed[$viewservee->id]['proof'] = $job->proof;
			}
				
		
		}
		}
		//Find latest filing task
		$filing = DB::table('tasks')->where('order_id', $id)
					    ->where('process', '<', 5)
					    ->where('completion', NULL)->orderBy('completion', 'asc')->first();
		//Show Documents After Filing is Complete

		if(empty($filing)){
			
		$file = array();
		$filingtask = array();
		$recording = array();
		
			if(!empty($showorders->filed_docs)){
		$filing['description'] = "Filed Documents";
		$filing['file'] = $showorders->filed_docs;
		View::share(['filing' => $filing]);
			}
			if(!empty($showorders->rec_docs)){
		$recording['description'] = "Recorded Documents";
		$recording['file'] = $showorders->rec_docs;
		View::share(['recording' => $recording]);
			}
		}
		else{

		//Determine if job or order is on hold
		$job = DB::table('jobs')->where('id', $filing->job_id)->first();
		
		if($job->status == 1 OR $showorders->status == 1){
		
		
		if($job->status == 1){
		$filingtask['job'] = $job->id;
		$filingtask['description'] = "Job On Hold";
		$filingtask['status'] = '1';
		}
		else{
		$filingtask['order'] = $id;
		$filingtask['job'] = $job->id;
		$filingtask['description'] = "Order On Hold";
		$filingtask['status'] = '2';
		}
			
		}
		else{
		//Prepared filing status for View			   
			$filingtask['description'] = $this->tasks->TaskStatus($filing->process);
			$filingtask['deadline'] = date("m/d/y", strtotime($filing->deadline));
			$filingtask['file'] = $showorders->filed_docs;
			$filingtask['job'] = $filing->job_id;
			$filingtask['status'] = '0';

		}
		}
		//Find Invoices for Order
		
		$invoice_data = DB::table('invoices')->where('order_id', $id)->get();
		
		if(!empty($invoice_data)){
		//If there are invoices, loop through them
		
		$invoices = array();
		
		foreach($invoice_data as $data){
			
		$invoices[$data->id]['date'] = date("m/d/y", strtotime($data->created_at));
		$invoices[$data->id]['invoice'] = $data->invoice;
		$invoices[$data->id]['amount'] = $data->client_amt;
		$invoices[$data->id]['id'] = $data->id;
		View::share(['invoices' => $invoices]);
		
		}
		}
		
		//Return Order View	
		return View::make('orders.show')->with(['filingtask' => $filingtask])->with('orders', $showorders)->with('servees', $viewservees)->with(['progress' => $progress])->with(['completed' => $completed])->with(['invoice_data' => $invoice_data]);
		}
		else{
		Return redirect::to('login');	
		}
	}
	
	public function documents()
	{
		//Retrieve Uploaded Docs
		$input = Input::all();
		$file = Input::file('documents');
		if(empty($file)){
		return Redirect::back();
		}
		$id = Input::get('orders_id');
		//Validate File
		if ( ! $this->orders->fill($input)->ValidFiles())
	{
		return Redirect::back()->withInput()->withErrors($this->orders->errors);	
	}
		//Save File
		$destinationPath = public_path().'/service_documents';
		$file = str_random(6);
		$filename = Input::get('orders_id').'_'.$file.'.pdf';
		Input::file('documents')->move($destinationPath, $filename);
		
		//Update Table
		$orders = Orders::whereId(Input::get('orders_id'))->first();
		$orders->filed_docs = $filename;
		$orders->save();

		//Find latest filing task
		$filing = DB::table('tasks')->where('order_id', $id)
					    ->where('process', '<', 6)
					    ->where('completion', NULL)->orderBy('completion', 'asc')->first();
		
		//Removed Document Hold, if there are no filing tasks assoc. w/ order
		if(empty($filing)){
			$this->tasks->WaitingDocs($id);
		//Update Due Dates of Tasks
		$jobs = DB::table('jobs')->where('order_id', $id)
					->where('completed', NULL)->get();
		if(!empty($jobs)){
		foreach($jobs as $job){
		$task = DB::table('tasks')->where('job_id', $job)
					  ->where('completion', NULL)
					  ->where('process', '>' ,4)->orderBy('completion', 'asc')->first();
		if(!empty($task)){
		$this->tasks->TaskForecast($task->id);
		}
		}
		}
		}
		
		//Send back to order page
		Return Redirect::back()->withErrors('Documents Uploaded!');
	}
	
	public function files($file)
	{
		Return View::make('orders.file')->with('file', $file)->with('_token', Session::token());
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function add()
	{
			$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');
			View::share('orders_id', Session::get('orders_id'));
			$jobs = DB::table('jobs')->where('order_id', Session::get('orders_id'))->orderBy('id', 'asc')->get();
			View::share('jobs', $jobs);
			View::share('service_documents', Session::get('service_documents'));
			Return View::make('orders.new', array('states' => $states));
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
	
	public function revise ()
	{
		$input = Session::all();
		View::share('input', $input);
		$orders_id = Session::get('orders_id');
		if(empty($orders_id))
			{
				$orders_id = '0';
			}
		$jobs = DB::table('jobs')->where('order_id', Session::get('orders_id'))->orderBy('id', 'asc')->get();
		if(empty($jobs))
		{
			$jobs = '0';
		}
		View::share('jobs', $jobs);
		View::share('orders_id', $orders_id);
		$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');
		Return View::make('orders.revise', array('states' => $states));
		
	}
	
	public function status(){
	
	$status = Input::get('status');
	
	if($status == 0){
	//Remove hold
	$showorders = $this->orders->whereId(Input::get('id'))->first();
	$showorders->status = 0;
	$showorders->save();
	
	//Remove hold from all jobs
	$orderjobs = DB::table('jobs')->where('order_id', Input::get('id'))
				      ->where('complete', NULL)->get();
	
	foreach($orderjobs as $job){
	
	$this->jobs->RemoveHold($job);
		
	}
	
	return Redirect::back();	
	
	}
	if($status == 1){
	//Place Order on hold
	
	$showorders = $this->orders->whereId(Input::get('id'))->first();
	$showorders->status = 1;
	$showorders->save();
	
	//Place all jobs on hold
	$orderjobs = DB::table('jobs')->where('order_id', Input::get('id'))
				      ->where('complete', NULL)->get();
	
	foreach($orderjobs as $job){
	
	$this->jobs->HoldJob($job);
		
	}
	
	return Redirect::back();
	
	}
	
	if($status == 2){
	//Cancel Order
	
	$showorders = $this->orders->whereId(Input::get('id'))->first();
	$showorders->status = 2;
	$showorders->completed = Carbon::now();
	$showorders->save();
	
	//Cancel all jobs
	$orderjobs = DB::table('jobs')->where('order_id', Input::get('id'))
				      ->where('complete', NULL)->get();
	
	foreach($orderjobs as $job){
	
	$this->jobs->CancelJob($job);
	$this->invoices->CreateInvoice($job);
		
	}	
	
	return Redirect::back();
		
	}
	
	}

}
