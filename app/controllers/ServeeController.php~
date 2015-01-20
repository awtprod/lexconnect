<?php

class ServeeController extends \BaseController {
	public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices)
	{
	
		$this->orders = $orders;
		$this->tasks = $tasks;
		$this->reprojections = $reprojections;	
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
		
	$servee = Servee::whereId($id)->first();
	
	//Check if client or Admin
	if(Auth::user()->company == $servee->client OR Auth::user()->user_role == 'Admin'){
		
	//Find active serves
	$active = Jobs::whereServeeId($id)
					->whereNull('completed')->first();
	
	//If there is an ative serve, find attempts
	if(!empty($active)){
		
	$attempts = Attempts::whereJob($active->id)->orderBy('created_at', 'asc')->get();
	
	View::share('active', $active);
	View::share(['attempts' => $attempts]);
	
	}
		
	//Find all completed jobs
	$completed = Jobs::whereServeeId($id)
					->whereNotNull('completed')->orderBy('created_at', 'desc')->get();
	
	//If there are completed serves, find invoices
	if(!empty($completed)){
		
	$serveeinvoices = Invoices::whereServeeId($id)->orderBy('created_at', 'asc')->get();
	

	
	//Loop through all jobs
	$serves = array();
	
	foreach($completed as $complete){
	
	$served = Serve::whereServeeId($id)->first();
			
			//Determine if defendant was served
			if(!empty($served)){
				$serves[$complete->id]['served_upon'] = $served->served_upon;
				$serves[$complete->id]['date'] = date("m/d/y", strtotime($served->date));	
				$serves[$complete->id]['time'] = date("h:i A", strtotime($served->time));	
				$serves[$complete->id]['street'] = $complete->street;
				$serves[$complete->id]['city'] = $complete->city;	
				$serves[$complete->id]['state'] = $complete->state;	
				$serves[$complete->id]['zipcode'] = $complete->zipcode;	
				$serves[$complete->id]['proof'] = $complete->proof;	
			//Determine if personal or sub-served
				if($served->sub_served == 0){
				$serves[$complete->id]['description'] = "Personal Service";
				}
				else{
				$serves[$complete->id]['description'] = "Substitute Service";
				$serves[$complete->id]['declaration'] = $complete->declaration;
				}
			}
			//Cancelled Job
			elseif($complete->status == 2){

			$serves[$complete->id]['description'] = "Job Canceled";	
			$serves[$complete->id]['served_upon'] = "N/A";
			$serves[$complete->id]['date'] = date("m/d/y", strtotime($complete->completed));	
			$serves[$complete->id]['time'] = date("h:i A", strtotime($complete->completed));
			$serves[$complete->id]['street'] = $complete->street;
			$serves[$complete->id]['city'] = $complete->city;	
			$serves[$complete->id]['state'] = $complete->state;	
			$serves[$complete->id]['zipcode'] = $complete->zipcode;
			$serves[$complete->id]['proof'] = NULL;
			}
			//Non-Serve
			else{
			//Find date and time on non-serve
			$non = Attempts::whereJob($complete->id)->latest('date')->first();
			
			$serves[$complete->id]['description'] = "Non-Serve";
			$serves[$complete->id]['served_upon'] = "N/A";
			$serves[$complete->id]['date'] = date("m/d/y", strtotime($complete->date));	
			$serves[$complete->id]['time'] = date("h:i A", strtotime($complete->time));
			$serves[$complete->id]['street'] = $complete->street;
			$serves[$complete->id]['city'] = $complete->city;	
			$serves[$complete->id]['state'] = $complete->state;	
			$serves[$complete->id]['zipcode'] = $complete->zipcode;
			$serves[$complete->id]['proof'] = $complete->proof;
			}
	}
		View::share(['serveeinvoices' => $serveeinvoices]);
		View::share(['serves' => $serves]);
		View::share(['completed' => $completed]);
	}
		Return View::make('servee.show')->with('servee', $servee);
	}
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
