<?php

class CompanyController extends \BaseController {
	protected $company;
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role=='Admin') {

			$company = $this->company->all();

			return View::make('company.index', ['company' => $company]);
		}
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if(Auth::user()->user_role=='Admin') {

			$states = DB::table('states')->orderBy('name', 'asc')->lists('abbrev', 'abbrev');
			return View::make('company.create', array('states' => $states));
		}
	}
	
	public function store()
	{
		$input = Input::all();
		
		if ( ! $this->company->fill($input)->isValid())
			{
				return Redirect::back()->withInput()->withErrors($this->company->errors);	
			}

		//Create new company
		$new = new Company;
		$new->name = $input["name"];
		$new->address = $input["address"];
		$new->city = $input["city"];
		$new->state = $input["state"];
		$new->zip_code = $input["zip_code"];
		$new->v_c = $input["v_c"];
		if(!empty($input["vendor_prints"])) {
			$new->vendor_prints = 1;
		}
		$new->pay_method = $input["pay_method"];
		$new->phone = $input["phone"];
		$new->email = $input["email"];
		$new->save();

		$address = array (
			"Type" => "UserDefined",
			"Name" => $input["name"],
			"Address" => $input["address"],
			"City" => $input["city"],
			"Region" => $input["state"],
			"PostalCode" => $input["zip_code"],
			"Country" => "US",
			"Category" => $input["v_c"],
			"UserData" => $new->id
		);


		//Post data to GeoSvc
		$this->locations->postLocation(array(['address_string' => json_encode($address), 'address' => $address]));
		
		return Redirect::route('company.index');
	}
	
	public function show($id)
	{

		if(Auth::user()->user_role=='Admin' OR (Auth::user()->company_id == $id AND Auth::user()->role == 'Supervisor')) {

			$company = $this->company->whereId($id)->first();

			return View::make('company.show', ['company' => $company]);
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
		if(Auth::user()->user_role=='Admin' OR (Auth::user()->company_id == $id AND Auth::user()->role == 'Supervisor')) {

			$company = $this->company->whereId($id)->first();

			$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'name');

			return View::make('company.edit', ['company' => $company, 'states' => $states]);
		}
		}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function save()
	{
		$input = Input::all();

		if ( ! $this->company->fill($input)->isValid())
		{
			return Redirect::back()->withInput()->withErrors($this->company->errors);
		}

		$update = Company::whereId($input["id"])->first();

		$update->name = Input::get('name');
		$update->v_c = Input::get('v_c');
		if(!empty($input["vendor_prints"])){
			$update->vendor_prints = 1;
		}
		else{
			$update->vendor_prints = 0;
		}
		$update->pay_method = Input::get('pay_method');
		$update->address = Input::get('address');
		$update->city = Input::get('city');
		$update->state = Input::get('state');
		$update->zip_code = Input::get('zip_code');
		$update->phone = Input::get('phone');
		$update->email = Input::get('email');

		$update->save();

		return Redirect::route('company.show', $input["id"]);

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
