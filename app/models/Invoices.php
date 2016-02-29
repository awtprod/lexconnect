<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class Invoices extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['order_id', 'servee_id','job_id','client_amt','vendor_amt','app_fee','free_pgs','pg_rate','vendor','client','invoice'];
	
	public static $rules = [
		'date' => 'required|date',
		'time' => 'required',
		'served_upon' => 'required',
		'description' => 'required'
		];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invoices';

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
	
	public function CreateInvoice($data){
		
	//Find Job
	$job = Jobs::whereId($data['jobId'])->first();

	//Find current page rate for vendor
	$pages = VendorRates::whereVendor($job->vendor)->whereState($job->state)->whereCounty($job->county)->first();

	//Find service rate
	$rate = ClientRates::whereClient($job->client)->pluck($data['process'].'FeeRate');

	//Find app rate
	$appFee = 	$data['rate'] * $rate;

	//Determine total to charge client
	$clientRate = ($data['rate']) + ($data['rate'] * $rate);
		
		//Save data to table
		$invoice = new Invoices;
		$invoice->order_id = $job->order_id;
		$invoice->job_id = $job->id;
		$invoice->servee_id = $job->servee_id;
		$invoice->client_amt = $clientRate;
		$invoice->vendor_amt = $data['rate'];
		$invoice->app_fee = $appFee;
		$invoice->free_pgs = $pages->free_pgs;
		$invoice->pg_rate = $pages->pg_rate;
		$invoice->vendor = $job->vendor;
		$invoice->client = $job->client;
		$invoice->save();
		
		return true;		

	}



}
