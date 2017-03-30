<?php

class DocumentsController extends \BaseController {


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

    public function upload($orderId){

        if(!empty($orderId)) {

            $client = Orders::whereId($orderId)->pluck('company');

            if (Auth::user()->company == $client OR Auth::user()->user_role == 'Admin') {


                $documents = array(['Notice of Trustee Sale', 'Notice of Trustee Sale'],['AmendedSummons','Amended Summons'],['Summons','Summons'], ['AmendedComplaint','Amended Complaint'],['Complaint','Complaint'], ['NoticeOfPendency', 'Notice of Pendency'], ['LisPendens','Lis Pendens'], ['DeclarationOfMilitarySearch','Declaration of Military Search'], ['CaseHearingSchedule','Case Hearing Schedule']);

                Return View::make('documents.upload')->with(['documents' => $documents])->with('orderId', $orderId);
            }
        }


    }

    public function view(){

        //Get job Id, if available
        $orderId = Input::get('orderId');

        //Get order information
        $order = Orders::whereId($orderId)->first();

        //Get job Id, if available
        $jobId = Input::get('jobId');

        //Get job information
        $job = Jobs::whereId($jobId)->first();

        //if provide only job id, find order info
        if(empty($order)){

            $orderId = $job->order_id;

            $order = Orders::whereId($job->order_id)->first();
        }
        //Find documents for order

        //Find if client or Admin or vendor
        if(Auth::user()->company_id==$order->company OR Auth::user()->user_role=='Admin' OR (Auth::user()->company_id==$job->vendor AND $orderId == $job->order_id)){

            //if vendor, find documents by job
            if(!empty($job)){

                $documents = Documents::wherejobId($jobId)->orWhere(function($query) use ($orderId)
                                                            {
                                                                $query->whereNull('job_id')
                                                                      ->where('order_id', '=', $orderId);
                                                            })
                                                            ->orderBy('created_at', 'desc')->get();
            }
            else {

                $documents = Documents::whereOrderId($orderId)->orderBy('created_at', 'desc')->get();
            }

            Return View::make('documents.OrderView')->with(['documents' => $documents, 'order' => $order]);
        }
        else{
            Return "Not Authorized To View!";

        }

        /*

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
*/
        }


    public function storeDocuments(){

        $input = Input::all();

        foreach ($input["documents"] as $document) {

            if(!empty($document["file"])) {

                //If valid file, save document
                $this->Documents->saveDoc(['document' => $document, 'orders_id' => $input["orderId"], 'folder' => 'service_documents']);

            }

            //Save service types
            $this->DocumentsServed->saveDocType(['document' => $document, 'orderId' => $input["orderId"]]);
        }

        Return Redirect::route('orders.show', ['id' => $input["orderId"]]);

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
