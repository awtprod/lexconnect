@extends('layouts.default')

@section('content')
<h1>Login</h1>
{{ $errors->first(); }}
{{ Form::open(['route' => 'sessions.store']) }}
	<div>
	{{ Form::label('email', 'Email: ') }}
	{{ Form::email('email') }}
	{{ $errors->first('email') }}
	</div>
	<div>
	{{ Form::label('password', 'Password: ') }}
	{{ Form::password('password') }}
	{{ $errors->first('password') }}
	</div>
	{{ Form::hidden('return', URL::previous()) }}
	<div>{{ Form::submit('Login') }}</div>
{{ Form::close() }}
<a href="{{ route('resend_activation') }}">Resend Activation Code?</a><p>
<a href="{{ route('forgot_password') }}">Forgot Password?</a><p>
<a href="{{ URL::previous() }}">Go Back</a>
@stop
