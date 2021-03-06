<?php

class RulesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Admin'){
		$rules = DB::table('rules')->orderBy('name', 'asc')->get();
		
		Return View::make('rules.index')->with(['rules' => $rules]);
		}
		else{
		Return "Not Authorized!";
		}
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if(Auth::user()->user_role == 'Admin'){
		$rules = DB::table('rules')->orderBy('name', 'asc')->get();
		
		Return View::make('rules.create')->with(['rules' => $rules]);
		}
		else{
		Return "Not Authorized!";
		}
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//Retrieve Data
		$state = Input::get('state');
		$mailing = Input::get('mailing');
		$affidavit = Input::get('affidavit');
		$filing_client = Input::get('filing_client');
		$filing_vendor = Input::get('filing_vendor');
		$service_client = Input::get('service_client');
		$service_vendor = Input::get('service_vendor');
		
		//Find IDs of states
		$ids = DB::table('rules')->orderBy('name', 'asc')->get();
		
		//Save Rule Updates
		foreach($ids as $id){
		$rule = Rules::whereName($state[$id->id])->first();
		
		if(empty($mailing[$id->id])){
		$rule->mailing = 'no';
		}
		if(!empty($mailing[$id->id])){
		$rule->mailing = $mailing[$id->id];
		}
		if(empty($affidavit[$id->id])){
		$rule->affidavit = 'no';
		}
		if(!empty($affidavit[$id->id])){
		$rule->affidavit = $affidavit[$id->id];
		}
		$rule->filing_client = $filing_client[$id->id];
		$rule->filing_vendor = $filing_vendor[$id->id];
		$rule->service_client = $service_client[$id->id];
		$rule->service_vendor = $service_vendor[$id->id];
		$rule->save();
		}
		
		Return Redirect::Route('rules.index');
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
