@extends('layouts.default')

@section('content')
<h1>Add Recording Details</h1>

{{ Form::open(array('route'=>'tasks.documents','files'=>true)) }}

	<div>
	{{ Form::label('date', 'Date Recorded: ') }}
	{{ Form::input('date', 'date') }}
	{{ $errors->first('date') }}
	</div>
		<div>
	{{ Form::label('recording', 'Instrument #: ') }}
	{{ Form::text('recording') }}
	{{ $errors->first('recording') }}
	</div>
	  <input type="file" name="documents" id="">
	{{ Form::hidden('job', $job_id) }}
	{{ Form::hidden('tasks_id', $tasks_id) }}
	<div>{{ Form::submit('Complete Recording') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
