<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class Invoices extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['status','order_id', 'servee_id','job_id','client_amt','vendor_amt','service_fee','app_fee_rate','free_pgs','pg_rate','vendor','client','invoice'];
	
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

	//Find service rate
	$surcharge = ClientRates::whereClient($job->client)->pluck($data['process'].'Surcharge');

	//Determine # of pages to bill for
	$paidPgs = $data["numPgs"] - $data["freePgs"];

	$rate = $data["rate"];

	//Determine if personal service is required
	if(!empty($data["personal"]["personal"]))	{

		$rate += $data["personalRate"];

	}

	//Add pages to fee
	$vendorRate = $rate + ($data["pageRate"] * $paidPgs);

	//Find app rate
	$appFee = 	$vendorRate * $surcharge;

	//Determine total to charge client
	$clientRate = $vendorRate + $appFee;

	//Determine if client is flat rate
	$flat = ClientRates::whereClient($job->client)->pluck($data['process'].'Flat');

		if($flat != '0'){

			$clientRate = $flat;

		}
		
		//Save data to table

		//Create Invoice for Vendor
		$invoice = new Invoices;
		$invoice->order_id = $job->order_id;
		$invoice->job_id = $job->id;
		$invoice->servee_id = $job->servee_id;
		$invoice->vendor_amt = $vendorRate;
		$invoice->service_fee = $rate;
		$invoice->app_fee_rate = $surcharge;
		$invoice->free_pgs = $data["freePgs"];
		$invoice->pg_rate = $data["pageRate"];
		$invoice->vendor = $job->vendor;
		$invoice->status = '0';
		$invoice->save();

		//Create Invoice for Client

		$invoice = new Invoices;
		$invoice->order_id = $job->order_id;
		$invoice->job_id = $job->id;
		$invoice->servee_id = $job->servee_id;
		$invoice->client_amt = $clientRate;
		$invoice->app_fee_rate = $surcharge;
		$invoice->client = $job->client;
		$invoice->status = '0';
		$invoice->save();
		
		return true;		

	}

public function BillInvoice($data){

	//find documents served
	$documentsServed = DocumentsServed::whereOrderId($data['orderId'])->get();

	//Count pages of service documents
	$numPgs = 0;

	foreach ($documentsServed as $documentServed){

		$numPgs += Documents::whereDocument($documentServed->document)->whereOrderId($data['orderId'])->orderBy('created_at', 'desc')->first();
	}

	//Find invoice for vendor
	$vendorInvoice = Invoices::whereJobId($data['jobId'])->whereNotNull('vendor')->first();

	//Calculate pages to bill
	$excessPgs = $numPgs - ($vendorInvoice->free_pgs);

	$pageAmt = 0;

	//If page count is greater than number of free pages, bill excess
	if($excessPgs > 0){

		$pageAmt = $excessPgs*($vendorInvoice->pg_rate);
	}

	//Determine total cost
	$vendorRate = ($vendorInvoice->service_fee) + $pageAmt;

	//Update Vendor Invoice
	$vendorInvoice->vendor_amt = $vendorRate;
	$vendorInvoice->status = '1';
	$vendorInvoice->save();

	//Find invoice for Client
	$clientInvoice = Invoices::whereJobId($data['jobId'])->whereNotNull('client')->first();

	//Determine client rate
	$clientRate = $vendorRate + ($vendorRate * $clientInvoice->app_fee_rate);

	//Determine if client is flat rate
	$job = Jobs::whereId($data['job'])->first();

	$flat = ClientRates::whereClient($job->client)->pluck($data['process'].'Flat');

	if($flat != '0'){

		$clientRate = $flat;

	}

	//Create PDF
	$date = date('jS \d\a\y \of F Y', strtotime(Carbon::now()));

	$file = str_random(6);
	$filename =  $file . '_'. 'invoice.pdf';
	$filepath = storage_path().'/invoices/'. $filename;


	//Create proof
	$client = Company::whereId($job->client)->first();

	$serve = Serve::wherejobId($job->id)->first();

	$pdf = PDF::loadView('invoices.pdf', ['serve' => $serve], ['job' => $job], ['date' => $date], ['client' => $client], ['invoice' => $clientInvoice])->save($filepath);

	//Update Document Table
	$invoicePDF = new Documents;
	$invoicePDF->document = 'Invoices';
	$invoicePDF->job_id = $job->id;
	$invoicePDF->order_id = $job->order_id;
	$invoicePDF->filename = $filename;
	$invoicePDF->filepath = 'invoices';
	$invoicePDF->save();

	//Update Client Invoice
	$clientInvoice->client_amt = $clientRate;
	$clientInvoice->doc_id = $invoicePDF->id;
	$clientInvoice->status = '1';
	$clientInvoice->save();
}

}
