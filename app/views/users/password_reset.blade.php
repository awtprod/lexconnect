@extends('layouts.default')
@section('head')
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
	<script>
		jQuery(document).ready(function($) {
			//Validate Data
			$("#password_update").validate({

				rules: {
					password: "required",
					password_confirmation: {
						equalTo: "#password"
					}
				},
			});
		});
	</script>

@section('content')
<h1>Update Password</h1>

{{ Form::open(array('route' => 'post_reset_password', 'id' => 'password_update')) }}

		Password:<input type="password" id="password" name="password"><br>

		{{ $errors->first('password') }}
		Confirm Password:<input type="password" id="password_confirmation" name="password_confirmation"><br>

		{{ $errors->first('password_confirmation') }}
	{{ Form::hidden('password_reset', $password_reset) }}
	<div>{{ Form::submit('Update Password') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
