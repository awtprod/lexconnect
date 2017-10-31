<?php

class OrdersController extends \BaseController {
	protected $order;



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

		$states = ['' => 'Select State']+DB::table('states')->orderBy('name', 'asc')->lists('name', 'abbrev');

		$courts = DB::table('courts')->orderBy('court', 'asc')->lists('court', 'court');

		$users = User::whereCompanyId(Auth::user()->company_id)->orderBy('fname','asc')->get();

		$users = $users->lists('FullName','id');

		if(Auth::user()->user_role=='Admin'){
			$company = DB::table('company')->where('v_c', 'Client')->orderBy('name', 'asc')->lists('name', 'id');
		}
		else{
			$company = Auth::user()->company_id;
		}

        $documents = array(['Notice of Trustee Sale', 'Notice of Trustee Sale'],['AmendedSummons','Amended Summons'],['Summons','Summons'], ['AmendedComplaint','Amended Complaint'],['Complaint','Complaint'], ['NoticeOfPendency', 'Notice of Pendency'], ['LisPendens','Lis Pendens'], ['DeclarationOfMilitarySearch','Declaration of Military Search'], ['CaseHearingSchedule','Case Hearing Schedule']);

		if(Auth::user()->user_role=='Admin'){
		Return View::make('orders.admin', array('states' => $states, 'courts' => $courts, 'company' => $company, 'documents' => $documents));
		}
		elseif(Auth::user()->user_role=='Client'){
			Return View::make('orders.client', array('states' => $states, 'courts' => $courts, 'company' => $company, 'documents' => $documents, 'users'=>$users));
		}
		else{
		Return Redirect::to('login');
		}
		
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	 public function getCourts()
	 {
         $input = Input::get('option');
	$courts = DB::table('courts')->where('state', $input)->get();

         $numbers = ['' => 'Select Court']+DB::table('courts')->where('state', $input)->orderBy('id', 'asc')->lists('court','court');

         return Response::json($numbers);
	 }

