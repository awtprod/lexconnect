@extends('layouts.default')

@section('content')
<h1>Add Service Attempt</h1>

{{ Form::open(['route' => 'attempts.store']) }}

	<div>
	{{ Form::label('date', 'Date: ') }}
	{{ Form::input('date', 'date') }}
	{{ $errors->first('date') }}
	</div>
	<div>
	{{ Form::label('time', 'Time: ') }}
	{{ Form::input('time', 'time') }}
	{{ $errors->first('time') }}
	</div>
	<div>
	{{ Form::label('description', 'Description: ') }}
	{{ Form::textarea('description') }}
	{{ $errors->first('description') }}
	</div>
	<div>
	{{ Form::label('non-serve', 'Non-Serve: ') }}
	{{ Form::checkbox('non-serve', 'yes') }} Note: This will end service for this defendant and generate a Proof of Service.
	{{ $errors->first('non-serve') }}
	</div>
	{{ Form::hidden('job', $job) }}
	{{ Form::hidden('taskId', $taskId) }}
	<div>{{ Form::submit('Add Attempt') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
