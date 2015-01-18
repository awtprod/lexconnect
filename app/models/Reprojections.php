<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class Reprojections extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['reprojected','description','job_id','order_id','servee_id'];
	
	public static $rules = [
		'reprojected' => 'required|date',
		'description' => 'required'
		];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reprojections';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function isValid()
	{
		
		$validation = Validator::make($this->attributes, static::$rules);
		
		if ($validation->passes()) return true;
		
		$this->errors = $validation->messages();
		
		return false;
	}
	
	public function status($id)
	{
	
	$status = array();
	
	//Determin the task process
	$task = DB::table('tasks')->where('id', $id)->first();
	
	//Find latest serve attempt
	$attempt = DB::table('attempts')->where('job', $task->job_id)->orderBy('created_at', 'desc')->first();
	
	//Find latest reprojection
	$reprojection = Reprojections::whereJobId($task->job_id)->orderBy('created_at', 'desc')->first();
	
	//Determine if there are no reprojections or attempts
	if(empty($attempt) AND empty($reprojection)){
		
	$status['description'] = 'On Time';
	
	return $status;
	}
	//No attempts made yet
	elseif(empty($attempt)){
	
	$status['description'] = $reprojection->description;
	$status['date'] = date("m/d/Y", strtotime($reprojection->created_at));	
	$status['time'] = date("h:i A", strtotime($reprojection->created_at));
	
	return $status;
	
	}
	//No reprojections
	elseif(empty($reprojection)){
		
	$status['description'] = $attempt->description;
	$status['date'] = date("m/d/Y", strtotime($attempt->date));	
	$status['time'] = date("h:i A", strtotime($attempt->time));	

	return $status;
	
	}
	
	//Determin if an attempt was made or the task was reprojected most recently
	
	else{

	//Convert dates to Carbon-friendly form
	$reprojection_y = date("Y", strtotime($reprojection->created_at));
	$reprojection_m = date("m", strtotime($reprojection->created_at));
	$reprojection_d = date("d", strtotime($reprojection->created_at));

	$attempt_y = date("Y", strtotime($attempt->date));
	$attempt_m = date("m", strtotime($attempt->date));
	$attempt_d = date("d", strtotime($attempt->date));

	//Calculate difference in days between most recent attempt and reprojection
	$reprojection_difference = Carbon::now()->diffInDays(Carbon::createFromDate($reprojection_y, $reprojection_m, $reprojection_d));
	$attempt_difference = Carbon::now()->diffInDays(Carbon::createFromDate($attempt_y, $attempt_m, $attempt_d));	
	
	//Determine if which difference is greater
	if($reprojection_difference > $attempt_difference){
		
	$status['description'] = $attempt->description;
	$status['date'] = date("m/d/Y", strtotime($attempt->date));	
	$status['time'] = date("h:i A", strtotime($attempt->time));	

	return $status;	
		
	}
	else{
	
	$status['description'] = $reprojection->description;
	$status['date'] = date("m/d/Y", strtotime($reprojection->created_at));	
	$status['time'] = date("h:i A", strtotime($reprojection->created_at));
	
	return $status;
	
	}
	
	}
		

	}



}
