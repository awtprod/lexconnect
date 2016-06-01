<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Documents extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
	public $timestamps = true;
	protected $fillable = ['fileDate','courtcase','document', 'filename', 'filepath', 'job_id', 'order_id'];

    public static $fileRules = [
        'Summons' => 'mimes:pdf|max:10000',
        'Declaration_of_Military_Search' => 'mimes:pdf|max:10000',
        'Filed_Complaint' => 'mimes:pdf|max:50000',
        'Unfiled_Complaint' => 'mimes:pdf|max:50000',
        'Unrecorded_Notice_of_Pendency' => 'mimes:pdf|max:10000',
        'Recorded_Notice_of_Pendency' => 'mimes:pdf|max:10000',
        'Unrecorded_Lis_Pendens' => 'mimes:pdf|max:10000',
        'Recorded_Lis_Pendens' => 'mimes:pdf|max:10000',
        'Case_Hearing_Schedule' => 'mimes:pdf|max:10000',
    ];

	
	public $errors;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'documents';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

    public function ValidFiles($docInput)
    {
        $validation = Validator::make(array($docInput["docType"] => $docInput["file"]), static::$fileRules);

        if ($validation->passes()) return true;

        $this->errors = $validation->messages();

        return false;
    }

	public function pageCount($input){

		//$cmd = "/path/to/pdfinfo";           // Linux
		$cmd = public_path().'\xpdfbin-win-3.04\bin64\pdfinfo.exe';  // Windows

		$file = $input["path"].'/'.$input["file"];

		// Parse entire output
		// Surround with double quotes if file name has spaces
		exec("$cmd \"$file\"", $output);

		// Iterate through lines
		$pagecount = 0;
		foreach($output as $op)
		{
			// Extract the number
			if(preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1)
			{
				$pagecount = intval($matches[1]);
				break;
			}
		}

		return $pagecount;
	}

	public function saveDoc($input){

		//Set file variable
		$destinationPath = storage_path() . '/' . $input["folder"];
		$file = str_random(6);

		if($input["document"]["type"] == "other"){
			$filename = $input["orders_id"] . $file . '_' . $input["document"]["other"] . '.pdf';
		}
		else{
			$filename = $input["orders_id"] . $file . '_' . $input["document"]["type"] . '.pdf';
		}
		//$filepath = public_path('service_documents/' . $filename);
		$input["document"]["file"]->move($destinationPath, $filename);

		//Get page count
		$pagecount = $this->pageCount(['path' => $destinationPath, 'file' => $filename]);

		$document = new Documents;

		if($input["document"]["type"] == "other"){
			$document->document = $input["document"]["other"];
		}
		else{
			$document->document = $input["document"]["type"];
		}
		$document->order_id = $input["orders_id"];
		$document->filename = $filename;
		$document->filepath = $input["folder"];
		$document->pages = $pagecount;
		$document->save();
	}

}
