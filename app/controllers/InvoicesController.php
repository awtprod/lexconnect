<?php
use Carbon\Carbon;

class InvoicesController extends \BaseController {

	public function earnings(){

		if(Auth::user()->user_role == 'Vendor' AND Auth::user()->role == 'Supervisor'){

			Return View::make('invoices.earnings');
		}
	}

	public function earnings_table(){

		$input = Input::all();
		if(!empty($input["month"])){
			//Carbon::setToStringFormat('F Y');

			if(is_numeric($input["month"])){
				$offset = $input["month"];
				$dates = Carbon::now()->addMonths($input["month"])->format('F Y');

			}
			else{
				$offset = 0;
				$dates = Carbon::now()->format('F Y');
			}
			$earnings = Invoices::where( DB::raw('Month(created_at)'), '=', date('m')-$offset )->whereVendor(Auth::user()->company_id)->orderBy('created_at','desc')->get();
		}
		elseif(!empty($input["year"])){

			//Carbon::setToStringFormat('Y');

			if(is_numeric($input["year"])){
				$offset = $input["year"];
				$dates = Carbon::now()->subYears($input["year"])->format('Y');

			}
			else{
				$offset = 0;
				$dates = Carbon::now()->format('Y');
			}

			$earnings = Invoices::where( DB::raw('Year(created_at)'), '=', date('Y')-$offset )->whereVendor(Auth::user()->company_id)->orderBy('created_at','desc')->get();
		}
		else{
			$earnings = Invoices::whereBetween('created_at',array($input["start_date"],$input["end_date"]))->whereVendor(Auth::user()->company_id)->orderBy('created_at','desc')->get();
			$dates = date('F j, Y', strtotime($input["start_date"])).' - '.date('F j, Y', strtotime($input["end_date"]));
		}

		$task = array();

		foreach ($earnings as $earning){
			$task[$earning->id] = Jobs::whereId($earning->job_id)->pluck('service');
		}

		Return View::make('invoices.earnings_table',['earnings'=>$earnings,'task'=>$task,'input'=>$input])->with('dates',$dates);
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
