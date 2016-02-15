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



}
