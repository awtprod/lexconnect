<?php

class LocationsController extends \BaseController {

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
                    "Address" => $input["location"][$location->id]["street"],
                    "City" => $input["location"][$location->id]["city"],
                    "Region" => $input["location"][$location->id]["state"],
                    "PostalCode" => $input["location"][$location->id]["zipcode"],
                    "Country" => "US",
                    "Category" => "Vendors",
                    "UserData" => Auth::user()->company_id
                );

                //Post data to GeoSvc
                $error = $this->locations->postLocation(array(['address_string' => json_encode($address), 'address' => $address]));

                if(!empty($error)){

                    Session::flash('message', 'Error: '.$error);

                    return Redirect::back()->withInput();

                }
                else {


                    Session::flash('message', 'Location Updated!');
                }
            }
        }

        //Create new location
        if(!empty($input["location"][0]["street"])){

            $address = array (
                    "Type" => "UserDefined",
                   "Name" => $input["location"][0]["name"],
                   "Address" => $input["location"][0]["street"],
                   "City" => $input["location"][0]["city"],
                   "Region" => $input["location"][0]["state"],
                   "PostalCode" => $input["location"][0]["zipcode"],
                   "Country" => "US",
                   "Category" => "Vendors",
                   "UserData" => Auth::user()->company_id
            );


            //Post data to GeoSvc
            if(!empty($error = $this->locations->postLocation(array(['address_string' => json_encode($address), 'address' => $address])))){


                Session::flash('message', 'Error: '.$error);

                return Redirect::back()->withInput();

            }
            else {

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

         //Delete from db
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
