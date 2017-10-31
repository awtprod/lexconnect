<?php

class InvoicesController extends \BaseController {

	public function earnings(){

		if(Auth::user()->user_role == 'Vendor' AND Auth::user()->role == 'Supervisor'){

			Return View::make('invoices.earnings');
		}
	}

	public function earnings_table(){

		$input = Input::all();

		if(!empty($input["month"])){

			if(is_numeric($input["month"])){
				$offset = $input["month"];
			}
			else{
				$offset = 0;
			}

			$earnings = Invoices::where( DB::raw('Month(created_at)'), '=', date('m')-$offset )->whereVendor(Auth::user()->company_id)->orderBy('created_at','desc')->get();
		}
		elseif(!empty($input["year"])){

			if(is_numeric($input["year"])){
				$offset = $input["year"];
			}
			else{
				$offset = 0;
			}

			$earnings = Invoices::where( DB::raw('Year(created_at)'), '=', date('Y')-$offset )->whereVendor(Auth::user()->company_id)->orderBy('created_at','desc')->get();
		}
		else{
			$earnings = Invoices::where('created_at','<=',$input["start_date"])->where('created_at','>=',$input["end_date"])->whereVendor(Auth::user()->company_id)->orderBy('created_at','desc')->get();

		}
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
