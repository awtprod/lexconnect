<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class Invoices extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['order_id', 'servee_id','job_id','client_amt','vendor_amt','vendor','client','invoice'];
	
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
	
	public function CreateInvoice($id){
		
	//Find Job
	$job = DB::table('jobs')->where('id', $id)->first();
	
	//Determine if filing job or service job
	$process = DB::table('tasks')->where('job_id', $id)->orderBy('process', 'asc')->pluck('process');
	$fee = DB::table('rules')->where('name', $job->state)->first();
	if(empty($fee)){
	$fee = DB::table('rules')->where('abbrev', $job->state)->first();
		
	}
	
	$data = array();
	
	if($process == 0){
		
		$data['client_fee'] = $fee->filing_client;
		$data['vendor_fee'] = $fee->filing_vendor;
		$data['product'] = "Filing/Recording";
	}
	else{
		$data['client_fee'] = $fee->service_client;
		$data['vendor_fee'] = $fee->service_vendor;	
		$data['product'] = "Effect Service of Process";		
	}
	
	//load service data for invoice
		$serve = DB::table('serve')->where('job_id', $job->id)->first();
		if(empty($serve)){
		$serve = DB::table('attempts')->where('job', $job->id)->orderBy('date', 'desc')->first();
	
		}
		if(empty($serve->date)){
		$data['serve_date'] = date("m/d/y", strtotime(Carbon::now()));	
		}
		else{
		$data['serve_date'] = date("m/d/y", strtotime($serve->date));
		}
		$data['defendant'] = $job->defendant;
		$data['street'] = $job->street;	
		$data['city'] = $job->city;	
		$data['state'] = $job->state;	
		$data['zip'] = $job->zipcode;
		$data['order'] = $job->order_id;
		$data['job'] = $job->id;
	//load client data
		$client = DB::table('company')->where('name', $job->client)->first();
		
		$data['client'] = $client->name;
		$data['client_street'] = $client->address;
		$data['client_city'] = $client->city;
		$data['client_state'] = $client->state;
		$data['client_zip'] = $client->zip_code;
		
		//Create Invoice
		$file = str_random(6);
		$filename =  $file . '_'. 'invoice.pdf';
		$filepath = public_path('invoices/' . $filename);
		$pdf = PDF::loadView('invoices.create', ['data' => $data])->save($filepath);
		
		//Save data to table
		$invoice = new Invoices;
		$invoice->order_id = $job->order_id;
		$invoice->job_id = $job->id;
		$invoice->servee_id = $job->servee_id;
		$invoice->client_amt = $data['client_fee'];
		$invoice->vendor_amt = $data['vendor_fee'];
		$invoice->vendor = $job->vendor;
		$invoice->client = $job->client;
		$invoice->invoice = $filename;
		$invoice->save();
		
		return true;		

	}



}
