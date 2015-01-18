<?php

class CompanyController extends \BaseController {
	protected $company;
	
	public function __construct (Company $company)
	{
	
		$this->company = $company;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
				$company = $this->company->all();
		
		return View::make('company.index', ['company' => $company]);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');
		return View::make('company.create', array('states' => $states));
	}
	
	public function store()
	{
		$input = Input::all();
		
		if ( ! $this->company->fill($input)->isValid())
			{
				return Redirect::back()->withInput()->withErrors($this->company->errors);	
			}
			
			
		$this->company->save();
		
		return Redirect::route('company.index');
	}
	
	public function show($id)
	{
		$company = $this->company->whereId($id)->first();
	
		return View::make('company.show', ['company' => $company]);
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
