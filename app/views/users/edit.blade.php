@extends('layouts.default')

@section('content')
<h1>Edit Profile</h1>

{{ Form::open(['route' => 'users.store_edit']) }}
	<div>
	{{ Form::label('First Name', 'First Name: ') }}
	{{ Form::text('fname', $user->fname) }}
	{{ $errors->first('fname') }}
	</div>
	<div>
	{{ Form::label('Last Name', 'Last Name: ') }}
	{{ Form::text('lname', $user->lname) }}
	{{ $errors->first('lname') }}
	</div>
	<div>
	{{ Form::label('email', 'Email: ') }}
	{{ Form::email('email', $user->email) }}
	{{ $errors->first('email') }}
	</div>
	<div>
	{{ Form::label('email_confirmation', 'Confirm Email: ') }}
	{{ Form::email('email_confirmation', $user->email) }}
	{{ $errors->first('email_confirmation') }}
	</div>
	<div>
	{{ Form::label('role', 'User Level: ') }}
	{{ Form::select('role', array('Employee' => 'Employee', 'Supervisor' => 'Supervisor'), $user->role); }}
	{{ $errors->first('role') }}
	</div>
	@if (Auth::user()->user_role=='Admin')
	<div>
	{{ Form::label('company', 'Company: ') }}
	{{ Form::select('company', $company, $user->company) }}
	{{ $errors->first('company') }}
	</div>
	@else 
	<div>
	{{ Form::label('company', 'Company: ') }}
	{{ $user->company }}
	{{ Form::hidden('company', $user->company) }}
	</div>
	@endif
	@if (Auth::user()->user_role=='Admin')
	<div>
	{{ Form::label('user_role', 'Site User Level: ') }}
	{{ Form::select('user_role', array('Vendor' => 'Vendor', 'Client' => 'Client', 'Admin' => 'Admin'), $user->user_role); }}
	{{ $errors->first('user_role') }}
	</div>
	@else
	<div>
	{{ Form::hidden('user_role', $user->user_role); }}
	</div>
	@endif
	<div>
	{{ Form::hidden('company_id', $user->company_id) }}
	</div>
	{{ Form::hidden('id', $user->id) }}
	<div>{{ Form::submit('Save') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
