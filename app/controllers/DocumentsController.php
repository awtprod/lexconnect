<?php

class DocumentsController extends \BaseController {

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //Find document data
        $document = Documents::whereId($id)->first();

        //Find task data
        if($document->job_id == 'NULL'){

            $tasks = Tasks::whereorderId($document->order_id)->get();
        }
        else{

            $tasks = Tasks::wherejobId($document->job_id)->get();
        }

        //Find Order data
        $order = Orders::whereId($document->order_id)->first();

        //Find if vendor is assigned to job

        $vendor = false;
        foreach($tasks as $task){

            if($task->group == Auth::user()->company_id){

                $vendor = true;
            }
        }

        //Determine user access
        if(Auth::user()->user_role=='Admin' OR $order->client == Auth::user()->company OR $vendor == true){

        //Load pdf
            $filepath = storage_path().'/'.$document->filepath.'/'.$document->filename;

            return Response::make(file_get_contents($filepath), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; '.$document->filename,
            ]);

        }
        else{

            Return "Not Authorized To View!";

        }
    }

    public function upload(){

        $orderId = Input::get('orderId');

        if(!empty($orderId)) {

            $client = Orders::whereId($orderId)->pluck('company');

            if (Auth::user()->company == $client OR Auth::user()->user_role == 'Admin') {

                //Find documents to be served for this order
                $documentsServed = DocumentsServed::whereOrderid($orderId)->get();

                //Find if documents need to be filed
                $filing = Tasks::whereOrderId($orderId)->whereProcess(1)->whereStep(5)->first();

                //Find if documents need to be recorded
                $recording = Tasks::whereOrderId($orderId)->whereProcess(1)->whereStep(7)->first();

                Return View::make('documents.upload')->with(['documentsServed' => $documentsServed])->with('filing', $filing)->with('recording', $recording)->with('orderId', $orderId);
            }
        }


    }

    public function view(){

        //Get order Id, if available
        $orderId = Input::get('orderId');

        //Get order information
        $order = Orders::whereId($orderId)->first();

        //Get job Id, if available
        $jobId = Input::get('jobId');

        //Get job information
        $job = Jobs::whereId($jobId)->first();


        //Find documents for order
        if(!empty($orderId)){

        //Find if client or Admin
        if(Auth::user()->company==$order->company OR Auth::user()->user_role=='Admin'){

            $documents = Documents::whereOrderid($orderId)->orderBy('created_at', 'desc')->get();

           Return View::make('documents.OrderView')->with(['documents' => $documents]);
        }

        }

        //Find documents for Job
        if(!empty($jobId)){

            //Find if vendor or Admin
            if(Auth::user()->company_id==$job->vendor OR Auth::user()->user_role=='Admin'){

                //Find documents to be served
                $servDocs = DocumentsServed::whereOrderid($job->order_id)->get();

                //Initialize array for service documents
                $serviceDocuments = array();

                //Find service documents
                foreach($servDocs as $servDoc) {

                    //Find recorded Notice of Pendency
                    if ($servDoc->document == 'Notice of Pendency') {

                        $doc = Documents::whereOrderid($job->order_id)->where('document', 'Recorded_Notice_of_Pendency')->orderBy('created_at', 'desc')->first();

                        //If empty, find unrecorded NoP
                        if (empty($doc)) {

                            $docRecNOP = Documents::whereOrderid($job->order_id)->where('document', 'Unrecorded_Notice_of_Pendency')->orderBy('created_at', 'desc')->first();

                        } //If not empty, save doc to array
                        else {

                            $serviceDocuments[$servDoc->id] = $doc;
                        }
                        if (!empty($docRecNOP)) {

                            $serviceDocuments[$servDoc->id] = $docRecNOP;

                        }
                    }

                    //Find recorded Lis Pendens
                    if ($servDoc->document == "Lis Pendens") {

                        $docLP = Documents::whereOrderid($job->order_id)->where('document', 'Recorded_Lis_Pendens')->orderBy('created_at', 'desc')->first();

                        //If empty, find unrecorded LP
                        if (empty($docLP)) {

                            $docRecLP = Documents::whereOrderid($job->order_id)->where('document', 'Unrecorded_Lis_Pendens')->orderBy('created_at', 'desc')->first();

                        } //If not empty, save doc to array
                        else {

                            $serviceDocuments[$servDoc->id] = $docLP;
                        }
                        if (!empty($docRecLP)) {

                            $serviceDocuments[$servDoc->id] = $docRecLP;

                        }
                    }

                    //Find filed complaint
                    if ($servDoc->document == "Complaint") {

                        $docCom = Documents::whereOrderid($job->order_id)->where('document', 'Unfiled_Complaint')->orderBy('created_at', 'desc')->first();

                        //If empty, find unfiled Complaint
                        if (empty($docCom)) {

                            $docFiledCom = Documents::whereOrderid($job->order_id)->where('document', 'Filed_Complaint')->orderBy('created_at', 'desc')->first();

                        } //If not empty, save doc to array
                        else {

                            $serviceDocuments[$servDoc->id] = $docCom;
                        }
                        if (!empty($docFiledCom)) {

                            $serviceDocuments[$servDoc->id] = $docFiledCom;

                        }
                    } //Find other service documents
                    else {

                        $docOther = Documents::whereOrderid($job->order_id)->where('document', $servDoc->document)->orderBy('created_at', 'desc')->first();


                        if (!empty($docOther)) {

                            $serviceDocuments[$servDoc->id] = $docOther;

                        }

                    }
                }

                }
                //Find other documents for job
                $jobDocuments = Documents::whereJobid($jobId)->orderBy('created_at', 'desc')->get();

                Return View::make('documents.jobView')->with(['serviceDocuments' => $serviceDocuments])->with(['jobDocuments' => $jobDocuments])->with(['servDocs' => $servDocs]);


            }

        }


    public function filedDocuments(){

        $task = Tasks::whereId(Input::get('taskId'))->first();

        //Validate File
        $docInput = array('file' => Input::file('filedDocuments'), 'docType' => 'PDF');
        if (!$this->documents->ValidFiles($docInput)) {
            return Redirect::back()->withInput()->withErrors($this->documents->errors);
        }

        //Save File
        $destinationPath = public_path() . '/service_documents';
        $file = str_random(6);
        $filename = $task->order_id . '_' . $file . '.pdf';
        Input::file('filedDocuments')->move($destinationPath, $filename);

        //save to db
        $dbDoc = new Documents;
        $dbDoc->document = 'Filed Documents';
        if (!empty($jobId)) {
            $dbDoc->jobId = $jobId;
        }
        $dbDoc->orderId = $task->order_id;
        $dbDoc->filename = $filename;
        $dbDoc->filepath = 'service_documents';
        $dbDoc->save();

        //Update filing information
        $order = Orders::whereId($task->order_id)->first();
        $order->courtcase = Input::get('case');
        $order->fileDate = Input::get('date');
        $order->save();

        //Complete task
        $this->tasks->TaskComplete($task->id);

        Return Redirect::route('jobs.show', $task->job_id);

    }

    public function storeDocuments()
    {

        $docsUploaded = array();

        $orderId = Input::get('orderId');

        //Retrieve document list
        $documents = Input::get('documents');

        //Loop through uploaded files and validate them
        foreach ($documents as $document) {
            if (Input::file($document) != NULL) {

                //Validate File
                $docInput = array('file' => Input::file($document), 'docType' => $document);
                if (!$this->documents->ValidFiles($docInput)) {
                    return Redirect::back()->withInput()->withErrors($this->documents->errors);
                }

                //Save File
                $destinationPath = public_path() . '/service_documents';
                $file = str_random(6);
                $filename = Input::get('orderId') . '_' . $file . '.pdf';
                Input::file($document)->move($destinationPath, $filename);

                //save to db
                $dbDoc = new Documents;
                $dbDoc->document = $document;
                if (!empty($jobId)) {
                    $dbDoc->jobId = $jobId;
                }
                $dbDoc->orderId = Input::get('orderId');
                $dbDoc->filename = $filename;
                $dbDoc->filepath = 'service_documents';
                $dbDoc->save();

                $docsUploaded[$document] = $document;

            }
        }

        //Check to see if documents for filing have been uploaded


        //See if filing is needed
        $filing = DB::table('tasks')->where('order_id', $orderId)
            ->where('process', '=', 1)
            ->where('step', '=', 6)
            ->where('completion', NULL)->orderBy('completion', 'asc')->first();

        //If server is filing documents, check to see if summons and complaint have been uploaded
        $docsUploadedFiling = false;

        if (!empty($filing)) {

            $summons = Documents::whereDocument('Summons')->whereOrderid(Input::get('orderId'))->first();

            $complaint = Documents::whereDocument('Unfiled_Complaint')->whereOrderid(Input::get('orderId'))->first();

            if (!empty($summons) AND !empty($complaint)) {

                $docsUploadedFiling = true;
            }
            else{

                $docsUploadedFiling = false;
            }
        }

        //If filing, complete

        //See if recording is needed
        $recording = DB::table('tasks')->where('order_id', $orderId)
            ->where('process', '=', 1)
            ->where('step', '=', 7)
            ->where('completion', NULL)->orderBy('completion', 'asc')->first();

        if(!empty($recording)){

            $lisPendens = Documents::whereDocument('Unrecorded_Lis_Pendens')->whereOrderid(Input::get('orderId'))->first();

            $NOP = Documents::whereDocument('Unrecorded_Notice_Of_Pendency')->whereOrderid(Input::get('orderId'))->first();

            if(empty($lisPendens) AND empty($NOP)){

                $docsUploadedRecording = false;
            }
            else{

                $docsUploadedRecording = true;
            }

            if(!empty($filing)){

                if(($docsUploadedFiling == true) AND ($docsUploadedRecording == true)){

                    //Find task id for "documents uploaded" for filing

                    $filingTask = Tasks::whereProcess(1)->whereStep(1)->whereOrderId(Input::get('orderId'))->first();

                    //Complete "documents uploaded" task

                    $this->tasks->TaskComplete($filingTask->id);

                }

            }
            elseif($docsUploadedRecording == true){

                //Find task id for "documents uploaded" for filing

                $filingTask = Tasks::whereProcess(1)->whereStep(1)->whereOrderId(Input::get('orderId'))->first();

                //Complete "documents uploaded" task

                    $this->tasks->TaskComplete($filingTask->id);

            }

        }
        elseif($docsUploadedFiling == true){

            //Find task id for "documents uploaded" for filing

            $filingTask = Tasks::whereProcess(1)->whereStep(1)->whereOrderId(Input::get('orderId'))->first();

            //Complete "documents uploaded" task

            $this->tasks->TaskComplete($filingTask->id);

        }
        //See if service is on hold
        $service = DB::table('tasks')->where('order_id', $orderId)
            ->where('process', '>', 1)
            ->where('status', '=', 0)
            ->where('completion', NULL)->orderBy('completion', 'asc')->first();

        //Loop through documents to see if all documents for service have been uploaded
        $allDocsUploaded = true;
        foreach ($documents as $document) {

            $uploaded = Documents::whereDocument($document)->whereOrderid(Input::get('orderId'))->first();

            if(empty($uploaded)){

                $allDocsUploaded = false;
            }



        }


        //If all documents have been uploaded, release docs for service
        if($allDocsUploaded == true) {

            if (empty($filing) OR empty($service) OR empty($recording)) {
                $this->tasks->WaitingDocs(Input::get('orderId'));

                //Update Due Dates of Tasks
                $jobs = DB::table('jobs')->where('order_id', Input::get('orderId'))
                    ->where('completed', NULL)->get();

                if (!empty($jobs)) {
                    foreach ($jobs as $job) {
                        $task = DB::table('tasks')->where('job_id', $job->id)
                            ->where('completion', NULL)
                            ->where('process', '>', 1)->orderBy('completion', 'asc')->first();
                        if (!empty($task)) {
                            $this->tasks->TaskForecast($task->id);

                        }
                    }
                }
            }
        }

//Send to confirmation page
        Return View::make('documents.confirmation')->with(['docsUploaded' => $docsUploaded]);

        }
    public function storeFiledDocuments()
    {

        $docsUploaded = array();

        $orderId = Input::get('orderId');

        //Retrieve document list
        $documents = Input::get('documents');

        //Loop through uploaded files and validate them
        foreach ($documents as $document) {
            if (Input::file($document) != NULL) {

                //Validate File
                $docInput = array('file' => Input::file($document), 'docType' => $document);
                if (!$this->documents->ValidFiles($docInput)) {
                    return Redirect::back()->withInput()->withErrors($this->documents->errors);
                }

                //Save File
                $destinationPath = public_path() . '/service_documents';
                $file = str_random(6);
                $filename = Input::get('orderId') . '_' . $file . '.pdf';
                Input::file($document)->move($destinationPath, $filename);

                //save to db
                $dbDoc = new Documents;
                $dbDoc->document = $document;
                if (!empty($jobId)) {
                    $dbDoc->jobId = $jobId;
                }
                $dbDoc->orderId = Input::get('orderId');
                $dbDoc->filename = $filename;
                $dbDoc->filepath = 'service_documents';
                $dbDoc->save();

                $docsUploaded[$document] = $document;

            }
        }

        //Check to see if documents for filing have been uploaded


        //Find filing task id
        $filing = DB::table('tasks')->where('order_id', $orderId)
            ->where('process', '=', 1)
            ->where('step', '=', 6)
            ->where('completion', NULL)->orderBy('completion', 'asc')->first();

        //If server is filing documents, check to see if summons and complaint have been uploaded

        if ($filing->id == Input::get('taskId')) {

            $summons = Documents::whereDocument('Summons')->whereOrderid(Input::get('orderId'))->first();

            $complaint = Documents::whereDocument('Filed_Complaint')->whereOrderid(Input::get('orderId'))->first();

            if (!empty($summons) AND !empty($complaint)) {

                //Complete "documents uploaded" task

                $this->tasks->TaskComplete(Input::get('taskId'));

                //See if service is on hold
                $service = DB::table('tasks')->where('order_id', $orderId)
                    ->where('process', '>', 1)
                    ->where('status', '=', 0)
                    ->where('completion', NULL)->orderBy('completion', 'asc')->first();

                //Loop through documents to see if all documents for service have been uploaded
                $allDocsUploaded = true;
                foreach ($documents as $document) {

                    $uploaded = Documents::whereDocument($document)->whereOrderid(Input::get('orderId'))->first();

                    if(empty($uploaded)){

                        $allDocsUploaded = false;
                    }
                }


                //If all documents have been uploaded, release docs for service
                if($allDocsUploaded == true) {

                    if (empty($filing) OR empty($service) OR empty($recording)) {
                        $this->tasks->WaitingDocs(Input::get('orderId'));

                        //Update Due Dates of Tasks
                        $jobs = DB::table('jobs')->where('order_id', Input::get('orderId'))
                            ->where('completed', NULL)->get();

                        if (!empty($jobs)) {
                            foreach ($jobs as $job) {
                                $task = DB::table('tasks')->where('job_id', $job->id)
                                    ->where('completion', NULL)
                                    ->where('process', '>', 1)->orderBy('completion', 'asc')->first();
                                if (!empty($task)) {
                                    $this->tasks->TaskForecast($task->id);

                                }
                            }
                        }
                    }
                }
            }
        }


        //Find recording task id
        $recording = DB::table('tasks')->where('order_id', $orderId)
            ->where('process', '=', 1)
            ->where('step', '=', 7)
            ->where('completion', NULL)->orderBy('completion', 'asc')->first();

        if($recording->id == Input::get('taskId')) {

            $lisPendens = Documents::whereDocument('Recorded_Lis_Pendens')->whereOrderid(Input::get('orderId'))->first();

            $NOP = Documents::whereDocument('Recorded_Notice_Of_Pendency')->whereOrderid(Input::get('orderId'))->first();

            if (empty($lisPendens) AND empty($NOP)) {

                $docsUploadedRecording = false;
            } else {

                $docsUploadedRecording = true;

                //Complete "documents uploaded" task

                $this->tasks->TaskComplete(Input::get('taskId'));
            }
        }



//Send to confirmation page
        Return View::make('documents.confirmation')->with(['docsUploaded' => $docsUploaded]);

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
