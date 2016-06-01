<?php

class OrdersController extends \BaseController {
	protected $order;

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

		if(Auth::user()->user_role=='Admin'){
			$company = DB::table('company')->orderBy('name', 'asc')->lists('name', 'name');
		}
		else{
			$company = Auth::user()->company;
		}

        $documents = array(['Notice of Trustee Sale', 'Notice of Trustee Sale'],['AmendedSummons','Amended Summons'],['Summons','Summons'], ['AmendedComplaint','Amended Complaint'],['Complaint','Complaint'], ['NoticeOfPendency', 'Notice of Pendency'], ['LisPendens','Lis Pendens'], ['DeclarationOfMilitarySearch','Declaration of Military Search'], ['CaseHearingSchedule','Case Hearing Schedule']);

		if(Auth::user()->user_role=='Admin' OR Auth::user()->user_role=='Client'){
		Return View::make('orders.create', array('states' => $states, 'courts' => $courts, 'company' => $company, 'documents' => $documents));
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
		$orders_id = '9999';

		$court = DB::table('courts')->where('court', Input::get('court'))->first();

		/*
                $orders = new Orders;
                $orders->plaintiff = Input::get('plaintiff');
                $orders->reference = Input::get('reference');
                $orders->courtcase = Input::get('case');
                $orders->state = Input::get('caseSt');
                $orders->judicial = $input["judicial"];

                if(!empty($court)) {
                    $orders->county = $court->county;
                    $orders->court = $court->court;
                }

                $orders->user = Auth::user()->id;
                $orders->company = Input::get('company');
                $orders->save();
                $orders_id =  $orders->id;


                //If docs are uploaded, save them
                if(!empty($input["documents"])){

                    foreach ($input["documents"] as $document) {

                        if(!empty($document["file"])) {

                            //If valid file, save document
                            $this->Documents->saveDoc(['document' => $document, 'orders_id' => $orders_id, 'folder' => 'service_documents']);

                            $docCount++;
                        }

                        //Save service types
                        $this->DocumentsServed->saveDocType(['document' => $document, 'orderId' => $orders_id]);
                    }

                }
                dd($input);


                if($docCount=='0') {

                    //Set job to verify that docs are uploaded
                    $job = $this->jobs->createJob(['server' => '1', 'defendant' => '', 'client' => $input["company"], 'orders_id' => $orders_id, 'service' => 'Verify Documents', 'priority' => 'Routine', 'status' => '1', 'street' => '', 'city' => '', 'state' => '', 'zip' => '']);

                    //Load task into db
                    $process = $this->tasks->CreateTasks(['judicial' => $input["judicial"], 'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $orders_id, 'county' => 'Null', 'process' => 'Verify_Documents', 'priority' => 'Routine', 'client' => $input["company"], 'state' => 'Null']);

                    //Update job with process
                    $job->process = $process;
                    $job->save();
                }

                //create tasks/jobs for filing/recording, if requested

                if(!empty($input["filing"]) OR !empty($input["recording"])){

                for ($i = 1; $i < 3; $i++) {


                    if (!empty($input["filing"])) {

                        $service = 'filing';

                    }
                    elseif (!empty($input["filing"])) {

                        $service = "recording";

                    }

                        //Create job for filing
                        $job = $this->jobs->createJob(['server' => '1', 'defendant' => $court->court, 'client' => $input["company"], 'orders_id' => $orders_id, 'service' => $service, 'priority' => $input[$service], 'status' => '0', 'street' => '', 'city' => '', 'state' => $court->state, 'zip' => $court->zip]);

                        //Select Server

                        $server = $this->jobs->SelectServer(['zipcode' => $court->zip, 'state' => $input["caseState"], 'county' => $court->county, 'jobId' => $job->id, 'process' => $service, 'priority' => $input[$service], 'client' => $input["company"]]);

                        //Load task into db
                        $process = $this->tasks->CreateTasks(['judicial' => $input["judicial"], 'jobs_id' => $job->id, 'vendor' => $server, 'orders_id' => $orders_id, 'county' => $court->county, 'process' => $service, 'priority' => $input[$service], 'client' => $input["company"], 'state' => $court->state]);

                        //Check for dependent jobs

                        if (!$this->jobs->depProcess(['process' => $process, 'orderId' => $orders_id])) {

                            $job->status = 1;

                        } else {

                            $job->status = 0;

                        }

                        //Update job with process
                        $job->vendor = $server["server"];
                        $job->process = $process;
                        $job->save();

                        //Create Invoice
                        $this->invoices->CreateInvoice(['jobId' => $job->id, 'rate' => $server["rate"], 'process' => $service]);

                        $input["filing"] = '';
                    }

                }
        */
		//If defendant was added, validate data
		if (!empty($input["defendant"])) {
dd($input["defendant"]);
		//loop through all addresses
			foreach ($input["defendant"] as $servees) {
dd(count($servees["servee"]));
		//Determine # of servees at address
				$numServees = count($servees["servee"]);

		//Determine # of personal serves at address
				if(!empty($servees["servee"]["personal"])) {

					$numPersonal = count($servees["servee"]["personal"]);

				}

		//loop through all servees for address		
				foreach ($servees["servee"] as $servee) {
dd($servee);
				if(!empty($servee["personal"])){
					echo $servee["personal"]."<br>";
				}
		//create servee
				//$this->servee->creatServee(['defendant' => $servee, 'company' => $input["company"], 'orders_id' => $orders_id, 'status' => '1']);
				}
			}
		}
	}
/*
				//Select Server

				$server = $this->jobs->SelectServer(['zipcode' => $result[0]['components']['zipcode'], 'state' => $result[0]['components']['state_abbreviation'], 'county' => $result[0]['metadata']['county_name'], 'jobId' => 'Null', 'process' => $input["type"], 'priority' => $input["priority"], 'client' => Input::get('company'), 'orderId' => $orders_id]);

				//Find total cost (including service charge)
				$rate = $this->jobs->TotalRate(['state' => $result[0]['components']['state_abbreviation'], 'county' => $result[0]['metadata']['county_name'], 'server'=> $server['server'],'process' => $input["type"], 'rate' => $server['rate'], 'client' => Input::get('company')]);


		}
		$input["orders_id"] = $orders_id;

		Cache::put('orders_id', $orders_id, 30);
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
		$id = Cache::get('orders_id');
		}


		//Retrieve Order
		$order = Orders::whereId($id)->first();
		
		//Check if user is Admin or Client
		if(Auth::user()->company==$order->company OR Auth::user()->user_role=='Admin'){
		
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

			//Find verify task
			if(!empty($verify)) {
				$verifyTask = Tasks::wherejobId($verify->id)
						->whereNull('completion')->first();
			}


		//Find filing jobs
		$filing = Jobs::whereService('Filing')
				  ->whereorderId($id)->first();

			//Find filing tasks
			if(!empty($filing)) {
				$filingTasks = Tasks::wherejobId($filing->id)
						->whereStatus(1)->first();

				//Filing status
				$filingStatus = $this->orders->status($filing->id);

				//Find recording actions
				$filingActions = $this->orders->actions($filing->id);

				View::share(['filingTasks' => $filingTasks]);
				View::share(['filingActions' => $filingActions]);
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

				//Find recording actions
				$recordingActions = $this->orders->actions($recording->id);

				View::share(['recordingTasks' => $recordingTasks]);
				View::share(['recordingActions' => $recordingActions]);
				View::share('recordingStatus', $recordingStatus);
			}


		$defendants = array();

		//Find status for defendants
		if(!empty($viewservees)) {
			foreach ($viewservees as $viewservee) {

				//Find current job
				$defendants[$viewservee->id]["jobId"] = Jobs::whereserveeId($viewservee->id)
						->whereNull('completed')->pluck('id');


				//Find current task
				$defendants[$viewservee->id]["due"] = Tasks::wherejobId($defendants[$viewservee->id]["jobId"])
						->whereStatus(1)->pluck('deadline');


				//Find current status
				$defendants[$viewservee->id]["status"] = $this->orders->status($defendants[$viewservee->id]["jobId"]);


				//Find job actions
				$defendants[$viewservee->id]["actions"] = $this->orders->actions($defendants[$viewservee->id]["jobId"]);

			}
		}

        $token = Session::token();

		//Return Order View	
		return View::make('orders.show')->with('orders', $order)->with('servees', $viewservees)->with(['verify'=>$verifyTask])->with(['recording'=>$recording])->with(['filing'=>$filing])->with(['defendants'=>$defendants])->with('token', $token);
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
    public function edit($id){

        //Get order data
        $data = Orders::whereId($id)->first();

        $states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');

        $clients = Company::whereVC('client')->orderBy('name', 'asc')->lists('name', 'name');

        //Check if user is Admin or Client
        if(Auth::user()->company==$data->company OR Auth::user()->user_role=='Admin') {

            Return View::make('orders.edit')->with(['data' => $data])->with(['states' => $states])->with(['clients' => $clients]);
        }
    }
    public function update()
    {
        //Gather form data
        $input = Input::all();

        //Validate form data
        if ( ! $this->orders->fill($input)->isValid())
        {
            return Redirect::back()->withInput()->withErrors($this->orders->errors);
        }

        $court = DB::table('courts')->where('id', Input::get('court'))->pluck('court');

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
        $orders->court = $court;
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
