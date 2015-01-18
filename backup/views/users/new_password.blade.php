@extends('layouts.default')

@section('content')
<h1>Create Password</h1>

{{ Form::open(['url' => '/users/new_password/' . $activation_code]) }}

	<div>
	{{ Form::label('password', 'Password: ') }}
	{{ Form::password('password') }}
	{{ $errors->first('password') }}
	</div>
	<div>
	{{ Form::label('password_confirmation', 'Confirm Password: ') }}
	{{ Form::password('password_confirmation') }}
	{{ $errors->first('password_confirmation') }}
	</div>

	<div>{{ Form::submit('Update Password') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
