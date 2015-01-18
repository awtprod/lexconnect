<?php
use Carbon\Carbon;
class TasksController extends \BaseController {
	public function __construct (Tasks $tasks, Orders $orders, Jobs $jobs, Invoices $invoices)
	{
	
		$this->tasks = $tasks;
		$this->orders = $orders;
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
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function complete()
	{
		$tasks_id = Input::get('tasks_id');
		if(empty($tasks_id)){
		$tasks_id = Session::get('tasks_id');	
		}

		$tasks = Tasks::whereId($tasks_id)->first();
		//Retrieve Documents
		if($tasks->process == 0 OR $tasks->process == 1 OR $tasks->process == 2 OR $tasks->process == 5 OR $tasks->process == 8){
			
		$complete = $this->tasks->TaskComplete($tasks->id);
		//Mark Job as Complete
		if($complete == TRUE){
		$jobs = Jobs::whereId($tasks->job_id)->first();
		$jobs->completed = Carbon::now();
		$jobs->save();
		
		//Create Invoice
		$this->invoices->CreateInvoice($tasks->job_id);
		}
			return Redirect::route('jobs.index');
		}
		//Filing Complete
		if($tasks->process == 3 OR $tasks->process == 4){
		return Redirect::route('tasks.filing')->with('job_id', $tasks->job_id)->with('tasks_id', $tasks->id);
		}
		//Service Attempts
		if($tasks->process == 6){
		//Non-Service
		if(Session::get('attempts')==1){
		$this->tasks->TaskComplete($tasks->id);
		return Redirect::route('jobs.index');	
		}
		//Service Attempt
		elseif(Session::get('attempts')==2){
		$this->tasks->TaskForecast($tasks->id);
		return Redirect::route('jobs.index');
		}
		else{
		//Enter Attempt
		return Redirect::route('attempts.create')->with('job_id', $tasks->job_id)->with('tasks_id', $tasks->id);
		}
		}
		//Complete POS
		if($tasks->process == 7){
		return Redirect::route('jobs.show')->with('job_id', $tasks->job_id)->with('tasks_id', $tasks->id);
		}
		//Complete Declaration
		if($tasks->process == 9 OR $tasks->process == 10){
		$job = Jobs::whereId($tasks->job_id)->first();
		$servers = DB::table('users')->where('company_id', $job->vendor)->orderBy('name', 'asc')->lists('name', 'name');		
		return View::make('tasks.declaration')->with('job_id', $tasks->job_id)->with('tasks_id', $tasks->id)->with(['job' => $job])->with(['servers' => $servers]);
		}
	}
	
	public function filing(){
		$tasks = DB::table('tasks')->where('id', Session::get('tasks_id'))->first();
		if($tasks->process == 3){
		Return View::make('tasks.filing')->with('jobs_id', Session::get('job_id'))->with('tasks_id', Session::get('tasks_id'));
	}
	else{
		Return View::make('tasks.recording')->with('job_id', Session::get('job_id'))->with('tasks_id', Session::get('tasks_id'));

	} 
	}

	public function documents(){
		//Retrieve Uploaded Docs
		$input = Input::all();
		$case = Input::get('case');
		$recording = Input::get('recording');
		if(!empty($recording)){
		if ( ! $this->tasks->fill($input)->ValidRec())
	{
		return Redirect::back()->withInput()->withErrors($this->tasks->errors)->with('tasks_id', Input::get('tasks_id'));	
	}
		}
	elseif(!empty($case)){
		//Validate File
		if ( ! $this->tasks->fill($input)->ValidFile())
	{
		return Redirect::back()->withInput()->withErrors($this->tasks->errors)->with('tasks_id', Input::get('tasks_id'));	
	}
	}
		$jobs = Jobs::whereId(Input::get('job'))->first();
		$tasks = DB::table('tasks')->where('id', Input::get('tasks_id'))->first();
		$orders = Orders::whereId($jobs->order_id)->first();
	//Save Filed Documents
	if($tasks->process == 3){
		$destinationPath = public_path().'/service_documents';
		$file = str_random(6);
		$filename = $orders->id.'_'.$file.'.pdf';
		Input::file('documents')->move($destinationPath, $filename);
		
		//Update Table
		$orders->filed_docs = $filename;
		$orders->case = Input::get('case');
		$orders->save();
		$complete = $this->tasks->TaskComplete(Input::get('tasks_id'));
		//Mark Job as Complete
		if($complete == TRUE){
		$jobs->completed = Carbon::now();
		$jobs->save();
		}
		//Removed Document Hold
		$this->tasks->WaitingDocs($orders);
	}
	//Save Recorded Documents
	if($tasks->process == 4){
		$destinationPath = public_path().'/recorded_documents';
		$file = str_random(6);
		$filename = $orders->id.'_'.$file.'.pdf';
		Input::file('documents')->move($destinationPath, $filename);
		
		//Update Table
		$orders->rec_docs = $filename;
		$orders->instrument = Input::get('recording');
		$orders->save();
		$complete = $this->tasks->TaskComplete(Input::get('tasks_id'));
		//Mark Job as Complete
		if($complete == TRUE){
		$jobs->completed = Carbon::now();
		$jobs->save();
		}
	}
		//Update Due Dates of Tasks
		$this->tasks->TaskForecast($tasks->id);

		//Send back to order page
		Return Redirect::route('jobs.index');
		
	}
	public function proof(){
		
		//Determine if Serve or Non-Serve
		$serve = DB::table('serve')->where('job_id', Input::get('job_id'))->first();
		View::share(['serve' => $serve]);
		View::share('server', Input::get('server'));
		//If Defendant was served, Generate Proof of Service
		if(!empty($serve)){

		$data = array();
		
		$data['date'] = date("m/d/y", strtotime($serve->date));
		$data['time'] = $serve->time;
		$data['served'] = $serve->served_upon;
		if($serve->sub_served == 0){
		$data['relationship'] = "NAMED DEFENDANT";
		}
		else{
		$data['relationship'] = $serve->relationship;
		}
		
		$proof = DB::table('jobs')->where('id', Input::get('job_id'))->first();

		$file = str_random(6);
		$filename =  $file . '_'. 'proof.pdf';
		$filepath = public_path('proofs/' . $filename);
		$pdf = PDF::loadView('tasks.serve', ['data' => $data], ['proof' => $proof], ['serve' => $serve])->save($filepath);
		DB::table('jobs')->where('id', Input::get('job_id'))->update(array('proof' => $filename));
		return $pdf->download($filename);		
		}
		else{
		$attempts = DB::table('attempts')->OrderBy('date', 'asc')->where('job', Input::get('job_id'))->get();
		$a = array();
		foreach( $attempts as $attempt){
			
		$a[$attempt->job]['date'] = date("m/d/y", strtotime($attempt->date));
		$a[$attempt->job]['time'] = $attempt->time;
		$a[$attempt->job]['description'] = $attempt->description;
		}
		$proof = DB::table('jobs')->where('id', Input::get('job_id'))->first();
		$file = str_random(6);
		$filename =  $file . '_'. 'proof.pdf';
		$filepath = public_path('proofs/' . $filename);

		$pdf = PDF::loadView('tasks.non', ['a' => $a], ['proof' => $proof], ['serve' => $serve])->save($filepath);
		DB::table('jobs')->where('id', Input::get('job_id'))->update(array('proof' => $filename));	
		return $pdf->download($filename);
		}
	}
	public function create_dec(){
	
	}
	public function declaration(){
		//Retrieve data for array for declaration
		$serve = DB::table('serve')->where('job_id', Input::get('job'))->first();
		$job = Jobs::whereId(Input::get('job'))->first();
		$carbon = Carbon::now();
		
		//Data array for declaration
		$data = array();
		
		$data['serve_date'] = date("m/d/y", strtotime($serve->date));
		$data['served'] = $serve->served_upon;
		$data['defendant'] = $job->defendant;
		$data['street'] = $job->street;	
		$data['city'] = $job->city;	
		$data['state'] = $job->state;	
		$data['zip'] = $job->zipcode;
		$data['mail_date'] = date("m/d/y", strtotime(Input::get('mail_date')));	
		$data['declarant'] = Input::get('declarant');	
		$data['year'] = $carbon->year;
		$data['month'] = $carbon->format('F');

		//Create Proof
		$file = str_random(6);
		$filename =  $file . '_'. 'declaration.pdf';
		$filepath = public_path('declarations/' . $filename);
		$pdf = PDF::loadView('tasks.dec_proof', ['data' => $data])->save($filepath);
		DB::table('jobs')->where('id', Input::get('job'))->update(array('declaration' => $filename));
		$this->tasks->TaskComplete(Input::get('tasks_id'));
		return $pdf->download($filename);		
	}

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
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
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
