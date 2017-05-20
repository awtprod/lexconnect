<?php
use Carbon\Carbon;
class JobsController extends \BaseController {


	public function getRate()
	{
		$input = Input::all();

		//Select Server

		$server = $this->jobs->SelectServer(array('zipcode' => $input['zipcode'], 'state' => $input['state'], 'county' => $input['county'], 'jobId' => 'Null', 'process' => $input["type"], 'priority' => $input["priority"], 'client' => $input['client'], 'orderId' => $input['orderId']));

		//Find total cost (including service charge)
		$rate = $this->jobs->TotalRate(array('process' => $input["type"], 'rate' => $server['rate'], 'client' => $input['client'], 'server'=> $server['server']));

		return Response::json($rate);
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

		//retrieve data from order page
		$input = Session::get('input');

		if(!empty($input)) {

			$result = Session::get('result');
			$server = Session::get('server');
			$orders_id = Session::get('orders_id');
			$rate = Session::get('rate');
			$jobs = Session::get('jobs');

		}
		else {
			//retrieve data from input form
			$input = Input::all();

			Cache::put('input', Input::all(), 5);
			$orders_id = Input::get('orders_id');
			$servee_id = Input::get('servee_id');

			//Find order info
			$order = Orders::whereId($orders_id)->first();

			//get data is from new serve address from
			if (!empty($servee_id)) {

				//Retrieve previously attempted addresses
				$serveejobs = Jobs::whereserveeId(Input::get('servee_id'))->orderBy('created_at', 'asc')->get();

				View::share(['serveejobs' => $serveejobs]);
			}


			//if (!$this->jobs->fill($input)->isValid()) {
			//	return Redirect::back()->withInput()->withErrors($this->jobs->errors);
			//}

			//Retrieve previously entered defendants
			$jobs = Jobs::whereorderId(Input::get('orders_id'))
					->whereNotNull('street')->orderBy('id', 'asc')->get();

			//Verify Address

			$result = $this->jobs->addressVerification($input);

			if (!empty($result)) {

				//Select Server

				$server = $this->jobs->SelectServer(array('zipcode' => $result[0]['components']['zipcode'], 'state' => $result[0]['components']['state_abbreviation'], 'county' => $result[0]['metadata']['county_name'], 'jobId' => 'Null', 'process' => $input["type"], 'priority' => $input["priority"], 'client' => $order->client, 'orderId' => $order->id));

				//Find total cost (including service charge)
				$rate = $this->jobs->TotalRate(array('process' => $input["type"], 'rate' => $server['rate'], 'client' => $order->client));

			}
		}

		if(!empty($result)){

		Return View::make('jobs.verify', ['result' => $result])->with(['jobs' => $jobs])->with(['input' => $input])->with('rate', $rate)->with(['server' => $server])->with('orders_id', $orders_id);

			}
		else {

			$counties = ['' => 'Select County']+Counties::whereState($input['state'])->orderBy('county', 'asc')->lists('county', 'county');

			Return View::make('jobs.nonverify')->with(['jobs' => $jobs])->with(['input' => $input])->with('orders_id', $orders_id)->with(['counties' => $counties]);
		}

	}

	public function store(){

		$input = Input::all();

		//Determine # of servees at address
		$numServees = count($input["defendants"]);

		//Determine # of personal serves at address
		$numPersonal = 0;

		for ($i = 1; $i <= $numServees; $i++) {

			if (!empty($input["defendants"][$i]["personal"])) {

				$numPersonal++;

			}
		}

		//Find documents being served
		$docsServed = DocumentsServed::whereOrderId($input["orders_id"])->get();

		//Find number of pages of service package
		$numPgs = 0;

		foreach ($docsServed as $docServed) {

			$numPgs += Documents::whereDocument($docServed->document)->whereOrderId($input["orders_id"])->orderBy('created_at', 'desc')->pluck('pages');

		}

		//Select Server

		$server = $this->jobs->SelectServer(['zipcode' => $input["zipcode"], 'state' => $input["state"], 'county' => $input["county"], 'jobId' => 'Null', 'process' => $input["type"], 'priority' => $input["priority"], 'client' => $input["company"], 'orderId' => $input["orders_id"], 'numServees' => $numServees, 'numPersonal' => $numPersonal, 'numPgs' => $numPgs]);

		$firstServee = true;

		//loop through all servees for address
		foreach ($input["defendants"] as $defendant) {


			//create servee
			$serveeId = $this->Servee->createServee(['defendant' => $defendant["name"], 'company' => $input["company"], 'orders_id' => $input["orders_id"], 'status' => '1']);

			//Create job for servee
			$job = $this->jobs->createJob(['server' => $server["server"], 'defendant' => $defendant["name"], 'servee' => $defendant, 'notes' => $input["notes"], 'serveeId'=> $serveeId, 'client' => $input["company"], 'orders_id' => $input["orders_id"], 'service' => $input["type"], 'priority' => $input["priority"], 'status' => '0', 'street' => $input["street"], 'city' => $input["city"], 'state' => $input["state"], 'zip' => $input["zipcode"]]);

			//Load task into db
			$process = $this->tasks->CreateTasks(['judicial' => 'Judicial', 'jobs_id' => $job->id, 'vendor' => $server["server"], 'orders_id' => $input["orders_id"], 'county' => $input["county"], 'process' => $input["type"], 'priority' => $input["priority"], 'client' => $input["company"], 'state' => $input["state"]]);

			//Check for dependent jobs

			if (!$this->jobs->depProcess($process, $input["orders_id"])) {

				$job->status = 1;

			} else {

				$job->status = 2;

			}

			//Update job with process
			$job->process = $process;
			$job->save();

			//if first servee, set regular rate
			if($firstServee == true){

				$rate = $server["rate"];

			}
			//otherwise set rate for additional servee
			else{

				$rate = $server["addServeeRate"];
			}

			//Create Invoice
			$this->invoices->CreateInvoice(['jobId' => $job->id, 'process' => $input["type"], 'personal' => $defendant, 'personalRate' => $server["personalRate"], 'rate' => $rate, 'numPgs' => $numPgs, 'freePgs' => $server["freePgs"], 'pageRate' => $server["pageRate"]]);

			$firstServee = false;
		}
		

		Return Redirect::route('orders.show', ['id' => Input::get('orders_id')]);
		
		
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

		//Create data array
		$data = array();

		$first = true;

		foreach ($tasks as $task){

			$data[$task->id]["process"] = $task->process;
			$data[$task->id]["deadline"] = date("m/d/y", strtotime($task->deadline));

			if(is_null($task->completion) AND $first == true AND $task->status == '1'){

				$data[$task->id]["completion"] = true;

				$first = false;
			}
			elseif(is_null($task->completion) AND $first == true){

				$data[$task->id]["completion"] = "Job Is On Hold";

				$first = false;

			}
			elseif(is_null($task->completion) AND $first == false){

				$data[$task->id]["completion"] = " ";

			}
			else{

				$data[$task->id]["completion"] = date("m/d/y", strtotime($task->completion));
			}
			

		}

		//Find job data
		$jobs = Jobs::whereId($id)->OrderBy('id', 'asc')->first();


		
		if(Auth::user()->user_role=='Admin' OR Auth::user()->company_id==$jobs->vendor){

                return View::make('jobs.view')->with('jobs', $jobs)->with('tasks', $tasks)->with('token', $token)->with(['data' => $data]);
            }

		else{
			return 'Not Authorized to View Page!';
		}

	}

