<?php

class StatesController extends \BaseController {
    
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Admin'){


            $states = DB::table('states')->orderBy('name', 'asc')->get();


            Return View::make('states.index')->with(['states'=>$states]);
		}
		else{
		Return "Not Authorized!";
		}
	}

    public function load(){

        $input = Input::all();

        $state = States::whereId($input["id"])->first();


			if(File::exists(app_path('/views/states/'.$state->name.'_'.$input["type"].'.blade.php'))) {

				return Response::json(array('body'=> File::get(app_path('/views/states/'.$state->name.'_'.$input["type"].'.blade.php')), 'title'=>$state->name.' '.ucwords($input["type"]).' Template'));
			}
			else{

				return Response::json(array('body'=> '', 'title'=>$state->name.' '.ucwords($input["type"]).' Template'));

			}

    }
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function save()
	{

		$input = Input::all();
		dd($input);

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        States::destroy($id);

		$states = DB::table('states')->orderBy('name', 'asc')->lists('name', 'abbrev');

        Return Redirect::Route('states.index')->with('state', $states);
	}


}
