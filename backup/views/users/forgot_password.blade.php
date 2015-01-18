@extends('layouts.default')

@section('content')
<h1>Forgot Password</h1>
{{ $errors->first(); }}
{{ Form::open(['route' => 'push_forgot_password']) }}
	<div>
	{{ Form::label('email', 'Email: ') }}
	{{ Form::email('email') }}
	{{ $errors->first('email') }}
	</div>

	<div>{{ Form::submit('Send Password Reset Email') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
