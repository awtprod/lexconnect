<?php

class SearchController extends \BaseController {
	public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, Search $search)
	{
	
		$this->orders = $orders;
		$this->tasks = $tasks;
		$this->reprojections = $reprojections;	
		$this->jobs = $jobs;
		$this->invoices = $invoices;
		$this->search = $search;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Input::get('search');
		
		if(Auth::user()->user_role == 'Client'){
			
		//Search database for results
			
		$results = $this->search->ClientSearch($search);

		$search_results = array();
		
		//Loop through search results and find order id
		
		foreach($results['orders'] as $results_orders){
		
		$data = DB::table('orders')->where('id', $results_orders->id)->first();
		
		//find order data
		
		$search_results[$results_orders->id]['order_id'] = $data->id;
		$search_results[$results_orders->id]['ref'] = $data->reference;
		$search_results[$results_orders->id]['plaintiff'] = $data->plaintiff;
		$search_results[$results_orders->id]['defendant'] = $data->defendant;
		$search_results[$results_orders->id]['case'] = $data->case;
		$search_results[$results_orders->id]['state'] = $data->state;
		$search_results[$results_orders->id]['court'] = $data->court;
			
		}
		foreach($results['jobs'] as $results_jobs){

		$data = DB::table('orders')->where('id', $results_jobs->order_id)->first();
		$data2 = DB::table('jobs')->where('id', $results_jobs->id)->first();
		
		//find order data

		$search_results[$results_jobs->order_id]['order_id'] = $data->id;
		$search_results[$results_jobs->order_id]['ref'] = $data->reference;
		$search_results[$results_jobs->order_id]['plaintiff'] = $data->plaintiff;
		$search_results[$results_jobs->order_id]['defendant'] = $data2->defendant;
		$search_results[$results_jobs->order_id]['case'] = $data->case;
		$search_results[$results_jobs->order_id]['state'] = $data->state;
		$search_results[$results_jobs->order_id]['court'] = $data->court;
			
		}
		
		if((count($results['jobs']) == 1 AND count($results['orders']) == 0) OR (count($results['jobs']) == 0 AND count($results['orders']) == 1)){
			
		Return Redirect::route('orders.show')->with('orders_id', $data->id);
			
		}
		

	
		Return View::make('search.index')->with(['search_results' => $search_results])->with(['results' => $results]);		
		}

		elseif(Auth::user()->user_role == 'Vendor'){	
			
		//Search database for results
			
		$results = $this->search->VendorSearch($search);

		$search_results = array();
		
		//Loop through search results and find job id
		
		foreach($results as $result){

		$data = DB::table('orders')->where('id', $result->order_id)->first();
		$data2 = DB::table('jobs')->where('id', $result->id)->first();
		
		//find order data

		$search_results[$result->id]['job_id'] = $data->id;
		$search_results[$result->id]['plaintiff'] = $data->plaintiff;
		$search_results[$result->id]['defendant'] = $data2->defendant;
		$search_results[$result->id]['case'] = $data->case;
		$search_results[$result->id]['state'] = $data->state;
		$search_results[$result->id]['court'] = $data->court;
			
		}		
		if((count($results) == 1){
			
		Return Redirect::route('jobs.show')->with('id', $data2->id);
			
		}
		
		Return View::make('search.vendor')->with(['search_results' => $search_results])->with(['results' => $results]);					
			
		}
		
		elseif(Auth::user()->user_role == 'Admin'){
			
		//Search database for results
			
		$results = $this->search->AdminSearch($search);

		$search_results = array();
		
		//Loop through search results and find order id
		
		foreach($results['orders'] as $results_orders){
		
		$data = DB::table('orders')->where('id', $results_orders->id)->first();
		
		//find order data
		
		$search_results[$results_orders->id]['order_id'] = $data->id;
		$search_results[$results_orders->id]['ref'] = $data->reference;
		$search_results[$results_orders->id]['plaintiff'] = $data->plaintiff;
		$search_results[$results_orders->id]['defendant'] = $data->defendant;
		$search_results[$results_orders->id]['case'] = $data->case;
		$search_results[$results_orders->id]['state'] = $data->state;
		$search_results[$results_orders->id]['court'] = $data->court;
			
		}
		foreach($results['jobs'] as $results_jobs){

		$data = DB::table('orders')->where('id', $results_jobs->order_id)->first();
		$data2 = DB::table('jobs')->where('id', $results_jobs->id)->first();
		
		//find order data

		$search_results[$results_jobs->order_id]['order_id'] = $data->id;
		$search_results[$results_jobs->order_id]['ref'] = $data->reference;
		$search_results[$results_jobs->order_id]['plaintiff'] = $data->plaintiff;
		$search_results[$results_jobs->order_id]['defendant'] = $data2->defendant;
		$search_results[$results_jobs->order_id]['case'] = $data->case;
		$search_results[$results_jobs->order_id]['state'] = $data->state;
		$search_results[$results_jobs->order_id]['court'] = $data->court;
			
		}
		
		if((count($results['jobs']) == 1 AND count($results['orders']) == 0) OR (count($results['jobs']) == 0 AND count($results['orders']) == 1)){
			
		Return Redirect::route('orders.show')->with('orders_id', $data->id);
			
		}
		

	
		Return View::make('search.index')->with(['search_results' => $search_results])->with(['results' => $results]);		
		}		
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
