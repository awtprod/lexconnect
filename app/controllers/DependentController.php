<?php

class DependentController extends \BaseController {

    public function __construct (Orders $orders, Tasks $tasks, Reprojections $reprojections, Jobs $jobs, Invoices $invoices, DocumentsServed $DocumentsServed, Processes $processes, Steps $steps, Dependent $dependent)
    {

        $this->orders = $orders;
        $this->tasks = $tasks;
        $this->reprojections = $reprojections;
        $this->jobs = $jobs;
        $this->invoices = $invoices;
        $this->DocumentsServed = $DocumentsServed;
        $this->Processes = $processes;
        $this->Steps = $steps;
        $this->Dependent = $dependent;
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

            //Find data for selected process
            $process = Processes::whereId($id)->first();

            //Find processes in table
            $processes = DB::table('Processes')->where('id', '!=', $id)->get();

            //Determine if process is dependent on current process

            $depArray = array();

            foreach($processes as $dependent){

                //check to see if process is in dependent table

                $depProcess = Dependent::wherepredProcess($id)->wheredepProcess($dependent->id)->first();

                //if found in dependent table, set var to "yes"
                if(!empty($depProcess)){
                    $depArray[$dependent->id] = "yes";
                }

            }


            Return View::make('dependent.edit')->with(['processes' => $processes ])->with(['depArray' => $depArray])->with('predProcess',$process);

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

        //Retrieve Data
        $dependent = Input::get('dependent');
        $predProcess = Input::get('processId');

        //Find processes in table
        $processes = DB::table('Processes')->where('id', '!=', $predProcess)->get();

        //Save new data
        foreach($processes as $process) {

            //Find process is in dependent table
            $depProcess = Dependent::wheredepProcess($process->id)
                                    ->wherepredProcess($predProcess)->first();

            //If process is in dependent table, update value
            if(!empty($depProcess)) {

                //If box is unchecked, remove entry
                if (!isset($dependent[$process->id])) {
                    $depProcess->delete();
                }
            }

            //If process in NOT in dependent table, create new entry
            else{
                $newDependent = new Dependent;
                $newDependent->pred_process = $predProcess;
                $newDependent->dep_process = $process->id;
                $newDependent->save();

            }
        }
		Return Redirect::Route('dependent.edit', $predProcess);
	}



}
