@extends('layouts.default')

@section('content')
<h1>Update Password</h1>

{{ Form::open(['route' => 'post_reset_password']) }}

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
	{{ Form::hidden('password_reset', $password_reset) }}
	<div>{{ Form::submit('Update Password') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