	 public  function clear()
	 {
	 	Session::forget('input');
	 	Return Redirect::back();
	 }
	public function verify()
	{
		$input = Input::all();
	}
	public function store()
	{

		$input = Input::all();

		$docCount = 0;

		//delete after test
		//$orders_id = '9999';

		$court = DB::table('courts')->where('court', Input::get('court'))->first();


                $orders = new Orders;

				if(!empty($input["plaintiff"])) {
					$orders->plaintiff = Input::get('plaintiff');
				}
                $orders->reference = Input::get('reference');

				if(!empty($input["case"])) {

					$orders->courtcase = Input::get('case');
				}
				if(!empty($input["caseSt"])) {

					$orders->state = Input::get('caseSt');
				}
                if(!empty($court)) {
                    $orders->county = $court->county;
                    $orders->court = $court->court;
                }

                $orders->user = Input::get('requester');
                $orders->company = Input::get('company');
                $orders->save();
                $orders_id =  $orders->id;

                //If docs are uploaded, save them

		foreach ($input["documents"] as $document) {

					if ($document["file"]!= NULL) {

						//If valid file, save document
						$this->Documents->saveDoc(['document' => $document, 'orders_id' => $orders_id, 'folder' => 'service_documents', 'jobId' => '']);

						$docCount++;

						if (!empty($document["type"])) {

							//Save service types
							$this->DocumentsServed->saveDocType(['document' => $document, 'orderId' => $orders_id]);
						}
					}

				}

				//Find uploaded docs for order
				$allDocs = Documents::whereOrderId($orders_id)->get();

				//Find total # of pages uploaded docs
				$numPages = 0;

				if(!empty($allDocs)) {


					foreach ($allDocs as $allDoc) {

						$numPages += $allDoc->pages;
					}
				}

                    //Set job to verify that docs are uploaded
                    $job = $this->jobs->createJob(['server' => '1', 'defendant' => '', 'client' => $input["company"], 'orders_id' => $orders_id, 'service' => 'Verify Documents', 'priority' => 'Routine', 'status' => '1', 'street' => '', 'city' => '', 'state' => '', 'county' =>'','zip' => '']);

                    //Load task into db
                    $process = $this->tasks->CreateTasks(['judicial' => 'judicial', 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $orders_id, 'county' => 'Null', 'process' => 'Verify_Documents', 'priority' => 'Routine', 'client' => $input["company"], 'state' => 'Null']);

                    //Update job with process
                    $job->process = $process;
                    $job->save();

		//create skip trace tasks
		if (!empty($input["skip_defendant"])) {


			foreach ($input["skip_defendant"] as $defendant) {

				//Create job for skip trace
				$job = $this->jobs->createJob(['server' => '1', 'defendant' => $defendant, 'servee' => '', 'notes' => '', 'serveeId' => $input["run_notes"], 'client' => $input["company"], 'orders_id' => $orders_id, 'service' => 'skip trace', 'priority' => 'routine', 'status' => '1', 'street' => '', 'city' => '', 'state' => '', 'county' => '', 'zip' => '']);

				//Load task into db
				$process = $this->tasks->CreateTasks(['judicial' => 'judicial', 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $orders_id, 'county' => '', 'process' => 'skip trace', 'priority' => 'Routine', 'client' => $input["company"], 'state' => '']);

				//Check for dependent jobs

				if (!$this->jobs->depProcess($process, $orders_id)) {

					$job->status = 1;

				} else {

					$job->status = 2;

				}

				//Update job with process
				$job->vendor = 1;
				$job->process = $process;
				$job->notes = $input["skip_notes"];
				$job->save();

				//Create Invoice
				$this->invoices->CreateInvoice(['jobId' => $job->id, 'process' => 'skip trace', 'personal' => '', 'personalRate' => '95', 'rate' => '95', 'numPgs' => $numPages, 'freePgs' => '0', 'pageRate' => '0']);
			}
			//Uploaded docs
			if($input["skip_docs"][0]["file"]!=NULL) {


				foreach ($input["skip_docs"] as $document) {

					$this->Documents->saveDoc(['document' => $document, 'orders_id' => $orders_id, 'folder' => 'service_documents', 'jobId'=>'']);
				}
			}

		}

				//create court run tasks
				if (!empty($input["run_notes"])) {


					//Create job for filing
					$job = $this->jobs->createJob(['server' => '1', 'defendant' => '', 'servee' => '', 'notes' => '', 'serveeId'=> $input["run_notes"], 'client' => $input["company"], 'orders_id' => $orders_id, 'service' => 'court run', 'priority' => $input["court run"], 'status' => '0', 'street' => '', 'city' => '', 'state' => '', 'county' => '', 'zip' => '']);

					//Select Server

					//$server = $this->jobs->SelectServer(['zipcode' => $court->zip, 'state' => $input["caseState"], 'county' => $court->county, 'jobId' => $job->id, 'process' => 'file', 'priority' => $input["court run"], 'client' => $input["company"]]);

					//Load task into db
					$process = $this->tasks->CreateTasks(['judicial' => 'judicial', 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $orders_id, 'county' => '', 'process' => 'file', 'priority' => $input["court run"], 'client' => $input["company"], 'state' => '']);

					//Check for dependent jobs

					if (!$this->jobs->depProcess($process, $orders_id)) {

						$job->status = 1;

					} else {

						$job->status = 2;

					}

					//Update job with process
					$job->vendor = 1;
					$job->process = $process;
					$job->save();

					//Create Invoice
					$this->invoices->CreateInvoice(['jobId' => $job->id, 'process' => 'run', 'personal' => '', 'personalRate' => '95', 'rate' =>'95', 'numPgs' => $numPages, 'freePgs' => '0', 'pageRate' => '0']);

					//Uploaded docs
					if($input["run_docs"]["file"]!=NULL) {
						dd($input["run_docs"]["file"]);

						foreach ($input["run_docs"]["file"] as $document) {

							$this->Documents->saveDoc(['document' => $document, 'orders_id' => $orders_id, 'folder' => 'service_documents', 'jobId'=>$job->id]);
						}
					}

					$input["filing"] = '';
					
				}
					
				//create tasks/jobs for filing/recording, if requested

                if(!empty($input["filing"]) OR !empty($input["recording"])){

                for ($i = 1; $i < 2; $i++) {


                    if (!empty($input["filing"])) {

                        $service = 'filing';

                    }
                    elseif (!empty($input["recording"])) {

                        $service = "recording";

                    }

                        //Create job for filing
						$job = $this->jobs->createJob(['server' => '1', 'defendant' => '', 'servee' => '', 'notes' => '', 'serveeId'=> '', 'client' => $input["company"], 'orders_id' => $orders_id, 'service' => $service, 'priority' => $input[$service], 'status' => '0', 'street' => '', 'city' => '', 'state' => '', 'county' => '', 'zip' => '']);

						if(!empty($input["caseSt"])){
							$caseSt = $input["caseSt"];
						}
						else{
							$caseSt = '';
						}
                        //Load task into db
						$process = $this->tasks->CreateTasks(['judicial' => 'judicial', 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $orders_id, 'county' => '', 'process' => $service, 'priority' => $input[$service], 'client' => $input["company"], 'state' => $caseSt]);

                        //Check for dependent jobs

                        if (!$this->jobs->depProcess($process, $orders_id)) {

                            $job->status = 1;

                        } else {

                            $job->status = 2;

                        }

                        //Update job with process
                        $job->vendor = 1;
                        $job->process = $process;
                        $job->save();

						//Create Invoice
						$this->invoices->CreateInvoice(['jobId' => $job->id, 'process' => $service, 'personal' => '', 'personalRate' => '0', 'rate' => '0', 'numPgs' => $numPages, 'freePgs' => '0', 'pageRate' => '0']);

                        $input["filing"] = '';
                    }

                }

		//If defendant was added, validate data
		if ($input["defendant"][1]!="") {

			//loop through all addresses
			foreach ($input["defendant"] as $servees) {


				//Determine # of servees at address
				$numServees = count($servees["servee"]);

				if($numServees > 1){

					$add_servee = true;
				}
				else{
					$add_servee = false;
				}

				//Determine # of personal serves at address
				$numPersonal = 0;

				for ($i = 1; $i <= $numServees; $i++) {

					if (!empty($servees["servee"][$i]["personal"])) {

						$numPersonal++;

					}
				}

				//Select Server

				//$server = $this->jobs->SelectServer(['zipcode' => $servees["zipcode"], 'state' => $servees["state"], 'county' => $servees["county"], 'jobId' => 'Null', 'process' => $servees["type"], 'priority' => $servees["priority"], 'client' => $input["company"], 'orderId' => $orders_id, 'add_servee' => $add_servee, 'numPersonal' => $numPersonal, 'numPgs' => $numPages]);

				$firstServee = true;

				//loop through all servees for address
				foreach ($servees["servee"] as $servee) {


				//create servee
				$serveeId = $this->Servee->createServee(['defendant' => $servee["name"], 'company' => $input["company"], 'orders_id' => $orders_id, 'status' => '1']);

				//Create job for servee
				$job = $this->jobs->createJob(['server' => '1', 'defendant' => $servee["name"], 'servee' => $servee, 'notes' => $servees["notes"], 'serveeId'=> $serveeId, 'client' => $input["company"], 'orders_id' => $orders_id, 'service' => $servees["type"], 'priority' => $servees["priority"], 'status' => '0', 'street' => $servees["street"], 'city' => $servees["city"], 'state' => $servees["state"], 'county' => $servees["county"], 'zip' => $servees["zipcode"]]);

				//Load task into db
                $process = $this->tasks->CreateTasks(['judicial' => 'judicial', 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $orders_id, 'county' => $court->county, 'process' => $servees["type"], 'priority' => $servees["priority"], 'client' => $input["company"], 'state' => $input["caseSt"]]);

					//Check for dependent jobs

					if (!$this->jobs->depProcess($process, $orders_id)) {

						$job->status = 1;

					} else {

						$job->status = 2;

					}

				//if first servee, set regular rate
				if($firstServee == true){

						$job->add_servee = 0;
						$rate = '95';

				}
				//otherwise set rate for additional servee
				else{
						$job->add_servee = 1;
						$rate = '95';
				}

				//Update job with process
                $job->process = $process;
                $job->save();



				//Create Invoice
				$this->invoices->CreateInvoice(['jobId' => $job->id, 'process' => $servees["type"], 'personal' => $servee, 'personalRate' => '95', 'rate' => $rate, 'numPgs' => $numPages, 'freePgs' => '0', 'pageRate' => '0']);

				$firstServee = false;
				}
			}
		}

		$input["orders_id"] = $orders_id;

		Return Redirect::route('orders.show', ['id' => $orders_id]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

		$actions = array('0' => 'Hold', '1' => 'Resume', '2' => 'Cancel');

		//Retrieve Order
		$order = Orders::whereId($id)->first();
		
		//Check if user is Admin or Client
		if(Auth::user()->company_id==$order->company OR Auth::user()->user_role=='Admin'){
		
		//If Admin, find all defendants
		if(Auth::user()->user_role=='Admin'){
		$viewservees = Servee::whereorderId($id)->orderBy('id', 'asc')->get();
		}
		else{
		//find all defendants in this case
		$viewservees = Servee::whereorderId($id)
					   ->where('client', Auth::user()->company)->orderBy('id', 'asc')->get();
		}


		//Find if verify docs process exists
		$verify = Jobs::whereService('Verify Documents')
				  ->whereorderId($id)->first();

		$verifyTask = "";

			//Find verify task
			if(!empty($verify)) {
				$verifyTask = Tasks::wherejobId($verify->id)
						->whereNull('completion')->first();
			}


		//Find filing jobs
		$filing = Jobs::whereService('Filing')
				  ->whereorderId($id)->first();

		$filingTasks = "";

			//Find filing tasks
			if(!empty($filing)) {
				$filingTasks = Tasks::wherejobId($filing->id)
						->whereStatus(1)->first();

				//Filing status
				$filingStatus = $this->orders->status($filing->id);


				View::share(['filingTasks' => $filingTasks]);
				View::share('filingStatus', $filingStatus);
			}


		//Find recording jobs
		$recording = Jobs::whereService('Recording')
				     ->whereorderId($id)->first();


			//Find recording tasks
			if(!empty($recording)) {
				$recordingTasks = Tasks::wherejobId($recording->id)
						->whereStatus(1)->first();

				//Recording status
				$recordingStatus = $this->orders->status($recording->id);


				View::share(['recordingTasks' => $recordingTasks]);
				View::share('recordingStatus', $recordingStatus);
			}


		$defendants = array();
		$not_found = array();
		$served = array();

		//Find status for defendants
		if(!empty($viewservees)) {
			foreach ($viewservees as $viewservee) {

				//Determine if defendant has been served
				if($viewservee->status == '1'){

					$job = Jobs::whereserveeId($viewservee->id)->orderBy('created_at','desc')->first();

					$served[$viewservee->id]["serve"] = Serve::whereServeeId($viewservee->id)->first();
					$served[$viewservee->id]["proof"] = Documents::whereJobId($job->id)->whereOrderId($job->order_id)->whereDocument('Executed_Proof')->orderBy('created_at','desc')->pluck('id');

				}
				elseif ($viewservee->status == '3'){
					$job = Jobs::whereserveeId($viewservee->id)->orderBy('created_at','desc')->first();

					$not_found[$viewservee->id]["due_diligence"] = Documents::whereJobId($job->id)->whereOrderId($job->order_id)->whereDocument('Due_Diligence')->orderBy('created_at','desc')->pluck('id');

				}
				else {

					//Find current job
					$defendants[$viewservee->id]["job"] = Jobs::whereserveeId($viewservee->id)
						->whereNull('completed')->first();


					//Find current task
					$defendants[$viewservee->id]["due"] = Tasks::wherejobId($defendants[$viewservee->id]["job"]->id)
						->whereStatus(1)->pluck('deadline');


					//Find current status
					$defendants[$viewservee->id]["status"] = $this->orders->status($defendants[$viewservee->id]["job"]->id);
				}

			}
		}
		$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'abbrev');

        $token = Session::token();

		//Return Order View	
		return View::make('orders.show')->with('orders', $order)->with(['not_found'=>$not_found])->with(['served'=>$served])->with('servees', $viewservees)->with(['verify'=>$verifyTask])->with(['recording'=>$recording])->with(['filing'=>$filing])->with(['defendants'=>$defendants])->with('token', $token)->with(['actions'=>$actions])->with(['states'=>$states]);
		}
		else{
		Return Redirect::to('login');
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
					    ->where('process', '=', 1)
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
					  ->where('process', '=' ,1)->orderBy('completion', 'asc')->first();
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
    public function edit(){

        //Get order data
        $data = Orders::whereId(Input::get('orderId'))->first();

		$users = User::whereCompany($data->company)->orderBy('fname','asc')->get();

		$users = $users->lists('FullName','id');

        $states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'abbrev');

        $clients = Company::whereVC('client')->orderBy('name', 'asc')->lists('name', 'name');

        //Check if user is Admin or Client
        if(Auth::user()->company==$data->company OR Auth::user()->user_role=='Admin') {

            Return View::make('orders.edit')->with(['data' => $data])->with(['users'=>$users])->with(['states' => $states])->with(['clients' => $clients]);
        }
    }
    public function update()
    {
        //Gather form data
        $input = Input::all();


        //Update Order Data
        $orders = Orders::whereId(Input::get('orderId'))->first();

        if(!empty($input["plaintiff"])) {
            $orders->plaintiff = Input::get('plaintiff');
        }
        if(!empty($input["defendant"])) {
            $orders->defendant = Input::get('defendant');
        }
        if(!empty($input["reference"])) {
            $orders->reference = Input::get('reference');
        }
        if(!empty($input["case"])) {
            $orders->courtcase = Input::get('case');
        }
        $orders->state = Input::get('state');
		if(!empty($input["court"])) {
			$orders->court = Input::get('court');
		}
        if(!empty($input["company"])) {
            $orders->company = Input::get('company');
        }
        $orders->save();

        Return Redirect::route('orders.show', Input::get('orderId'));

    }
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
	$showorders = $this->orders->whereId(Input::get('orders_id'))->first();
	$showorders->status = 0;
	$showorders->save();
	
	//Remove hold from all jobs
	$orderjobs = DB::table('jobs')->where('order_id', Input::get('id'))
				      ->where('completed', NULL)->get();
	
	foreach($orderjobs as $job){
	
	$this->jobs->RemoveHold($job);
		
	}
	
	return Redirect::back();	
	
	}
	if($status == 1){
	//Place Order on hold
	
	$showorders = Orders::whereId(Input::get('orders_id'))->first();
	$showorders->status = 1;
	$showorders->save();
	
	//Place all jobs on hold
	$orderjobs = DB::table('jobs')->where('order_id', Input::get('orders_id'))
				      ->where('completed', NULL)->get();
	
	foreach($orderjobs as $job){
	
	$this->jobs->HoldJob($job);
		
	}
	
	return Redirect::back();
	
	}
	
	if($status == 2){
	//Cancel Order
	
	$showorders = $this->orders->whereId(Input::get('orders_id'))->first();
	$showorders->status = 2;
	$showorders->completed = Carbon::now();
	$showorders->save();
	
	//Cancel all jobs
	$orderjobs = DB::table('jobs')->where('order_id', Input::get('id'))
				      ->where('completed', NULL)->get();
	
	foreach($orderjobs as $job){
	
	$this->jobs->CancelJob($job);
	$this->invoices->CreateInvoice($job);
		
	}	
	
	return Redirect::back();
		
	}
	
	}

}
