@extends('layouts.default')

@section('content')
<h1>Resend Activation Code</h1>
{{ $errors->first(); }}
{{ Form::open(['route' => 'push_resend_activation']) }}

	<div>
	{{ Form::label('email', 'Email: ') }}
	{{ Form::email('email') }}
	{{ $errors->first('email') }}
	</div>

	<div>{{ Form::submit('Resend Activation Code') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
