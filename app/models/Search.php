<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Search extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['email'];
	
	public static $rules = [
		'password' => 'confirmed|min:8',
		'name' => 'required'
	
		
	];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'search';

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
	
	public function ClientSearch($search)
	{
		$results = array();

		$results['jobs'] = DB::table('jobs')->where(function ($query) use($search){
				$query->where('id', 'LIKE', "%$search%")
				      ->orWhere('defendant', 'LIKE', "%$search%");
				})->where(function ($query) {
					$query->where('client', '=', Auth::user()->company);
				})->get();
				
		$results['orders'] = DB::table('orders')->where(function ($query) use($search){
				$query->where('id', 'LIKE', "%$search%")
				      ->orWhere('plaintiff', 'LIKE', "%$search%")
				      ->orWhere('case', 'LIKE', "%$search%")
				      ->orWhere('reference', 'LIKE', "%$search%");
				})->where(function ($query) {
					$query->where('company', '=', Auth::user()->company);
				})->get();
		return $results;
	}
	
	public function VendorSearch($search)
	{
		
		$results = array();
		
		$results['jobs'] = DB::table('jobs')->where(function ($query) use($search){
				$query->where('id', 'LIKE', "%$search%")
				      ->orWhere('defendant', 'LIKE', "%$search%");
				})->where(function ($query) {
					$query->where('vendor', '=', Auth::user()->company_id);
				})->get();
				
		$results['orders'] = DB::table('orders')->where(function ($query) use($search){
				$query->where('id', 'LIKE', "%$search%")
				      ->orWhere('plaintiff', 'LIKE', "%$search%")
				      ->orWhere('case', 'LIKE', "%$search%")
				      ->orWhere('reference', 'LIKE', "%$search%");
				})->get();				
				
				return $results;
	}

	public function AdminSearch($search)
	{
		$results = array();

		$results['jobs'] = DB::table('jobs')->where(function ($query) use($search){
				$query->where('id', 'LIKE', "%$search%")
				      ->orWhere('defendant', 'LIKE', "%$search%");
				})->get();
				
		$results['orders'] = DB::table('orders')->where(function ($query) use($search){
				$query->where('id', 'LIKE', "%$search%")
				      ->orWhere('plaintiff', 'LIKE', "%$search%")
				      ->orWhere('reference', 'LIKE', "%$search%");
				})->get();
		return $results;
	}

}
