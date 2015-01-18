@extends('layouts.default')

@section('content')
<h1>Add New Company</h1>

{{ Form::open(['route' => 'company.store']) }}
	<div>
	{{ Form::label('name', 'Company Name: ') }}
	{{ Form::text('name') }}
	{{ $errors->first('name') }}
	</div>
	<div>
	{{ Form::label('v_c', 'Company Class: ') }}
	{{ Form::select('v_c', array('Vendor' => 'Vendor', 'Client' => 'Client')); }}
	{{ $errors->first('v_c') }}
	</div>
	<div>
	{{ Form::label('address', 'Street Address: ') }}
	{{ Form::text('address') }}
	{{ $errors->first('address') }}
	</div>
	
	<div>
	{{ Form::label('city', 'City: ') }}
	{{ Form::text('city') }}
	{{ $errors->first('city') }}
	</div>
	
	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, Input::old('name')) }}
	{{ $errors->first('state') }}
	</div>
		<div>
	{{ Form::label('zip_code', 'Zip Code: ') }}
	{{ Form::text('zip_code') }}
	{{ $errors->first('zip_code') }}
	</div>
			<div>
	{{ Form::label('phone', 'Phone: ') }}
	{{ Form::text('phone') }}
	{{ $errors->first('phone') }}
	</div>
			<div>
	{{ Form::label('email', 'Email: ') }}
	{{ Form::email('email') }}
	{{ $errors->first('email') }}
	</div>
	<div>{{ Form::submit('Add Company') }}<a href="{{ URL::previous() }}">Go Back</a>
{{ Form::close() }}
@stop
