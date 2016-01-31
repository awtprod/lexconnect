<?php

class ClientRatesController extends \BaseController {

	public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, Processes $processes, Steps $steps, Template $template, Counties $counties)
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
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Admin'){


		//Get client Id
		$clientId = Input::get('clientId');

		if(empty($clientId)) {

				$clientId = Session::get('clientId');
		}

        //Get list of clients
        $clients = DB::table('company')->where('v_c', 'Client')->orderBy('v_c', 'asc')->lists('name', 'id');

		//Find states
		$states = DB::table('states')->orderBy('abbrev', 'asc')->get();

		//Get company data for client
		$company = DB::table('company')->where('id', $clientId)->first();

        //Find rates for client
		$rates = ClientRates::whereClient($clientId)->orderBy('state', 'asc')->get();
		
		Return View::make('clientRates.index')->with(['rates' => $rates])->with(['clients' => $clients])->with('states', $states)->with('clientId',$clientId)->with(['company' => $company]);
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
	public function update()
	{
		//Retrieve Data
		$state = Input::get('state');
		$discount = Input::get('discount');
		$filingMax = Input::get('filingMax');
		$filingSurcharge = Input::get('filingSurcharge');
		$filingFlat = Input::get('filingFlat');
		$serveMax = Input::get('serveMax');
		$serveSurcharge = Input::get('serveSurcharge');
		$serveFlat = Input::get('serveFlat');
		$recordingMax = Input::get('recordingMax');
		$recordingSurcharge = Input::get('recordingSurcharge');
		$recordingFlat = Input::get('recordingFlat');

		if(empty($state)){
			//Find rates for client
			$rates = ClientRates::whereClient(Input::get('clientId'))->orderBy('state', 'asc')->get();

			//Update client rates
			foreach ($rates as $rate) {

				$clientRates = ClientRates::whereId($rate->id)->first();

				$clientRates->discount = $discount[$rate->id];
				$clientRates->filingMax = $filingMax[$rate->id];
				$clientRates->filingSurcharge = $filingSurcharge[$rate->id];
				$clientRates->filingFlat = $filingFlat[$rate->id];
				$clientRates->serveMax = $serveMax[$rate->id];
				$clientRates->serveSurcharge = $serveSurcharge[$rate->id];
				$clientRates->serveFlat = $serveFlat[$rate->id];
				$clientRates->recordingMax = $recordingMax[$rate->id];
				$clientRates->recordingSurcharge = $recordingSurcharge[$rate->id];
				$clientRates->recordingFlat = $recordingFlat[$rate->id];
				$clientRates->save();
			}
		}

		if(!empty($state)) {
			//Find states
			$states = DB::table('states')->orderBy('name', 'asc')->get();

			//Save client rates
			foreach ($states as $state) {

				$clientRates = new ClientRates;
				$clientRates->state = $state->abbrev;
				$clientRates->client = Input::get('clientId');
				$clientRates->discount = $discount[$state->abbrev];
				$clientRates->filingMax = $filingMax[$state->abbrev];
				$clientRates->filingSurcharge = $filingSurcharge[$state->abbrev];
				$clientRates->filingFlat = $filingFlat[$state->abbrev];
				$clientRates->serveMax = $serveMax[$state->abbrev];
				$clientRates->serveSurcharge = $serveSurcharge[$state->abbrev];
				$clientRates->serveFlat = $serveFlat[$state->abbrev];
				$clientRates->recordingMax = $recordingMax[$state->abbrev];
				$clientRates->recordingSurcharge = $recordingSurcharge[$state->abbrev];
				$clientRates->recordingFlat = $recordingFlat[$state->abbrev];
				$clientRates->save();
			}
		}

        Return Redirect::route('clientRates.index')->with('clientId', Input::get('clientId'));
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
