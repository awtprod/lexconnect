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
        $run = Input::get('run');
        $service = Input::get('service');
        $post = Input::get('post');
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
        $revAddServee = Input::get('revAddServee');


        //Find all existing rates
        $rates = VendorRates::whereVendor(Auth::user()->company_id)->orderBy('state', 'asc')->get();

        //Update Rates

        foreach($rates as $rate) {
            $revRate = VendorRates::whereId($rate->id)->first();

            if($run[$rate->id] == 'flat'){
                $revRate->runFlat = $revRunFlat[$rate->id];
                $revRate->runBase = '0';
                $revRate->runMileage = '0';
            }
            else{
                $revRate->runFlat = '0';
                $revRate->runBase = $revRunBase[$rate->id];
                $revRate->runMileage = $revRunMileage[$rate->id];
            }
            $revRate->runRush = $revRunRush[$rate->id];
            $revRate->runSameDay = $revRunSameDay[$rate->id];
            if($service[$rate->id] == 'flat'){
                $revRate->serviceFlat = $revServiceFlat[$rate->id];
                $revRate->serviceBase = '0';
                $revRate->serviceMileage = '0';
            }
            else{
                $revRate->serviceFlat = '0';
                $revRate->serviceBase = $revServiceBase[$rate->id];
                $revRate->serviceMileage = $revServiceMileage[$rate->id];
            }
            $revRate->serviceRush = $revServiceRush[$rate->id];
            $revRate->serviceSameDay = $revServiceSameDay[$rate->id];
            if($post[$rate->id] == 'flat'){
                $revRate->postFlat = $revPostFlat[$rate->id];
                $revRate->postBase = '0';
                $revRate->postMileage = '0';
            }
            else{
                $revRate->postFlat = '0';
                $revRate->postBase = $revPostBase[$rate->id];
                $revRate->postMileage = $revPostMileage[$rate->id];
            }
            $revRate->postRush = $revPostRush[$rate->id];
            $revRate->postSameDay = $revPostSameDay[$rate->id];
            $revRate->personal = $revPersonal[$rate->id];
            $revRate->free_pgs = $revFreePgs[$rate->id];
            $revRate->pg_rate = $revPageRate[$rate->id];
            $revRate->add_servee = $revAddServee[$rate->id];
            $revRate->save();
        }

        //Check if a county has been added
        $county = Input::get('county');

        if(!empty($county)) {

        //Check if county is already in db
        $revCounty = VendorRates::whereCounty($county)->whereState(Input::get('state'))->whereVendor(Auth::user()->company_id)->first();

            //If county does not exist, create new rate
            if(empty($revCounty)) {

                $revCounty = new VendorRates;
                $revCounty->vendor = Auth::user()->company_id;
                $revCounty->state = Input::get('state');
                $revCounty->county = Input::get('county');
            }

            //Update values
            if($run[0] == 'flat'){
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
            if($service[0] == 'flat'){
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
            if($post[0] == 'flat'){
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
