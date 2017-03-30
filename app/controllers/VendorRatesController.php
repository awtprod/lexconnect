<?php

class VendorRatesController extends \BaseController {


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        if(Auth::user()->user_role == 'Admin'){


            //Get vendor Id
            $vendorId = Input::get('vendorId');

            //Get list of vendors
            $vendors = DB::table('company')->where('v_c', 'Vendor')->orderBy('v_c', 'asc')->lists('name', 'id');

            //Find states
            $states = DB::table('states')->orderBy('abbrev', 'asc')->lists('abbrev', 'abbrev');

            //Get company data for vendor
            $company = DB::table('company')->where('id', $vendorId)->first();

            //Find rates for client
            $rates = VendorRates::whereVendor($vendorId)->orderBy('state', 'asc')->get();

            Return View::make('vendorRates.index')->with(['rates' => $rates])->with(['vendors' => $vendors])->with('states', $states)->with('vendorId',$vendorId)->with(['company' => $company]);
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
        $input = Input::all();

        //Find all existing rates
        $rates = VendorRates::whereVendor($input["vendor"])->orderBy('state', 'asc')->get();

        if(!empty($rates)) {
            //Update Rates

            foreach ($rates as $rate) {
                $revRate = VendorRates::whereId($rate->id)->first();

                if ($input["run"][$rate->id] == 'flat') {
                    $revRate->runFlat = $input["revRunFlat"][$rate->id];
                    $revRate->runBase = '0';
                    $revRate->runMileage = '0';
                } else {
                    $revRate->runFlat = '0';
                    $revRate->runBase = $input["revRunBase"][$rate->id];
                    $revRate->runMileage = $input["revRunMileage"][$rate->id];
                }
                $revRate->runRush = $input["revRunRush"][$rate->id];
                $revRate->runSameDay = $input["revRunSameDay"][$rate->id];
                if ($input["service"][$rate->id] == 'flat') {
                    $revRate->serviceFlat = $input["revServiceFlat"][$rate->id];
                    $revRate->serviceBase = '0';
                    $revRate->serviceMileage = '0';
                } else {
                    $revRate->serviceFlat = '0';
                    $revRate->serviceBase = $input["revServiceBase"][$rate->id];
                    $revRate->serviceMileage = $input["revServiceMileage"][$rate->id];
                }
                $revRate->serviceRush = $input["revServiceRush"][$rate->id];
                $revRate->serviceSameDay = $input["revServiceSameDay"][$rate->id];
                if ($input["post"][$rate->id] == 'flat') {
                    $revRate->postFlat = $input["revPostFlat"][$rate->id];
                    $revRate->postBase = '0';
                    $revRate->postMileage = '0';
                } else {
                    $revRate->postFlat = '0';
                    $revRate->postBase = $input["revPostBase"][$rate->id];
                    $revRate->postMileage = $input["revPostMileage"][$rate->id];
                }
                $revRate->postRush = $input["revPostRush"][$rate->id];
                $revRate->postSameDay = $input["revPostSameDay"][$rate->id];
                $revRate->personal = $input["revPersonal"][$rate->id];
                $revRate->free_pgs = $input["revFreePgs"][$rate->id];
                $revRate->pg_rate = $input["revPageRate"][$rate->id];
                $revRate->add_servee = $input["revAddServee"][$rate->id];
                $revRate->save();
            }
        }
        //Check if a county has been added
        $county = Input::get('county');

        if(!empty($county)) {

        //Check if county is already in db
        $revCounty = VendorRates::whereCounty($county)->whereState(Input::get('state'))->whereVendor($input["vendor"])->first();

            //If county does not exist, create new rate
            if(empty($revCounty)) {

                $revCounty = new VendorRates;
                $revCounty->vendor = $input["vendor"];
                $revCounty->state = Input::get('state');
                $revCounty->county = Input::get('county');
            }

            //Update values
            if($input["run"][0] == 'flat'){
                $revCounty->runFlat = Input::get('runFlat');
                $revCounty->runBase = '0';
                $revCounty->runMileage = '0';
                }
            else{
                $revCounty->runFlat = '0';
                $revCounty->runBase = Input::get('runBase');
                $revCounty->runMileage = Input::get('runMileage');
            }
                $revCounty->runRush = Input::get('runRush');
                $revCounty->runSameDay = Input::get('runSameDay');
            if($input["service"][0] == 'flat'){
                $revCounty->serviceFlat = Input::get('serviceFlat');
                $revCounty->serviceBase = '0';
                $revCounty->serviceMileage = '0';
            }
            else{
                $revCounty->serviceFlat = '0';
                $revCounty->serviceBase = Input::get('serviceBase');
                $revCounty->serviceMileage = Input::get('serviceMileage');
            }
                $revCounty->serviceRush = Input::get('serviceRush');
                $revCounty->serviceSameDay = Input::get('serviceSameDay');
            if($input["post"][0] == 'flat'){
                $revCounty->postFlat = Input::get('postFlat');
                $revCounty->postBase = '0';
                $revCounty->postMileage = '0';
            }
            else{
                $revCounty->postFlat = '0';
                $revCounty->postBase = Input::get('postBase');
                $revCounty->postMileage = Input::get('postMileage');
            }
                $revCounty->postRush = Input::get('postRush');
                $revCounty->postSameDay = Input::get('postSameDay');
                $revCounty->personal = Input::get('personal');
                $revCounty->free_pgs = Input::get('free_pgs');
                $revCounty->pg_rate = Input::get('pg_rate');
                $revCounty->add_servee = Input::get('add_servee');
                $revCounty->save();


        }

        Return Redirect::Route('vendorrates.index', ['vendorId' => $input["vendor"]]);
	}

    public function show($id)
    {
        if(Auth::user()->user_role == 'Admin'){

            $rates = VendorRates::whereVendor($id)->orderBy('state', 'asc')->get();

            $states = DB::table('states')->orderBy('abbrev', 'asc')->lists('abbrev', 'abbrev');

            Return View::make('vendorRates.index')->with(['rates' => $rates])->with(['states'=>$states]);
        }
        else{
            Return "Not Authorized!";
        }
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        //Find vendor associated with rate
        $vendor = VendorRates::whereId($id)->pluck('vendor');

        //Check if user is vendor associated with rate
        if(Auth::user()->role == 'Supervisor' AND Auth::user()->company_id == $vendor) {

            VendorRates::destroy($id);
        }

        Return Redirect::back();
	}


}
