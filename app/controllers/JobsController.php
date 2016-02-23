<?php
use Carbon\Carbon;
class JobsController extends \BaseController {
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
		if(Auth::user()->user_role=='Admin'){
		$jobs = Jobs::OrderBy('id', 'asc')->get();

		}
		elseif(Auth::user()->user_role=='Vendor'){
		$jobs = Jobs::whereVendor(Auth::user()->company_id)
				      ->where('completed', NULL)->OrderBy('id', 'asc')->get();

		}
		if(Auth::user()->user_role=='Admin' OR Auth::user()->user_role=='Vendor'){
					return View::make('jobs.index', array('jobs' => $jobs));
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
					
		//Verify Address

		$result = $this->jobs->addressVerification($input);

		if(!empty($result)){

		Return View::make('jobs.verify', ['result' => $result])->with(['jobs' => $jobs])->with(['input' => $input]);

			}
		else {
			Return View::make('jobs.verify')->with(['jobs' => $jobs])->with(['input' => $input]);
		}

	}

	public function store(){

		$input = Input::all();

		$jobs = Jobs::whereorderId($input["orders_id"])
				->whereNotNull('street')->orderBy('id', 'asc')->get();

		$states = DB::table('states')->orderBy('name', 'asc')->lists('abbrev', 'abbrev');

		View::share(['states'=>$states]);
		View::share(['jobs'=>$jobs]);

		//Return to new defendant form
		if(Input::get('edit_create')){

			Return View::make('jobs.create')->with(['input' => $input]);
		}
		
		//Return to new serve address form
		elseif(Input::get('edit_add')){

			Return Redirect::route('jobs.add')->with('edit', TRUE)->with(['input' => Input::all()]);
			
		}

		elseif(Input::get('verify')){

		$client = DB::table('orders')->where('id', Input::get('orders_id'))->pluck('company');

		//Retrieve servee id from form
		$servee_id = Input::get('servee_id');

		//If adding new service address, set status to 0
		if(!empty($servee_id)) {

			$servee = Servee::whereId($servee_id)->first();
			$servee->status = 0;
			$servee->save();

		}

		//If new defendant, create servee
			elseif(empty($servee_id)) {

				$servee_id = DB::table('servee')->insertGetId(
						array('order_id' => input::get('orders_id'), 'client' => $client, 'user' => Auth::user()->id, 'defendant' => input::get('defendant'))
				);


				$order = Orders::whereId(Input::get('orders_id'))->first();

				//Mark to send back to new defendant form

				$new = TRUE;
			}
		else{
		
		$new = FALSE;
		}


		$street2 = Input::get('street2');

		$job = new Jobs;
		$job->servee_id = $servee_id;
		$job->service = 'Process Service';
		$job->priority = $input["service"]["priority"];
		$job->client = $client;
		$job->order_id = Input::get('orders_id');
		$job->defendant = Input::get('defendant');
		$job->street = Input::get('street');
		if(!empty($street2)){
		$job->street2 = Input::get('street2');
		}
		$job->city = Input::get('city');
		$job->state = Input::get('state');
        $job->county = Input::get('county');
		$job->zipcode = Input::get('zipcode');

        if(!empty(Input::get('notes'))) {
            $job->notes = Input::get('notes');
        }
		$job->save();

		//Select Server
		$serverData = array('zipcode' => Input::get('zipcode'), 'state' => Input::get('state'), 'county' => Input::get('county'), 'jobId' => $job->id, 'process' => 'service', 'priority' => $input["service"]["priority"], 'client' => $client);
		$server = $this->jobs->SelectServer($serverData);

			//Create Service Tasks Array
		$sendTask = array('county'=>$order->county,'judicial'=>$order->judicial,'jobs_id' => $job->id, 'vendor' => $server["server"], 'orders_id' => Input::get('orders_id'), 'court' => $order->court, 'process' => $input["type"], 'priority'=>$input["service"]["priority"], 'client' => $client, 'state' => $order->state );


        //Create Service Tasks
        $process = $this->tasks->CreateTasks($sendTask);

		//Check for dependent jobs
		$depData = array('process' => $process, 'orderId'=>Input::get('orders_id'));

		if(! $this->jobs->depProcess($depData)){

		$job->status = 1;

			}
		else{

		$job->status = 0;

			}

		//Update job with process
		$job->vendor = $server["server"];
		$job->process = $process;
		$job->save();
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

		//Save session token to variable
        $token = Session::token();

		//Retrieve job Id from session data, if available
		if(!is_numeric($id)){
		$id = Session::get('job_id');
		}

		//Find task data
		$tasks = Tasks::wherejobId($id)->OrderBy('sort_order', 'asc')->get();

		//Find job data
		$jobs = Jobs::whereId($id)->OrderBy('id', 'asc')->first();


		
		if(Auth::user()->user_role=='Admin' OR Auth::user()->company_id==$jobs->vendor){

				$first = 'true';

                return View::make('jobs.view')->with('jobs', $jobs)->with('tasks', $tasks)->with('token', $token)->with('first', $first);
            }

		else{
			return 'Not Authorized to View Page!';
		}

	}

	public function actions(){

		//Get job info
		$jobs = Jobs::whereId(Input::get('jobId'))->get();

		//If taking an action for all jobs for an order
		if(empty($jobs)){

			$jobs = Jobs::whereorderId(Input::get('orderId'))
						  ->whereNull('completed')->get();

		}

		//Place job on hold
		if(Input::get('action')==0){


			//Update jobs
			foreach($jobs as $job){


				//Update status to 0
				$status = Jobs::whereId($job->id)->first();

				//Check if job is already on hold
				if($status->status == 0){

				}
				else {
					$status->status = 0;
					$status->save();

					//Put current task on hold
					$curTask = Tasks::wherejobId($job->id)
							->whereStatus(1)->first();
					$curTask->status = 0;
					$curTask->save();

				//Create array to notify vendor
				$data = array('job'=>$job->id, 'type'=>'Hold Job');

				//Create task to notify vendor
				$this->jobs->vendorNotification($data);

				}

				$orderId = $job->order_id;
			}

		}

		//Remove hold
		elseif(Input::get('action')==1){

			//Update jobs
			foreach($jobs as $job) {

				//Update status to 1
				$status = Jobs::whereId($job->id)->first();
				$status->status = 1;
				$status->save();

				//Resume current task
				$curTask = Tasks::wherejobId($job->id)
								  ->whereNull('completion')->orderBy('sort_order','asc')->first();
				$curTask->status = 1;
				$curTask->save();

				//Update tasks
				$this->tasks->TaskForecast($curTask->id);

				//Create array to notify vendor
				$data = array('job'=>$job->id, 'type'=>'Resume Job');

				//Create task to notify vendor
				$this->jobs->vendorNotification($data);

				$orderId = $job->order_id;

			}

		}

		//Cancel job
		elseif(Input::get('action')==2){

			foreach($jobs as $job){

				//Update status to 1
				$status = Jobs::whereId($job->id)->first();
				$status->status = 3;
				$status->save();

				//Determine if process service job
				if($job->service == "Process Service"){

				//Determine if any attempts have been made
				$attempts = Attempts::wherejobId($job->id)->get();

				//Determine if defendant has been served
				$served = Serve::wherejobId($job->id)->first();

				//Find servee info
				$servee = Servee::wherejobId($job->id)->first();

				//If no attempts have been made, cancel current task
				if(empty($attempts) AND empty($served)){

					$curTask = Tasks::wherejobId($job->id)
							          ->whereNull('completion')->orderBy('sort_order','asc')->first();
					$curTask->status = 0;
					$curTask->save();

				}

				//If attempts have been made but defendant has not been served, cancel attempts
				elseif(empty($served) AND $servee->status == 0){

					//Complete current task
					$curTask = Tasks::wherejobId($job->id)
									  ->whereNull('completion')->orderBy('sort_order','asc')->first();
					$curTask->completion = Carbon::now();
					$curTask->completed_by = Auth::user()->id;
					$curTask->save();

					//Update next task
					$nextTask = Tasks::wherejobId($job->id)
									   ->whereNull('completion')->orderBy('sort_order','asc')->first();
					$nextTask->status = 1;
					$nextTask->save();

					//Mark serve as "non-serve"
					$servee->status = 2;
					$servee->save();
				}

				}
				else{

					//Pause current task
					$curTask = Tasks::wherejobId($job->id)
								  ->whereNull('completion')->orderBy('sort_order','asc')->first();
					$curTask->status = 0;
					$curTask->save();

				}

				//Create array to notify vendor
				$data = array('job'=>$job->id, 'type'=>'Stop Job');

				//Create task to notify vendor
				$this->jobs->vendorNotification($data);

				$orderId = $job->order_id;

			}

		}

		//Submit new address
		elseif(Input::get('action')==3){

			//Retrieve Order Information
			$servee = Servee::whereId($jobs->servee_id)->first();


			//Retrieve previously attempted addresses
			$prevJobs = Jobs::whereserveeId($servee->id)->orderBy('created_at', 'asc')->get();

			//Retrieve states names
			$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');

			Return View::make('jobs.new')->with(['states' => $states])->with(['servee' => $servee])->with(['jobs' => $prevJobs]);

		}

		Return Redirect::route('orders.show', $orderId);

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

     $orderId = Jobs::whereId(Input::get('job_id'))->pluck('order_id');

		//Save File
		$destinationPath = public_path().'/proofs';
		$file = str_random(6);
		$filename = Input::get('job_id').'_'.$file.'.pdf';
		Input::file('Executed_proof')->move($destinationPath, $filename);
		
		//Update Table
        $dbDoc = new Documents;
        $dbDoc->document = 'Executed_Proof';
        $dbDoc->jobId = Input::get('job_id');
        $dbDoc->orderId = $orderId;
        $dbDoc->filename = $filename;
        $dbDoc->filepath = '/proofs';
        $dbDoc->save();
		
		//Complete Task
		
		$complete = $this->tasks->TaskComplete(Input::get('taskId'));
		
		//Mark Job as Complete
		if($complete == TRUE){
        $jobs = Jobs::whereId(Input::get('job_id'))->first();
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
        $orderId = Jobs::whereId(Input::get('job_id'))->pluck('order_id');
        $jobs = Jobs::whereId(Input::get('job_id'))->first();

		//Save File
		$destinationPath = public_path().'/declarations';
		$file = str_random(6);
		$filename = Input::get('job_id').'_'.$file.'.pdf';
		Input::file('Executed_Declaration')->move($destinationPath, $filename);
		
		//Update Table
        $dbDoc = new Documents;
        $dbDoc->document = 'Executed_Declaration';
        $dbDoc->jobId = Input::get('job_id');
        $dbDoc->orderId = $orderId;
        $dbDoc->filename = $filename;
        $dbDoc->filepath = '/declarations';
        $dbDoc->save();
		
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
	
	if(($task->process == 1 AND $task->step > 1) OR ($task->process == 2 AND $task->step > 0)){

	$this->invoices->CreateInvoice(Input::get('id'));
	
	}
	
	//Determine if filing job and service docs have been uploaded
	if($task->step == 1){
	
	$canceledjob = DB::table('jobs')->where('id', Input::get('id'))->first();

	$servicedocs = DB::table('orders')->where('id', $canceledjob->order_id)->pluck('filed_docs');

	//Removed Document Hold, if service docs have been uploaded
	if(!empty($servicedocs)){

			$this->tasks->WaitingDocs($canceledjob->order_id);
			
		//Update Due Dates of Tasks
		$jobs = DB::table('jobs')->where('order_id', $canceledjob->order_id)
					->where('completed', NULL)->get();

					
		foreach($jobs as $job){
		$futuretask = DB::table('tasks')->where('job_id', $job->id)
					 	->where('completion', NULL)
					 	->where('process', '=', 1)->orderBy('completion', 'asc')->first();

		if(!empty($futuretask)){		  
		$this->tasks->TaskForecast($futuretask->id);
		}
		}
		}	
		
	}
	
	$this->jobs->CancelJob(Input::get('id'));
	
	
	
	return Redirect::back();
		
	}
	
	}

}
