<?php

class CountiesController extends \BaseController {
    
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Admin'){

            $counties = array();

            $state = DB::table('states')->where('abbrev', Session::get('state'))->first();

            if(empty($state)){
                $state= DB::table('states')->where('abbrev', Input::get('state'))->first();
            }

            $states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'abbrev');

            //Retrieve counties from form submit
            if(!empty($state)) {
                $counties = Counties::whereState($state->abbrev)->orderBy('county', 'asc')->get();
            }

            //If state data submitted find process data
            if(!empty($counties)){

                View::share('state', $state->abbrev);

                //Find process options
                $filing = Processes::whereService('Filing')->orderBy('id','asc')->lists('name', 'name');
                $service = Processes::whereService('Service')->orderBy('id','asc')->lists('name', 'name');
                $recording = Processes::whereService('Recording')->orderBy('id','asc')->lists('name', 'name');

                //Send arrays to View
                View::share(['filing'=>$filing]);
                View::share(['service'=>$service]);
                View::share(['recording'=>$recording]);

                //Find filing data for each county

                $filingDefault = array();
                $serviceDefault = array();
                $recordingDefault = array();

            foreach($counties as $county){

            $filingDefault[$county->id] = Processes::whereName($county->filing)->first();

                //if none is assigned, find first process

                if(empty($filingDefault[$county->id])){

                    $filingDefault[$county->id] = Processes::whereService('Filing')->orderBy('id', 'asc')->first();
                }

            $serviceDefault[$county->id] = Processes::whereName($county->service)->first();

                //if none is assigned, find first process

                if(empty($serviceDefault[$county->id])){

                    $serviceDefault[$county->id] = Processes::whereService('Service')->orderBy('id', 'asc')->first();
                }
            $recordingDefault[$county->id] = Processes::whereName($county->recording)->first();

                //if none is assigned, find first process

                if(empty($recordingDefault[$county->id])){

                    $recordingDefault[$county->id] = Processes::whereService('Recording')->orderBy('id', 'asc')->first();
                }

            }
                //Send defaults to View
                View::share(['filingDefault'=>$filingDefault]);
                View::share(['serviceDefault'=>$serviceDefault]);
                View::share(['recordingDefault'=>$recordingDefault]);

            }

            Return View::make('counties.index')->with(['states'=>$states])->with(['counties' => $counties]);
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
        $filing = Input::get('filing');
        $service = Input::get('service');
        $recording = Input::get('recording');

        //Find steps data in table
        $counties = Counties::whereState(Input::get('state'))->orderBy('id','asc')->get();

        //Save new data
        foreach($counties as $county) {

            $revCounty = Counties::whereId($county->id)->first();
            $revCounty->filing = $filing[$county->id];
            $revCounty->service = $service[$county->id];
            $revCounty->recording = $recording[$county->id];
            $revCounty->save();
        }
        Return Redirect::Route('counties.index')->with('state', Input::get('state'));
	}

    public function getCounties()
    {
        $input = Input::get('option');
        $counties = Counties::whereState($input)->get();

        $numbers = Counties::whereState($input)->orderBy('county', 'asc')->lists('county','county');

        return Response::json($numbers);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        Counties::destroy($id);

        Return Redirect::Route('counties.index')->with('state', Input::get('state'));
	}


}
