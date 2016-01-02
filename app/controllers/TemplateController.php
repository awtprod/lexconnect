<?php

class TemplateController extends \BaseController {

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

            $processes = Processes::all();

            Return View::make('template.index')->with(['processes' => $processes]);
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
        $addStep = Input::get('addStep');
        $status = Input::get('status');
        $sortOrder = Input::get('sortOrder');
        $name = Input::get('name');
        $group = Input::get('group');
        $RoutineOrigDueDate = Input::get('RoutineOrigDueDate');
        $RoutineNewDueDate = Input::get('RoutineNewDueDate');
        $RushOrigDueDate = Input::get('RushOrigDueDate');
        $RushNewDueDate = Input::get('RushNewDueDate');
        $SameDayOrigDueDate = Input::get('SameDayOrigDueDate');
        $SameDayNewDueDate = Input::get('SameDayNewDueDate');
        $window = Input::get('window');

        //Find all existing steps
        $steps = Steps::all();

        foreach($steps as $step) {

            //Add new steps to template

            if (!empty($addStep[$step->id])) {
                $template = new Template;
                $template->process = Input::get('process');
                $template->step = $stepId[$step->id];
                $template->name = $name[$step->id];
                $template->status = $status[$step->id];
                $template->sort_order = $sortOrder[$step->id];
                $template->group = $group[$step->id];
                $template->window = $window[$step->id];
                $template->RoutineOrigDueDate = $RoutineOrigDueDate[$step->id];
                $template->RoutineNewDueDate = $RoutineNewDueDate[$step->id];
                $template->RushOrigDueDate = $RushOrigDueDate[$step->id];
                $template->RushNewDueDate = $RushNewDueDate[$step->id];
                $template->SameDayOrigDueDate = $SameDayOrigDueDate[$step->id];
                $template->SameDayNewDueDate = $SameDayNewDueDate[$step->id];
                $template->save();
            }

            //Save changes to steps

            $revStep = Steps::whereId($step->id)->first();
            $revStep->name = $name[$step->id];
            $revStep->status = $status[$step->id];
            $revStep->sort_order = $sortOrder[$step->id];
            $revStep->group = $group[$step->id];
            $revStep->window = $window[$step->id];
            $revStep->RoutineOrigDueDate = $RoutineOrigDueDate[$step->id];
            $revStep->RoutineNewDueDate = $RoutineNewDueDate[$step->id];
            $revStep->RushOrigDueDate = $RushOrigDueDate[$step->id];
            $revStep->RushNewDueDate = $RushNewDueDate[$step->id];
            $revStep->SameDayOrigDueDate = $SameDayOrigDueDate[$step->id];
            $revStep->SameDayNewDueDate = $SameDayNewDueDate[$step->id];
            $revStep->save();
        }

        Return Redirect::route('template.edit', Input::get('process'));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function add()
	{
        $template = new Template;
        $template->process = Input::get('process');
        $template->name = Input::get('name');
        $template->status = Input::get('status');
        $template->sort_order = Input::get('sortOrder');
        $template->group = Input::get('group');
        $template->window = Input::get('window');
        $template->RoutineOrigDueDate = Input::get('RoutineOrigDueDate');
        $template->RoutineNewDueDate = Input::get('RoutineNewDueDate');
        $template->RushOrigDueDate = Input::get('RushOrigDueDate');
        $template->RushNewDueDate = Input::get('RushNewDueDate');
        $template->SameDayOrigDueDate = Input::get('SameDayOrigDueDate');
        $template->SameDayNewDueDate = Input::get('SameDayNewDueDate');
        $template->save();

        $step = new Steps;
        $step->name = Input::get('name');
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

        Return Redirect::route('template.edit', Input::get('process'));

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
            $steps = Steps::orderBy('sort_order', 'asc')->get();

            //Retrieve data about process
            $process = Processes::whereId($id)->first();

            //Find template data for process
            $templates = Template::whereProcess($id)->orderBy('sort_order', 'asc')->get();

            Return View::make('template.edit')->with(['steps' => $steps ])->with(['templates' => $templates])->with('process', $process);

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
        $status = Input::get('status');
        $sortOrder = Input::get('sortOrder');
        $name = Input::get('name');
        $group = Input::get('group');
        $RoutineOrigDueDate = Input::get('RoutineOrigDueDate');
        $RoutineNewDueDate = Input::get('RoutineNewDueDate');
        $RushOrigDueDate = Input::get('RushOrigDueDate');
        $RushNewDueDate = Input::get('RushNewDueDate');
        $SameDayOrigDueDate = Input::get('SameDayOrigDueDate');
        $SameDayNewDueDate = Input::get('SameDayNewDueDate');
        $window = Input::get('window');

        //Find steps data in table
        $templates = Template::whereProcess(Input::get('process'))->get();

        //Save new data
        foreach($templates as $template) {
            $template = Template::whereId($template->id)->first();
            $template->name = $name[$template->id];
            $template->sort_order = $sortOrder[$template->id];
            $template->status = $status[$template->id];
            $template->group = $group[$template->id];
            $template->RoutineOrigDueDate = $RoutineOrigDueDate[$template->id];
            $template->RoutineNewDueDate = $RoutineNewDueDate[$template->id];
            $template->RushOrigDueDate = $RushOrigDueDate[$template->id];
            $template->RushNewDueDate = $RushNewDueDate[$template->id];
            $template->SameDayOrigDueDate = $SameDayOrigDueDate[$template->id];
            $template->SameDayNewDueDate = $SameDayNewDueDate[$template->id];
            $template->window = $window[$template->id];
            $template->save();
        }
        Return Redirect::route('template.edit', Input::get('process'));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        Template::destroy($id);

        Return Redirect::back();
	}


}
