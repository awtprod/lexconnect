<?php

class StepsController extends \BaseController {

    public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, Processes $processes, Steps $steps, Template $template)
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
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::user()->user_role == 'Admin'){

            $steps = Steps::all();

            Return View::make('steps.index')->with(['steps' => $steps]);
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

            //Find all steps
            $steps = Steps::all();


		Return View::make('steps.create')->with(['steps' => $steps]);
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
        $stepId = Input::get('stepId');
        $revName = Input::get('revName');
        $revJud = Input::get('revJud');
        $revStatus = Input::get('revStatus');
        $revSortOrder = Input::get('revSortOrder');
        $revGroup = Input::get('revGroup');
        $revWindow = Input::get('revWindow');
        $revRoutineOrigDueDate = Input::get('revRoutineOrigDueDate');
        $revRoutineNewDueDate = Input::get('revRoutineNewDueDate');
        $revRushOrigDueDate = Input::get('revRushOrigDueDate');
        $revRushNewDueDate = Input::get('revRushNewDueDate');
        $revSameDayOrigDueDate = Input::get('revSameDayOrigDueDate');
        $revSameDayNewDueDate = Input::get('revSameDayNewDueDate');

        //Find all existing steps
        $steps = Steps::all();

        //Update Steps

        foreach($steps as $step) {
            $revStep = Steps::whereId($step->id)->first();
            $revStep->name = $revName[$step->id];
            $revStep->judicial = $revJud[$step->id];
            $revStep->status = $revStatus[$step->id];
            $revStep->sort_order = $revSortOrder[$step->id];
            $revStep->group = $revGroup[$step->id];
            $revStep->window = $revWindow[$step->id];
            $revStep->RoutineOrigDueDate = $revRoutineOrigDueDate[$step->id];
            $revStep->RoutineNewDueDate = $revRoutineNewDueDate[$step->id];
            $revStep->RushOrigDueDate = $revRushOrigDueDate[$step->id];
            $revStep->RushNewDueDate = $revRushNewDueDate[$step->id];
            $revStep->SameDayOrigDueDate = $revSameDayOrigDueDate[$step->id];
            $revStep->SameDayNewDueDate = $revSameDayNewDueDate[$step->id];
            $revStep->save();
        }

        //Add new step
        $name = Input::get('name');

        if(!empty($name)) {
            $step = new Steps;
            $step->name = Input::get('name');
            $step->judicial = Input::get('jud');
            $step->status = Input::get('status');
            $step->sort_order = Input::get('sortOrder');
            $step->group = Input::get('group');
            $step->window = Input::get('window');
            $step->RoutineOrigDueDate = Input::get('RoutineOrigDueDate');
            $step->RoutineNewDueDate = Input::get('RoutineNewDueDate');
            $step->RushOrigDueDate = Input::get('RushOrigDueDate');
            $step->RushNewDueDate = Input::get('RushNewDueDate');
            $step->SameDayOrigDueDate = Input::get('SameDayOrigDueDate');
            $step->SameDayNewDueDate = Input::get('SameDayNewDueDate');
            $step->save();
        }



		Return Redirect::Route('steps.create');
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

            //Find steps data in table
            $steps = Steps::whereProcesses($id)->get();

            //Retrieve data about process
            $process = Processes::whereId($id)->first();

            Return View::make('steps.edit')->with(['steps' => $steps ])->with(['process' => $process]);

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
	public function update($id)
	{
        //Retrieve Data
        $status = Input::get('status');
        $sortOrder = Input::get('sortOrder');
        $name = Input::get('name');
        $jud = Input::get('jud');
        $group = Input::get('group');
        $RoutineOrigDueDate = Input::get('RoutineOrigDueDate');
        $RoutineNewDueDate = Input::get('RoutineNewDueDate');
        $RushOrigDueDate = Input::get('RushOrigDueDate');
        $RushNewDueDate = Input::get('RushNewDueDate');
        $SameDayOrigDueDate = Input::get('SameDayOrigDueDate');
        $SameDayNewDueDate = Input::get('SameDayNewDueDate');
        $window = Input::get('window');

        //Find steps data in table
        $steps = Steps::whereProcesses(Input::get('process'))->get();

        //Save new data
        foreach($steps as $step) {
            $step = Steps::whereId($step->id)->first();
            $step->name = $name[$step->id];
            $step->judicial = $jud[$step->id];
            $step->sortOrder = $sortOrder[$step->id];
            $step->status = $status[$step->id];
            $step->group = $group[$step->id];
            $step->RoutineOrigDueDate = $RoutineOrigDueDate[$step->id];
            $step->RoutineNewDueDate = $RoutineNewDueDate[$step->id];
            $step->RushOrigDueDate = $RushOrigDueDate[$step->id];
            $step->RushNewDueDate = $RushNewDueDate[$step->id];
            $step->SameDayOrigDueDate = $SameDayOrigDueDate[$step->id];
            $step->SameDayNewDueDate = $SameDayNewDueDate[$step->id];
            $step->window = $window[$step->id];
            $step->save();
        }
        Return Redirect::route('steps.create', Input::get('process'));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        Steps::destroy($id);

        Return Redirect::Route('steps.create', Input::get('process'));
	}


}
