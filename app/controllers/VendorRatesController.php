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
        $revFilingFlat = Input::get('revFilingFlat');
        $revFilingBase = Input::get('revFilingBase');
        $revFilingMileage = Input::get('revFilingMileage');
        $revServiceFlat = Input::get('revServiceFlat');
        $revServiceBase = Input::get('revServiceBase');
        $revServiceMileage = Input::get('revServiceMileage');
        $revRecordingFlat = Input::get('revRecordingFlat');
        $revRecordingBase = Input::get('revRecordingBase');
        $revRecordingMileage = Input::get('revRecordingMileage');

        //Find all existing steps
        $rates = VendorRates::whereVendor(Auth::user()->company_id)->orderBy('state', 'asc')->get();

        //Update Steps

        foreach($rates as $rate) {
            $revRate = VendorRates::whereId($rate->id)->first();
            $revRate->filingFlat = $revFilingFlat[$rate->id];
            $revRate->filingBase = $revFilingBase[$rate->id];
            $revRate->filingMileage = $revFilingMileage[$rate->id];
            $revRate->serviceFlat = $revServiceFlat[$rate->id];
            $revRate->serviceBase = $revServiceBase[$rate->id];
            $revRate->serviceMileage = $revServiceMileage[$rate->id];
            $revRate->recordingFlat = $revRecordingFlat[$rate->id];
            $revRate->recordingBase = $revRecordingBase[$rate->id];
            $revRate->recordingMileage = $revRecordingMileage[$rate->id];
            $revRate->save();
        }

        //Check if a county has been added
        $county = Input::get('county');

        if(!empty($county)) {

        //Check if county is already in db
        $revCounty = VendorRates::whereCounty($county)->whereState(Input::get('state'))->whereVendor(Auth::user()->company_id)->first();

            //Update old rate
            if(!empty($revCounty)){

                $revCounty->filingFlat = Input::get('filingFlat');
                $revCounty->filingBase = Input::get('filingBase');
                $revCounty->filingMileage = Input::get('filingMileage');
                $revCounty->serviceFlat = Input::get('serviceFlat');
                $revCounty->serviceBase = Input::get('serviceBase');
                $revCounty->serviceMileage = Input::get('serviceMileage');
                $revCounty->recordingFlat = Input::get('recordingFlat');
                $revCounty->recordingBase = Input::get('recordingBase');
                $revCounty->recordingMileage = Input::get('recordingMileage');
                $revCounty->save();

            }
            //Otherwise, create new rate
            else {
                $rate = new VendorRates;
                $rate->vendor = Auth::user()->company_id;
                $rate->state = Input::get('state');
                $rate->county = Input::get('county');
                $rate->filingFlat = Input::get('filingFlat');
                $rate->filingBase = Input::get('filingBase');
                $rate->filingMileage = Input::get('filingMileage');
                $rate->serviceFlat = Input::get('serviceFlat');
                $rate->serviceBase = Input::get('serviceBase');
                $rate->serviceMileage = Input::get('serviceMileage');
                $rate->recordingFlat = Input::get('recordingFlat');
                $rate->recordingBase = Input::get('recordingBase');
                $rate->recordingMileage = Input::get('recordingMileage');
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