	public function actions(){

		//Get job info
		$jobs = Input::get('jobId');

		//Place job on hold
		if(Input::get('action')==0){


			//Update jobs
			foreach($jobs as $job){

				//Update status to 0
				$status = Jobs::whereId($job)->first();

				//Check if job is already on hold
				if($status->status == 0){

				}
				else {
					$status->status = 0;
					$status->save();

					//Put current task on hold
					$curTask = Tasks::wherejobId($job)
							->whereStatus(1)->first();
					$curTask->status = 0;
					$curTask->save();

				//Create array to notify vendor
				$data = array('job'=>$job, 'action'=>'0');

				//Create task to notify vendor
				$this->jobs->vendorNotification($data);

				}

				$orderId = $status->order_id;
			}

		}

		//Remove hold
		elseif(Input::get('action')==1){

			//Update jobs
			foreach($jobs as $job) {

				$jobData = Jobs::whereId($job)->first();

				$orderId = $jobData->order_id;

				//Check for dependent jobs
				if(!$this->jobs->depProcess($jobData->process, $jobData->order_id)) {

					//Update status to 1
					$status = Jobs::whereId($job)->first();
					$status->status = 1;
					$status->save();

					//Resume current task
					$curTask = Tasks::wherejobId($job)
						->whereNull('completion')->orderBy('sort_order', 'asc')->first();
					$curTask->status = 1;
					$curTask->save();

					//Update tasks
					$this->tasks->TaskForecast($curTask->id);

					//Create array to notify vendor
					$data = array('job' => $job, 'action' => '1');

					//Create task to notify vendor
					$this->jobs->vendorNotification($data);

				}

			}

		}

		//Cancel job
		elseif(Input::get('action')==2){

			foreach($jobs as $job){

				//Update status to 1
				$status = Jobs::whereId($job)->first();
				$status->status = 3;
				$status->save();

				//Determine if process service job
				if($job->service == "Process Service"){

				//Determine if any attempts have been made
				$attempts = Attempts::wherejobId($job)->get();

				//Find servee info
				$servee = Servee::wherejobId($job)->first();

				//If no attempts have been made, cancel current task
				if(empty($attempts) AND $servee->status == 0){

					$curTask = Tasks::wherejobId($job)
							          ->whereNull('completion')->orderBy('sort_order','asc')->first();
					$curTask->status = 0;
					$curTask->save();

				}

				//If attempts have been made but defendant has not been served, cancel attempts
				elseif(empty($served) AND $servee->status == 0){

					//Complete current task
					$curTask = Tasks::wherejobId($job)
									  ->whereNull('completion')->orderBy('sort_order','asc')->first();
					$curTask->completion = Carbon::now();
					$curTask->completed_by = Auth::user()->id;
					$curTask->save();

					//Update next task
					$nextTask = Tasks::wherejobId($job)
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
					$curTask = Tasks::wherejobId($job)
								  ->whereNull('completion')->orderBy('sort_order','asc')->first();
					$curTask->status = 0;
					$curTask->save();

				}

				//Create array to notify vendor
				$data = array('job'=>$job, 'action'=>'2');

				//Create task to notify vendor
				$this->jobs->vendorNotification($data);

				$orderId = $status->order_id;

			}

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
