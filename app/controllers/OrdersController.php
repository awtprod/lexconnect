<?php

class OrdersController extends \BaseController {
	protected $order;

	public function __construct (User $user, Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, Processes $processes, Steps $steps, Template $template, Counties $counties)
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

		$court = DB::table('courts')->where('court', Input::get('court'))->first();

		//Check to see if at least one service document type has been selected

		if (empty($input["documentServed"])) {
			return Redirect::back()->withInput()->withErrors($this->orders->errors);
		}

		//Check if judicial or non-judicial
		if(empty($input["documentServed"]["Notice_of_Trustee_Sale"])) {

			if (!$this->orders->fill($input)->isValid()) {
				return Redirect::back()->withInput()->withErrors($this->orders->errors);
			}
		}

		$orders = new Orders;
		$orders->plaintiff = Input::get('plaintiff');
		$orders->defendant = Input::get('defendant');
		$orders->reference = Input::get('reference');
		$orders->courtcase = Input::get('case');
		$orders->state = Input::get('caseState');

		if(empty($input["documentServed"]["Notice_of_Trustee_Sale"])){

		$orders->judicial = "Judicial";
		$judicial = "Judicial";

		}
		else{

		$orders->judicial = "Non-Judicial";
		$judicial = "Non-Judicial";
		}

		if(!empty($court)) {
			$orders->county = $court->county;
			$orders->court = $court->court;
		}

		$orders->user = Auth::user()->id;
		$orders->company = Input::get('company');
		$orders->save();
		$orders_id =  $orders->id;


		//Save service types

        $docArray = array('input' => $input, 'orderId' => $orders_id);
        $this->DocumentsServed->insertDocs($docArray);



		//If docs are uploaded, validate them
		if(!empty($input["service_documents"])){

			if (!$this->orders->fill($input)->validFile()) {
				return Redirect::back()->withInput()->withErrors($this->orders->errors);
			}

			//If valid file, move to service documents dir
			$destinationPath = public_path().'/service_documents';
			$file = str_random(6);
			$filename =  $orders_id.$file . '_'. 'serviceDocs.pdf';
			//$filepath = public_path('service_documents/' . $filename);
			Input::file('service_documents')->move($destinationPath, $filename);


			$document = new Documents;
			$document->document = 'Service Documents';
			$document->order_id = $orders_id;
			$document->filename = $filename;
			$document->filepath = 'service_documents';
			$document->save();
		}

		//Set job to verify that docs are uploaded

		$job = new Jobs;
		$job->vendor = 1;
		$job->client = Input::get('company');
		$job->order_id = $orders_id;
		$job->service = 'Verify Documents';
		$job->priority = 'Routine';
		$job->status = 1;
		$job->save();

		//Create task array
		$sendTask = array('judicial'=>$judicial,'jobs_id' => $job->id, 'vendor' => '1', 'orders_id' => $orders_id, 'county' => $court->county, 'process' => 'Verify_Documents', 'priority'=>'Routine', 'client' => Input::get('company'), 'state' => Input::get('state'));

		//Load task into db
		$process = $this->tasks->CreateTasks($sendTask);

		//Update job with process
		$job->process = $process;
		$job->save();


		if (!empty($input["filing"]) OR !empty($input["recording"])) {


			if(!empty($input["filing"])) {


				//Create job for filing

				$job = new Jobs;
				$job->defendant = $court->court;
				$job->client = Input::get('company');
				$job->order_id = $orders_id;
				$job->service = 'Filing';
				$job->priority = $input["filing"];
				$job->state = $court->state;
				$job->zipcode = $court->zip;
				$job->save();

				//Select Server
				$serverData = array('zipcode' => $court->zip, 'state' => Input::get('caseState'), 'county' => $court->county, 'jobId' => $job->id, 'process' => 'filing', 'priority' => $input["filing"], 'client' => Input::get('company'));
				$server = $this->jobs->SelectServer($serverData);

				//Create task array
				$sendTask = array('judicial'=>$judicial,'jobs_id' => $job->id, 'vendor' => $server, 'orders_id' => $orders_id, 'county' => $court->county, 'process' => 'Filing', 'priority' => $input["filing"], 'client' => Input::get('company'), 'state' => Input::get('state'));

				//Load task into db
				$process = $this->tasks->CreateTasks($sendTask);

				//Check for dependent jobs
				$depData = array('process' => $process, 'orderId'=>$orders_id);

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
			}

			if(!empty($input["recording"])) {


				//Create job for recording

				$job = new Jobs;
				$job->defendant = $court->court;
				$job->client = Input::get('company');
				$job->order_id = $orders_id;
				$job->service = 'Recording';
				$job->priority = $input["recording"];
				$job->state = $court->state;
				$job->zipcode = $court->zip;
				$job->save();

				//Select Server
				$serverData = array('zipcode' => $court->zip, 'state' => Input::get('caseState'), 'county' => $court->county, 'jobId' => $job->id, 'process' => 'recording','priority' => $input["recording"], 'client' => Input::get('company'));
				$server = $this->jobs->SelectServer($serverData);

				//Create task array
				$sendTask = array('judicial'=>$judicial,'jobs_id' => $job->id, 'vendor' => $server, 'orders_id' => $orders_id, 'county' => $court->county, 'process' => 'Recording', 'priority' => $input["recording"], 'client' => Input::get('company'), 'state' => Input::get('state'));

				//Load task into db
				$process = $this->tasks->CreateTasks($sendTask);

				//Check for dependent jobs
				$depData = array('process' => $process, 'orderId'=>$orders_id);

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
			}
		}

		//Determine if defendant was added
		$service = Input::get('service');

		//If defendant was added, validate data
		if(!empty($service["defendant"])){

			if (!$this->orders->fill($service)->isValidDefendant()) {
				return Redirect::back()->withInput()->withErrors($this->orders->errors);
			}

		//Verfiy Address
			$result = $this->jobs->addressVerification($service);

			//Retrieve previously entered defendants
			$jobs = DB::table('jobs')
					->where('order_id', $orders_id)
					->whereNotNull('street')->orderBy('id', 'asc')->get();

			$input["orders_id"] = $orders_id;

			if(!empty($result)){

				Return View::make('jobs.verify', ['result' => $result])->with(['jobs' => $jobs])->with(['input' => $input]);

			}
			else {
				Return View::make('jobs.verify')->with(['jobs' => $jobs])->with(['input' => $input]);
			}
		}

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



        $token = Session::token();

		//Return Order View	
		return View::make('orders.show')->with('orders', $showorders)->with('servees', $viewservees)->with('token', $token);
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
