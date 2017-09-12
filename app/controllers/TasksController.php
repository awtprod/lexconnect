<?php
use Carbon\Carbon;
class TasksController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		//If vendor, find all tasks due
		if(Auth::user()->user_role=='Vendor'){

			$jobTasks = Tasks::whereStatus(1)
					        ->whereGroup(Auth::user()->company_id)->orderBy('deadline', 'asc')->get();
		}
		elseif(Auth::user()->user_role == 'Admin'){

			$jobTasks = Tasks::whereStatus(1)->orderBy('deadline', 'asc')->get();
		}
		else{

			Return "Not Authorized To View!";
		}

		$job = array();

		//Find Jobs associated with tasks
		foreach($jobTasks as $jobTask){

			$job[$jobTask->id] = Jobs::whereId($jobTask->job_id)->first();
		}

		Return View::make('tasks.index')->with(['jobTasks'=>$jobTasks])->with(['job'=>$job]);

	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function complete()
    {
        //Retrieve task id from URL
       $tasksId = Input::get('id');

		$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'abbrev');


		//Retrieve task data from db
        $CurrentTask = Tasks::whereId($tasksId)->first();

        //Get zip code of serve address
        $job = DB::table('jobs')->where('id', $CurrentTask->job_id)->first();

		//Find servers
		if(Auth::user()->user_role == 'Admin'){
			$servers = User::orderBy('fname')->lists('fname', 'id');

		}
		else {

			$servers = User::whereCompanyId($job->vendor)->orderBy('fname')->lists('fname', 'id');
		}

		//Find latest proof
		$proof = Documents::whereJobId($job->id)
							->where('document', 'Unexecuted_Proof')->orderBy('created_at', 'desc')->first();

		//Get order data
        $order = Orders::whereId($CurrentTask->order_id)->first();

		//Find documents to be served
		$docs_served = DocumentsServed::whereOrderId($order->id)->get();

		//Get serve information
		$serve = Serve::whereJobId($job->id)->first();

		//Get invoice information
		$invoice = Invoices::whereJobId($job->id)->first();

		//Get servee information
		$servee = Servee::whereOrderId($order->id)->first();

		//Find number of pages served
		$pages = 0;

		foreach ($docs_served as $doc_served){
			$pages += Documents::whereDocument($doc_served->document)->orderBy('created_at', 'desc')->pluck('pages');
		}

		if($servee->summons != 'NULL'){
			$pages += Documents::whereId($servee->summons)->orderBy('created_at', 'desc')->first();
		}

		//Calculate page rate for service
		$total_pages_billed = $pages - $invoice->free_pgs;

		$pg_rate = $total_pages_billed*$invoice->pg_rate;

        //Find current task due
        /*$LatestTask = Tasks::OrderBy('sort_order', 'asc')
            ->where('job_id', $CurrentTask->job_id)
            ->where('completion', NULL)->first();


        //See if requested task is current task due, if not throw an error
        if ($LatestTask->id != $tasksId) {

            Return "Not Authorized To View!";

        //Check to see if user is authorized to complete task
        } elseif (Auth::user()->user_role=='Admin' OR Auth::user()->company_id == $CurrentTask->group) {

*/
			//see if there is a popup window for task
			if(!empty($CurrentTask->window)){


				Return Response::json(array('body' => View::make($CurrentTask->window)->with('taskId', $tasksId)->with(['docs_served'=>$docs_served])->with(['job'=>$job])->with(['order'=>$order])->with(['servers'=>$servers])->with(['states'=>$states])->with(['serve'=>$serve])->with(['invoice'=>$invoice])->with('pg_rate', $pg_rate)->with('pages', $pages)->with('proof', $proof)->render(), 'title' => 'test'));
			}
            //If vendor accepts serve, complete step and proceed with serve
            else{

            $this->tasks->TaskComplete($tasksId);

				Return Response::json('no');

            }


        /*}
        else{

            Return "Not Authorized To View!";
        }*/
    }

	public function accept(){

		$task = Tasks::whereId(Input::get('taskId'))->first();

		$job = Jobs::whereId($task->job_id)->first();

		//if server accepted job, mark task as complete

		if(Input::get('accept') == 'Accept'){

			$this->tasks->TaskComplete(Input::get('taskId'));


		}
		else{
			return Response::json($job);

			//Find new server
			$serverData = array('zipcode' => $job->zipcode, 'jobId' => $job->id);
			$server = $this->jobs->SelectServer($serverData);

			//Reassign server
			$newServerData = array('vendor' => $server, 'orderId' => $job->order_id, 'jobId' => $job->id);
			$newServer = $this->jobs->ReAssignServer($newServerData);

		}
	}

	public function verify(){

	}

	public function attempt(){

		//if defendant served, load serve screen
		if(Input::get('served') == 'true'){

			Return View::make('serve.create')->with('taskId', Input::get('taskId'));
		}
		//if defendant was not served, load attempt screen
		else{

			$job = Tasks::whereId(Input::get('taskId'))->pluck('job_id');

			Return View::make('attempt.create')->with('taskId', Input::get('taskId'))->with('job', $job);

		}
	}

	public function proof(){

		if(File::exists(app_path('/views/proofs/'.Input::get('jobId').'.txt'))) {

			return Response::json(File::get(app_path('/views/proofs/'.Input::get('jobId').'.txt')));
		}

		//Get job info
		$job = Jobs::whereId(Input::get('jobId'))->first();

		//Get task info
		$taskId = Tasks::whereJobId(Input::get('jobId'))->first();

		//Get order info
		$order = Orders::whereId($job->order_id)->first();

		//Find server information
		$server = User::whereId(Auth::user()->id)->first();

		//Find server firm
		$serverFirm = Company::whereId($server->company_id)->first();

		//Find court information
		$court = DB::table('courts')->whereCourt($order->court)->first();

		//Find what documents were served
		$documents = DocumentsServed::whereorderId($order->id)->get();

		$numItems = count($documents);
		$docsServed = '';
		$i = 1;

		foreach($documents as $key=>$value) {
			$docsServed .= $value.', ';
			if(++$i === $numItems) {
				$docsServed .= 'and '.$value;
			}
		}

		//Determine if Serve or Non-Serve
		$serve = DB::table('serve')->where('job_id', Input::get('jobId'))->first();

		//If Defendant was served, Generate Proof of Service
		if(!empty($serve)){

			$data = array();

			$data['date'] = date('jS \d\a\y \of F Y', strtotime($serve->date));
			$data['time'] = date('h:i A', strtotime($serve->time));
			$data['served'] = $serve->served_upon;
			$data['relationship'] = $serve->relationship;


			View::addLocation(app_path('/views/states/'));

			Return View::make($order->state."_proof",['job'=>$job,'data'=>$data,'order'=>$order,'server'=>$server,'court'=>$court,'docsServed'=>$docsServed,'serve'=>$serve]);
		}

		//If defendant was NOT served
		else{
			$attempts = DB::table('attempts')->OrderBy('date', 'asc')->where('job', Input::get('jobId'))->get();
			$data = array();
			foreach( $attempts as $attempt){

				$data[$attempt->job]['date'] = date("m/d/y", strtotime($attempt->date));
				$data[$attempt->job]['time'] = date('h:i A', strtotime($attempt->time));
				$data[$attempt->job]['description'] = $attempt->description;
			}

			View::addLocation(app_path('/views/states/'));

			Return View::make($order->state.'_non-serve',['job'=>$job,'data'=>$data,'order'=>$order,'server'=>$server,'court'=>$court,'docsServed'=>$docsServed]);
		}
	}
	public function generate_proof(){
		$input = Input::all();

		if($input["button"]=="save"){
			$this->save_proof($input);
		}
		else{
			$this->save_proof($input);
			$pdf = PDF::loadHTML($input["template_body"]);
			return $pdf->download();

		}

	}

	public function save_proof($input){

		File::put(app_path('/views/proofs/'.$input["id"].'.txt'), $input["template_body"]);

	}

	public function upload_proof(){

		$input = Input::all();

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$type = finfo_file($finfo, $input["executed_proof"]);


		if(!empty($input["executed_proof"]) AND ($type == "application/pdf")) {

			return "file uploaded";
			//Find job data
			$job = Jobs::whereId(Input::get('jobId'))->first();

			//If valid file, move to service documents dir
			$destinationPath = storage_path() . '/proofs';
			$file = str_random(6);
			$filename = $file . '_' . $job->id . '_' . 'proof.pdf';

			Input::file('executed_proof')->move($destinationPath, $filename);
		}
		else{
			return "file not uploaded";
		}

		//Update Table
		$document = new Documents;
		$document->document = 'Executed_Proof';
		$document->job_id = Input::get('jobId');
		$document->order_id = $job->order_id;
		$document->filename = $filename;
		$document->filepath = 'proofs';
		$document->save();

		//Complete task
		$this->tasks->TaskComplete(Input::get('taskId'));


	}
	public function mailing(){

		if(File::exists(app_path('/views/mailing/'.Input::get('jobId').'.txt'))) {

			return Response::json(File::get(app_path('/views/mailing/'.Input::get('jobId').'.txt')));
		}

		//Get job info
		$job = Jobs::whereId(Input::get('jobId'))->first();

		//Get task info
		$taskId = Tasks::whereJobId(Input::get('jobId'))->first();

		//Get order info
		$order = Orders::whereId($job->order_id)->first();

		//Find server information
		$server = User::whereId(Auth::user()->id)->first();

		//Find server firm
		$serverFirm = Company::whereId($server->company_id)->first();

		//Find court information
		$court = DB::table('courts')->whereCourt($order->court)->first();

		//Find what documents were served
		$documents = DocumentsServed::whereorderId($order->id)->get();

		$numItems = count($documents);
		$docsServed = '';
		$i = 1;

		foreach($documents as $key=>$value) {
			$docsServed .= $value.', ';
			if(++$i === $numItems) {
				$docsServed .= 'and '.$value;
			}
		}

		//Determine if Serve or Non-Serve
		$serve = DB::table('serve')->where('job_id', Input::get('jobId'))->first();



			$date = date('jS \d\a\y \of F Y', strtotime(Carbon::now()));


			View::addLocation(app_path('/views/states/'));

			Return View::make($order->state."_mailing",['job'=>$job,'date'=>$date,'order'=>$order,'server'=>$server,'court'=>$court,'docsServed'=>$docsServed,'serve'=>$serve]);

	}
	public function generate_mailing(){
		$input = Input::all();

		if($input["button"]=="save"){
			$this->save_mailing($input);
		}
		else{
			$this->save_mailing($input);
			$pdf = PDF::loadHTML($input["template_body"]);
			return $pdf->download();

		}

	}

	public function save_mailing($input){

		File::put(app_path('/views/mailing/'.$input["id"].'.txt'), $input["template_body"]);

	}

	public function upload_mailing(){

		$input = Input::all();

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$type = finfo_file($finfo, $input["executed_mailing"]);


		if(!empty($input["executed_mailing"]) AND ($type == "application/pdf")) {

			return "file uploaded";
			//Find job data
			$job = Jobs::whereId(Input::get('jobId'))->first();

			//If valid file, move to service documents dir
			$destinationPath = storage_path() . '/proofs';
			$file = str_random(6);
			$filename = $file . '_' . $job->id . '_' . 'mailing.pdf';

			Input::file('executed_mailing')->move($destinationPath, $filename);
		}
		else{
			return "file not uploaded";
		}

                //Update Table
                $document = new Documents;
                $document->document = 'Executed_Mailing';
                $document->job_id = Input::get('jobId');
                $document->order_id = $job->order_id;
                $document->filename = $filename;
                $document->filepath = 'proofs';
                $document->save();

                //Complete task
                $this->tasks->TaskComplete(Input::get('taskId'));


	}

	public function invoice(){
		
		$input = Input::all();

		//Load invoice information
		$invoice = Invoices::whereJobId($input["jobId"])->first();

		//Load job informaiton
		$job = Jobs::whereId($input["jobId"])->first();

		//Load client information
		$client = Company::whereId($job->client)->first();

		//Load serve information
		$serve = Serve::whereJobId($job->id)->first();

		$date = date('m/j/y', strtotime(Carbon::now()));


		//Create Invoice
		$file = $input["jobId"].str_random(6);
		$filename =  $file . '_'. 'invoice.pdf';
		$filepath = public_path('invoices/' . $filename);
		$pdf = PDF::loadView('invoices.pdf', ['invoice' => $invoice, 'job'=>$job, 'client'=>$client, 'serve'=>$serve, 'date',$date])->save($filepath);

		//Update Invoice
		$invoice->client_amt = $input["client_amt"];
		$invoice->vendor_amt = $input["vendor_amt"];
		$invoice->doc_id = $file;
		$invoice->save();

		//Complete task
		$this->tasks->TaskComplete(Input::get('taskId'));

	}
	
	public function service_documents($id){

		$job = Jobs::whereId($id)->first();
		
		if(Auth::user()->company_id == $job->vendor) {

			$path = storage_path('proofs/SijwEb_proof.pdf');

			return Response::download($path);
		}
	}

	public function filed(){

	}
	
	public function filing(){
		$tasks = DB::table('tasks')->where('id', Session::get('tasks_id'))->first();
		if($tasks->process == 1 AND $tasks->step == 3){
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
	if($tasks->process == 1 AND $tasks->step == 3){
		$destinationPath = public_path().'/service_documents';
		$file = str_random(6);
		$filename = $orders->id.'_'.$file.'.pdf';
		Input::file('documents')->move($destinationPath, $filename);
		
		//Update Table
		//$orders->filed_docs = $filename;
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
	if($tasks->process == 1 AND $tasks->step == 4){
		$destinationPath = public_path().'/recorded_documents';
		$file = str_random(6);
		$filename = $orders->id.'_'.$file.'.pdf';
		Input::file('documents')->move($destinationPath, $filename);
		
		//Update Table
		//$orders->rec_docs = $filename;
		//$orders->instrument = Input::get('recording');
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

	public function tasksTable(){

		if(Auth::user()->user_role=='Admin') {
			//Find current tasks

			$tasks = Tasks::whereNULL('completion')->whereStatus(1)->take(10)->orderBy('deadline', 'desc')->get();

			$tableData = array();

			foreach ($tasks as $task){

				$tableData[$task->id]["vendor"] = $this->company->taskVendor($task->group);
			}

			Return View::make('tasks.admin')->with(['tasks' => $tasks, 'tableData' => $tableData]);

		}
		elseif(Auth::user()->user_role=='Vendor') {

			$tasks = Tasks::whereNULL('completion')->whereStatus(1)->whereGroup(Auth::user()->company_id)->take(10)->orderBy('deadline', 'desc')->get();

			$tableData = array();

			foreach ($tasks as $task){

				$tableData[$task->id]["defendant"] = Jobs::whereId($task->job_id)->pluck('defendant');
			}

			Return View::make('tasks.vendor')->with(['tasks' => $tasks, 'tableData' => $tableData]);
		}

	}
	public function declaration(){
		//Retrieve data for array for declaration
		$serve = DB::table('serve')->where('job_id', Input::get('job_id'))->first();
		$job = Jobs::whereId(Input::get('job_id'))->first();
        $orderId = Jobs::whereId(Input::get('job_id'))->pluck('order_id');
        $task = Tasks::whereId(Input::get('tasks_id'))->first();
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

        //Update Table
        $dbDoc = new Documents;
        $dbDoc->document = 'Unexecuted_Declaration';
        $dbDoc->jobId = Input::get('job_id');
        $dbDoc->orderId = $orderId;
        $dbDoc->filename = $filename;
        $dbDoc->filepath = '/declarations';
        $dbDoc->save();

        if($task->step == 4) {
            $this->tasks->TaskComplete(Input::get('tasks_id'));
        }
		return $pdf->download($filename);		
	}

    public function proofFiled(){

        $this->tasks->TaskComplete(Input::get('taskId'));

        $jobId = Tasks::whereId(Input::get('taskId'))->pluck('job_id');

        Return Redirect::route('jobs.show', $jobId);
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
