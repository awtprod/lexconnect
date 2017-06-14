<?php

class DependentController extends \BaseController {
    


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
            $processes = Processes::where('id', '!=', $id)->get();

            //Determine if process is dependent on current process

            $predArray = array();

            foreach($processes as $predecessor){

                //check to see if process is in dependent table

                $predProcess = Dependent::wheredepProcess($id)->wherepredProcess($predecessor->id)->first();

                //if found in dependent table, set var to "yes"
                if(!empty($predProcess)){
                    $predArray[$predecessor->id] = "yes";
                }

            }


            Return View::make('dependent.edit')->with(['processes' => $processes ])->with(['predArray' => $predArray])->with('predProcess',$process);

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
        $predecessor = Input::get('predecessor');
        $depProcess = Input::get('processId');

        //Find processes in table
        $processes = Processes::where('id', '!=', $depProcess)->get();

        //Save new data
        foreach($processes as $process) {

            //Find process is in dependent table
            $predProcess = Dependent::wherepredProcess($process->id)
                                    ->wheredepProcess($depProcess)->first();

            //If process is in dependent table, update value
            if(!empty($predProcess)) {

                //If box is unchecked, remove entry
                if (!isset($predecessor[$process->id])) {
                    $predProcess->delete();
                }
            }

            //If process in NOT in dependent table, create new entry
            elseif(isset($predecessor[$process->id])){
                $newDependent = new Dependent;
                $newDependent->pred_process = $process->id;
                $newDependent->dep_process = $depProcess;
                $newDependent->save();

            }
        }
		Return Redirect::Route('processes.index');
	}



}
