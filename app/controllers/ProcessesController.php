<?php

class ProcessesController extends \BaseController {
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Admin'){
		$processes = DB::table('processes')->orderBy('id', 'asc')->get();

		$states = DB::table('states')->orderBy('name', 'asc')->lists('abbrev', 'abbrev');


		Return View::make('processes.index')->with(['processes' => $processes], ['states' => $states]);
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
	public function create()
	{
		if(Auth::user()->user_role == 'Admin'){

		$states = ['' => ''] + DB::table('states')->orderBy('name', 'asc')->lists('abbrev', 'abbrev');

			Return View::make('processes.create')->with(['states' => $states]);
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

        //Create new process

        $processes = new Processes;
        $processes->name = Input::get('name');
		$processes->service = Input::get('service');
        $processes->save();
		
		Return Redirect::Route('processes.index');
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
	public function edit($id)
	{
        if(Auth::user()->user_role == 'Admin'){

            //Find process data in table
            $process = Processes::whereId($id)->first();


			Return View::make('processes.edit')->with(['process' => $process]);

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
		//Find process
        $process = Processes::whereId(Input::get('processId'))->first();

        //Save new data
        $process->name = Input::get('name');
		$process->service = Input::get('service');
        $process->save();

        Return Redirect::route('processes.index');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{

		$templates = Template::whereProcess($id)->get();

		foreach($templates as $template){

			$delete = Template::whereId($template->id)->first();
			$delete->delete();
		}

		Processes::destroy($id);

		Return Redirect::route('processes.index');

	}


}
