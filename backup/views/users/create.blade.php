@extends('layouts.default')

@section('content')
<h1>Add New User</h1>

{{ Form::open(['route' => 'users.store']) }}
	<div>
	{{ Form::label('name', 'Name: ') }}
	{{ Form::text('name') }}
	{{ $errors->first('name') }}
	</div>
	<div>
	{{ Form::label('email', 'Email: ') }}
	{{ Form::email('email') }}
	{{ $errors->first('email') }}
	</div>
	<div>
	{{ Form::label('email_confirmation', 'Confirm Email: ') }}
	{{ Form::email('email_confirmation') }}
	{{ $errors->first('email_confirmation') }}
	</div>
	<div>
	{{ Form::label('role', 'User Level: ') }}
	{{ Form::select('role', array('Employee' => 'Employee', 'Supervisor' => 'Supervisor'), 'Employee'); }}
	{{ $errors->first('role') }}
	</div>
	@if (Auth::user()->user_role=='Admin')
	<div>
	{{ Form::label('company', 'Company: ') }}
	{{ Form::select('company', $company, Input::old('company')) }}
	{{ $errors->first('company') }}
	</div>
	@else 
	<div>
	{{ Form::label('company', 'Company: ') }}
	{{ Auth::user()->company }}
	{{ Form::hidden('company', Auth::user()->company) }}
	</div>
	@endif
	@if (Auth::user()->user_role=='Admin')
	<div>
	{{ Form::label('user_role', 'Site User Level: ') }}
	{{ Form::select('user_role', array('Vendor' => 'Vendor', 'Client' => 'Client', 'Admin' => 'Admin'), 'Vendor'); }}
	{{ $errors->first('user_role') }}
	</div>
	@else
	<div>
	{{ Form::hidden('user_role', Auth::user()->user_role); }}
	</div>
	@endif

	<div>{{ Form::submit('Add User') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
