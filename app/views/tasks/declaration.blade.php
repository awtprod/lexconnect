@extends('layouts.default')

@section('content')
<h1>Create Declaration of Mailing</h1><p>
@if(!empty($job->declaration))
<h2>Upload Executed Declaration of Mailing</h2><p>
{{ Form::open(array('route'=>'jobs.declaration','files'=>true)) }}
  	{{ $errors->first('declaration') }}<p>
  <input type="file" name="declaration" id="">
  <br/>
  {{ Form::hidden('job_id', $job->id) }}
  <!-- submit buttons -->
  {{ Form::submit('Upload Executed Declaration') }}
 @endif
	<div>
	<h3> Mail Documents To:</h3><p>
	{{ $job->street }}<p>
	{{ $job->city }},{{ $job->state }}{{ $job->zipcode }}<p>
	</div>
{{ Form::open(['route' => 'tasks.declaration']) }}

	<div>
	{{ Form::label('mail_date', 'Date Mailed: ') }}
	{{ Form::input('date', 'mail_date') }}
	{{ $errors->first('date') }}
	</div>
	<div>
	{{ Form::label('declarant', 'Declarant: ') }}
	{{ Form::select('declarant', $servers) }}
	</div>
	{{ Form::hidden('job', $job_id) }}
	{{ Form::hidden('tasks_id', $tasks_id) }}
	<div>{{ Form::submit('Create Declaration') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
