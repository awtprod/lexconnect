<?php
use Carbon\Carbon;
class TasksController extends \BaseController {
    public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, Documents $documents)
    {

        $this->orders = $orders;
        $this->tasks = $tasks;
        $this->reprojections = $reprojections;
        $this->jobs = $jobs;
        $this->invoices = $invoices;
        $this->DocumentsServed = $DocumentsServed;
        $this->documents = $documents;
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
        //Retrieve task id from URL
        $tasksId = Input::get('id');

        //Determine if serve was accepted by vendor
        $accept = Input::get('accept');


        //Retrieve task data from db
        $CurrentTask = Tasks::whereId($tasksId)->first();

        //Get zip code of serve address
        $job = DB::table('jobs')->where('id', $CurrentTask->job_id)->first();

        $order = Orders::whereId($CurrentTask->order_id)->first();

        //Determine if the defendant was served
        $served = Input::get('served');


        //Find current task due
        $LatestTask = DB::table('tasks')->OrderBy('step', 'asc')
            ->where('job_id', $CurrentTask->job_id)
            ->where('completion', NULL)->first();

        //See if requested task is current task due, if not throw an error
        if ($LatestTask->id != $tasksId) {

            Return "Not Authorized To View!";

        //Check to see if user is authorized to complete task
        } elseif (Auth::user()->user_role=='Admin' OR Auth::user()->company_id == $CurrentTask->vendor) {

            //Complete vendor accept serve step
            if ($CurrentTask->step == 0){


            //If vendor accepts serve, complete step and proceed with serve
            if($accept == TRUE){

            $this->tasks->TaskComplete($tasksId);

            return Redirect::back();

            }
            //If vendor rejects serve, find next server
            elseif($accept == false){

            //Complete Task
            $this->tasks->TaskComplete($tasksId);

            //Find next available server
            //$serverData = array('jobId' => $CurrentTask->job_id, 'zipcode' => $job->zipcode);
            //$server = $this->jobs->SelectServer($serverData);

            //Assign tasks to new server
            //$newServer = array('jobId' => $CurrentTask->job_id, 'vendor' => $server, 'orderId' => $CurrentTask->order_id);
            //$newTask = $this->tasks->ReAssignServer($newServer);

            //Reproject pending tasks
            //$this->tasks->TaskReproject($newTask);

            return Redirect::to('/');

            }
            }

            if($CurrentTask->process == 1){

                //Documents picked up
                if($CurrentTask->step == 2){

                    $this->tasks->TaskComplete($CurrentTask->id);

                    Return Redirect::route('jobs.show', $CurrentTask->job_id);
                }

                //QA Documents
                if($CurrentTask->step == 3){

                    //Find uploaded service documents
                    $documents = Documents::whereOrderid($CurrentTask->order_id)->get();

                    Return View::make('tasks.qa')->with(['task' => $CurrentTask])->with(['documents' => $documents]);
                }

                //Documents received
                if($CurrentTask->step == 5){

                    $this->tasks->TaskComplete($CurrentTask->id);

                    Return Redirect::route('jobs.show', $CurrentTask->job_id);
                }

                //Documents filed
                if($CurrentTask->step == 6){

                    //Find documents to be served for this order
                    $documentsServed = DocumentsServed::whereOrderid($order->id)->get();

                    Return View::make('tasks.filed')->with(['task' => $CurrentTask])->with(['documentsServed' => $documentsServed]);
                }

                //Documents recorded
                if($CurrentTask->step == 7){

                    //Find documents to be served for this order
                    $documentsServed = DocumentsServed::whereOrderid($order->id)->get();

                    Return View::make('tasks.recorded')->with(['task' => $CurrentTask])->with(['documentsServed' => $documentsServed]);
                }
            }

            //If posting, complete serve
            if($CurrentTask->process == 3 AND $CurrentTask->step == 1){

                Return View::make('serve.posting')->with(['task' => $CurrentTask]);

            }

            if($CurrentTask->process == 2 OR $CurrentTask->process == 3) {
                //Enter service attempt
                if ($CurrentTask->step == 1) {

                    //If defendant was served, enter service details
                    if ($served == TRUE) {

                        Return View::make('serve.create')->with(['task' => $CurrentTask]);

            } //If service attempt, enter attempt details
                    else {

                        Return View::make('attempts.create')->with(['task' => $CurrentTask])->with(['job' => $job]);
            }
                }

                //Generate and upload proof
                if ($CurrentTask->step == 2) {

                    //Find proofs, if any

                    $proof = Documents::whereJobid($job->id)->whereDocument('Unexecuted_Proof')->OrderBy('created_at', 'desc')->pluck('filename');

                    //Find server for this company

                    $servers = User::whereCompany(Auth::user()->company)->lists('name', 'name');

                    Return View::make('tasks.generate')->with(['task' => $CurrentTask])->with(['proof' => $proof])->with(['servers' => $servers])->with(['job' => $job]);

            }

                //File proof with court or send to client
                if ($CurrentTask->step == 3) {

                    Return View::make('tasks.send')->with(['order' => $order])->with(['task' => $CurrentTask]);
            }

                //Generate declaration
                if ($CurrentTask->step == 4) {

                    //Find proofs, if any

                    $proof = Documents::whereJobid($job->id)->whereDocument('Unexecuted_Declaration')->OrderBy('created_at', 'desc')->pluck('filename');

                    //Find server for this company

                    $servers = User::whereCompany(Auth::user()->company)->lists('name', 'name');

                    Return View::make('tasks.declaration')->with(['task' => $CurrentTask])->with(['proof' => $proof])->with(['servers' => $servers])->with(['job' => $job]);

                }

                //upload declaration
                if ($CurrentTask->step == 5) {

                    //Find proofs, if any

                    $proof = Documents::whereJobid($job->id)->whereDocument('Unexecuted_Declaration')->OrderBy('created_at', 'desc')->pluck('filename');

                    //Find server for this company

                    $servers = User::whereCompany(Auth::user()->company)->lists('name', 'name');

                    Return View::make('tasks.declaration')->with(['task' => $CurrentTask])->with(['proof' => $proof])->with(['servers' => $servers])->with(['job' => $job]);

                }
            }

        }
        else{

            Return "Not Authorized To View!";
        }
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
	public function proof(){

        $orderId = Jobs::whereId(Input::get('job_id'))->pluck('order_id');

        $taskId = Tasks::whereJobId(Input::get('job_id'))->first();

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

            if($taskId->process == 3){

                $pdf = PDF::loadView('tasks.post', ['data' => $data], ['proof' => $proof], ['serve' => $serve])->save($filepath);

            }
            else {
                $pdf = PDF::loadView('tasks.serve', ['data' => $data], ['proof' => $proof], ['serve' => $serve])->save($filepath);
            }
            //Update Table
            $dbDoc = new Documents;
            $dbDoc->document = 'Unexecuted_Proof';
            $dbDoc->jobId = Input::get('job_id');
            $dbDoc->orderId = $orderId;
            $dbDoc->filename = $filename;
            $dbDoc->filepath = '/proofs';
            $dbDoc->save();

		return $pdf->download($filename);		
		}

        //If defendant was NOT served
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

            //Update Table
            $dbDoc = new Documents;
            $dbDoc->document = 'Unexecuted_Proof';
            $dbDoc->jobId = Input::get('job_id');
            $dbDoc->orderId = $orderId;
            $dbDoc->filename = $filename;
            $dbDoc->filepath = '/proofs';
            $dbDoc->save();

		return $pdf->download($filename);
		}
	}
	public function create_dec(){
	
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
