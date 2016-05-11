<?php

class VendorRatesController extends \BaseController {

    public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, Processes $processes, Steps $steps, Template $template, VendorRates $vendorRates)
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
        $this->VendorRates = $vendorRates;
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Vendor' AND Auth::user()->role == 'Supervisor'){

            $rates = VendorRates::whereVendor(Auth::user()->company_id)->orderBy('state', 'asc')->get();

            $states = DB::table('states')->orderBy('abbrev', 'asc')->lists('abbrev', 'abbrev');

            Return View::make('vendorRates.index')->with(['rates' => $rates])->with(['states'=>$states]);
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

        //Get revised variables
        $rateId = Input::get('rateId');
        $revRunFlat = Input::get('revRunFlat');
        $revRunBase = Input::get('revRunBase');
        $revRunMileage = Input::get('revRunMileage');
        $revRunRush = Input::get('revRunRush');
        $revRunSameDay = Input::get('revRunSameDay');
        $revServiceFlat = Input::get('revServiceFlat');
        $revServiceBase = Input::get('revServiceBase');
        $revServiceMileage = Input::get('revServiceMileage');
        $revServiceRush = Input::get('revServiceRush');
        $revServiceSameDay = Input::get('revServiceSameDay');
        $revPostFlat = Input::get('revPostFlat');
        $revPostBase = Input::get('revPostBase');
        $revPostMileage = Input::get('revPostMileage');
        $revPostRush = Input::get('revPostRush');
        $revPostSameDay = Input::get('revSameDayMileage');
        $revFreePgs = Input::get('revFreePgs');
        $revPageRate = Input::get('revPageRate');
        $revPersonal = Input::get('revPersonal');


        //Find all existing rates
        $rates = VendorRates::whereVendor(Auth::user()->company_id)->orderBy('state', 'asc')->get();

        //Update Rates

        foreach($rates as $rate) {
            $revRate = VendorRates::whereId($rate->id)->first();
            $revRate->runFlat = $revRunFlat[$rate->id];
            $revRate->runBase = $revRunBase[$rate->id];
            $revRate->runMileage = $revRunMileage[$rate->id];
            $revRate->runRush = $revRunRush[$rate->id];
            $revRate->runSameDay = $revRunSameDay[$rate->id];
            $revRate->serviceFlat = $revServiceFlat[$rate->id];
            $revRate->serviceBase = $revServiceBase[$rate->id];
            $revRate->serviceMileage = $revServiceMileage[$rate->id];
            $revRate->serviceRush = $revServiceRush[$rate->id];
            $revRate->postFlat = $revPostFlat[$rate->id];
            $revRate->postBase = $revPostBase[$rate->id];
            $revRate->postMileage = $revPostMileage[$rate->id];
            $revRate->postRush = $revPostRush[$rate->id];
            $revRate->postSameDay = $revPostSameDay[$rate->id];
            $revRate->personal = $revPersonal[$rate->id];
            $revRate->free_pgs = $revFreePgs[$rate->id];
            $revRate->pg_rate = $revPageRate[$rate->id];
            $revRate->save();
        }

        //Check if a county has been added
        $county = Input::get('county');

        if(!empty($county)) {

        //Check if county is already in db
        $revCounty = VendorRates::whereCounty($county)->whereState(Input::get('state'))->whereVendor(Auth::user()->company_id)->first();

            //Update old rate
            if(!empty($revCounty)){

                $revCounty->runFlat = Input::get('runFlat');
                $revCounty->runBase = Input::get('runBase');
                $revCounty->runMileage = Input::get('runMileage');
                $revCounty->runRush = Input::get('runRush');
                $revCounty->runSameDay = Input::get('runSameDay');
                $revCounty->serviceFlat = Input::get('serviceFlat');
                $revCounty->serviceBase = Input::get('serviceBase');
                $revCounty->serviceMileage = Input::get('serviceMileage');
                $revCounty->postFlat = Input::get('postFlat');
                $revCounty->postBase = Input::get('postBase');
                $revCounty->postMileage = Input::get('postMileage');
                $revCounty->postRush = Input::get('postRush');
                $revCounty->postSameDay = Input::get('postSameDay');
                $revCounty->personal = Input::get('personal');
                $revCounty->free_pgs = Input::get('free_pgs');
                $revCounty->pg_rate = Input::get('pg_rate');
                $revCounty->save();

            }
            //Otherwise, create new rate
            else {
                $rate = new VendorRates;
                $rate->vendor = Auth::user()->company_id;
                $rate->state = Input::get('state');
                $rate->county = Input::get('county');
                $rate->runFlat = Input::get('runFlat');
                $rate->runBase = Input::get('runBase');
                $rate->runMileage = Input::get('runMileage');
                $rate->runRush = Input::get('runRush');
                $rate->runSameDay = Input::get('runSameDay');
                $rate->serviceFlat = Input::get('serviceFlat');
                $rate->serviceBase = Input::get('serviceBase');
                $rate->serviceMileage = Input::get('serviceMileage');
                $rate->serviceRush = Input::get('serviceRush');
                $rate->serviceSameDay = Input::get('serviceSameDay');
                $rate->postFlat = Input::get('postFlat');
                $rate->postBase = Input::get('postBase');
                $rate->postMileage = Input::get('postMileage');
                $rate->postRush = Input::get('postRush');
                $rate->postSameDay = Input::get('postSameDay');
                $rate->personal = Input::get('personal');
                $rate->free_pgs = Input::get('free_pgs');
                $rate->pg_rate = Input::get('pg_rate');
                $rate->save();
            }
        }

        Return Redirect::Route('vendorrates.index');
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
