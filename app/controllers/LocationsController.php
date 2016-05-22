<?php

class LocationsController extends \BaseController {

    public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, Processes $processes, Steps $steps, Template $template, VendorRates $vendorRates, Locations $locations)
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
        $this->locations = $locations;

    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Vendor' AND Auth::user()->role == 'Supervisor'){

            $locations = Locations::whereCompanyId(Auth::user()->company_id)->orderBy('id', 'asc')->get();

            $states = DB::table('states')->orderBy('abbrev', 'asc')->lists('abbrev', 'abbrev');

            Return View::make('locations.index')->with(['locations' => $locations])->with(['states'=>$states]);
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

        //Get input variables
/*
        $input = Input::all();

        //Validate data

        foreach ($input["location"] as $input) {

            if (!$this->locations->fill($input)->isValid()) {

                return Redirect::back()->withInput()->withErrors($this->locations->errors);
            }
        }
*/
        $input = Input::all();

        //Find all existing locations
        $locations = Locations::whereCompanyId(Auth::user()->company_id)->orderBy('id', 'asc')->get();

        //If other locations exist, update with new data
        if(!empty($locations))
        {
            //loop through locations
            foreach ($locations as $location)
            {

                $address = array (
                    "Id" => $location->geo_id,
                    "Type" => "UserDefined",
                    "Name" => $input["location"][$location->id]["name"],
                    "Address" => $input["location"][$location->id]["street"].",".$input["location"][$location->id]["street2"],
                    "City" => $input["location"][$location->id]["city"],
                    "Region" => $input["location"][$location->id]["state"],
                    "PostalCode" => $input["location"][$location->id]["zipcode"],
                    "Country" => "US",
                    "Category" => "Vendors",
                    "UserData" => Auth::user()->company_id
                );

                // json encode data

                $address_string = json_encode($address);

                //Post data to GeoSvc
                $this->locations->postLocation($address_string);

                //update db
                $update = Locations::whereId($location->id)->first();
                $update->name = $input["location"][$location->id]["name"];
                $update->street = $input["location"][$location->id]["street"];
                $update->street2 = $input["location"][$location->id]["street2"];
                $update->city = $input["location"][$location->id]["city"];
                $update->state = $input["location"][$location->id]["state"];
                $update->zipcode = $input["location"][$location->id]["zipcode"];
                $update->save();

                Session::flash('message', 'Location Updated!');
            }
        }

        //Create new location
        if(!empty($input["location"][0]["street"])){

            $address = array (
                    "Type" => "UserDefined",
                   "Name" => $input["location"][0]["name"],
                   "Address" => $input["location"][0]["street"].",".$input["location"][0]["street2"],
                   "City" => $input["location"][0]["city"],
                   "Region" => $input["location"][0]["state"],
                   "PostalCode" => $input["location"][0]["zipcode"],
                   "Country" => "US",
                   "Category" => "Vendors",
                   "UserData" => Auth::user()->company_id
            );

            // json encode data

            $address_string = json_encode($address);

            //Post data to GeoSvc
            $result = $this->locations->postLocation($address_string);

            $geoId= (array) simplexml_load_string($result);

            //If geoId is empty, location was not created
            if(empty($geoId["Id"])){

                Session::flash('message', 'Error: Location Was Not Created! Please Try Again.');

                return Redirect::back()->withInput();

            }
            else {
                //Save location to db
                $location = new Locations;
                $location->geo_id = $geoId["Id"];
                $location->company_id = Auth::user()->company_id;
                $location->name = $input["location"][0]["name"];
                $location->street = $input["location"][0]["street"];
                $location->street2 = $input["location"][0]["street2"];
                $location->city = $input["location"][0]["city"];
                $location->state = $input["location"][0]["state"];
                $location->zipcode = $input["location"][0]["zipcode"];
                $location->save();

                Session::flash('message', 'Location Created!');
            }
        }



        Return Redirect::Route('locations.index');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        //Find location associated with id
        $location = Locations::whereId($id)->first();

        //Check if user is vendor associated with locate
        if(Auth::user()->role == 'Supervisor' AND Auth::user()->company_id == $location->company_id) {

            //try to delete location from geosvc
            if($this->locations->deleteLocation($location->geo_id)) {

                Locations::destroy($id);

                Session::flash('message', 'Location Deleted!');

            }
            else{

                Session::flash('message', 'Error: Failed to Delete Location!');

            }
        }

        Return Redirect::back();
	}


}
