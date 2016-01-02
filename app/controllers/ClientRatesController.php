<?php

class ClientRatesController extends \BaseController {

    public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, ClientRates $ClientRates, VendorRates $VendorRates)
    {

        $this->orders = $orders;
        $this->tasks = $tasks;
        $this->reprojections = $reprojections;
        $this->jobs = $jobs;
        $this->invoices = $invoices;
        $this->DocumentsServed = $DocumentsServed;
        $this->ClientRates = $ClientRates;
        $this->VendorRates = $VendorRates;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Admin'){


        //Get list of clients
        $clients = DB::table('company')->where('v_c', 'Client')->orderBy('v_c', 'asc')->lists('name', 'id');

        //Get client Id
        $clientId = Input::get('clientId');

            if(empty($clientId)){

                $input = Input::all();

                if(!empty($input["clientId"])) {

                    $clientId = $input["clientId"];
                }
            }

        //Get company data for client
        $company = DB::table('company')->where('id', $clientId)->first();

        //Find rates for client
		$rates = ClientRates::whereClient($clientId)->orderBy('state', 'asc')->get();
		
		Return View::make('clientRates.index')->with(['rates' => $rates])->with(['clients' => $clients])->with('company', $company)->with('clientId',$clientId);
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
	public function create($clientId)
	{
		if(Auth::user()->user_role == 'Admin'){
		$states = DB::table('states')->orderBy('name', 'asc')->get();

        //Find client information
        $client = DB::table('company')->where('id', $clientId)->first();
		
		Return View::make('clientRates.create')->with(['states' => $states])->with('client', $client);
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
		$filingSurcharge = Input::get('filingSurcharge');
		$filingFlat = Input::get('filingFlat');
		$serveSurcharge = Input::get('serveSurcharge');
		$serveFlat = Input::get('serveFlat');
		
		//Find states
		$states = DB::table('states')->orderBy('name', 'asc')->get();
		
		//Save client rates
		foreach($states as $state){

        $clientRates = new ClientRates;

        $clientRates->state = $state->abbrev;

        $clientRates->client = Input::get('clientId');

		if(!empty($filingSurcharge[$state->abbrev])){

		$clientRates->filingSurcharge = $filingSurcharge[$state->abbrev];

		}

         if(!empty($filingFlat[$state->abbrev])){

         $clientRates->filingFlat = $filingFlat[$state->abbrev];

         }

         if(!empty($serveSurcharge[$state->abbrev])){

         $clientRates->serveSurcharge = $serveSurcharge[$state->abbrev];

         }

         if(!empty($serveFlat[$state->abbrev])){

         $clientRates->serveFlat = $serveFlat[$state->abbrev];

         }

         $clientRates->save();
		}

        Return Redirect::route('clientRates.index', Input::get('clientId'));
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
	public function edit($clientId)
	{
        if(Auth::user()->user_role == 'Admin'){

            //Find client rates
            $rates = ClientRates::whereClient($clientId)->get();

            //Find client information
            $client = DB::table('company')->where('id', $clientId)->first();

            Return View::make('clientRates.edit')->with(['rates' => $rates])->with('clientId', $clientId)->with(['client' => $client]);
        }
        else{
            Return "Not Authorized!";
        }

	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
        //Retrieve Data
        $state = Input::get('state');
        $filingSurcharge = Input::get('filingSurcharge');
        $filingFlat = Input::get('filingFlat');
        $serveSurcharge = Input::get('serveSurcharge');
        $serveFlat = Input::get('serveFlat');


        //Find client rates
        $rates = ClientRates::whereClient(Input::get('clientId'))->get();

        //Enter new rates
        foreach($rates as $rate){
            $clientRate = ClientRates::whereId($rate->id)->first();

            $clientRate->state = $state[$rate->id];

            if(!empty($filingSurcharge[$rate->id])){

                $clientRate->filingSurcharge = $filingSurcharge[$rate->id];

            }

            if(!empty($filingFlat[$rate->id])){

                $clientRate->filingFlat = $filingFlat[$rate->id];

            }

            if(!empty($serveSurcharge[$rate->id])){

                $clientRate->serveSurcharge = $serveSurcharge[$rate->id];

            }

            if(!empty($serveFlat[$rate->id])){

                $clientRate->serveFlat = $serveFlat[$rate->id];

            }

            $clientRate->save();
        }

        Return Redirect::route('clientRates.index', Input::get('clientId'));
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
