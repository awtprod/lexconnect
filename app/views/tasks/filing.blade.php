@extends('layouts.default')

@section('content')
<h1>Add Filing Details</h1>

{{ Form::open(array('route'=>'tasks.documents','files'=>true)) }}

	<div>
	{{ Form::label('date', 'Date Filed: ') }}
	{{ Form::input('date', 'date') }}
	{{ $errors->first('date') }}
	</div>
		<div>
	{{ Form::label('case', 'Court Case: ') }}
	{{ Form::text('case') }}
	{{ $errors->first('case') }}
	</div>
  {{ Form::label('documents','File') }}
  {{ Form::file('documents','') }}
  	{{ $errors->first('documents') }}
	{{ Form::hidden('job', $jobs_id) }}
	{{ Form::hidden('tasks_id', $tasks_id) }}
	<div>{{ Form::submit('Complete Filing') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
