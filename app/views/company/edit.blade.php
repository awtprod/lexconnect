@extends('layouts.default')

@section('content')
    <h1>Edit Company</h1>

    {{ Form::open(['route' => 'company.save']) }}
    <div>
        {{ Form::label('name', 'Company Name: ') }}
        {{ Form::text('name', $company->name ) }}
        {{ $errors->first('name') }}
    </div>
    @if(Auth::user()->user_role == 'Admin')
    <div>
        {{ Form::label('v_c', 'Company Class: ') }}
        {{ Form::select('v_c', array('Vendor' => 'Vendor', 'Client' => 'Client'),$company->v_c ); }}
        {{ $errors->first('v_c') }}
    </div>
    @endif

    @if($company->vendor_prints)
        {{ Form::label('vendor_prints', 'Prints Documents: ') }}
        {{ Form::checkbox('vendor_prints','1', true)}}
    @else
        {{ Form::label('vendor_prints', 'Prints Documents: ') }}
        {{ Form::checkbox('vendor_prints')}}
    @endif

    @if(Auth::user()->user_role == 'Admin')
        <div>
            {{ Form::label('pay_method', 'Payment Method: ') }}
            {{ Form::select('pay_method', array('Check' => 'Check', 'ACH Debit/Credit' => 'ACH Debit/Credit'),$company->pay_method ); }}
            {{ $errors->first('pay_method') }}
        </div>
    @else
        <div>
            {{ Form::label('pay_method', 'Payment Method: ') }}
            {{ $company->pay_method }}
            {{ Form::hidden('pay_method', $company->pay_method) }}
        </div>
    @endif
    <div>
        {{ Form::label('address', 'Street Address: ') }}
        {{ Form::text('address', $company->address) }}
        {{ $errors->first('address') }}
    </div>

    <div>
        {{ Form::label('city', 'City: ') }}
        {{ Form::text('city', $company->city) }}
        {{ $errors->first('city') }}
    </div>

    <div>
        {{ Form::label('state', 'State: ') }}
        {{ Form::select('state', $states, $company->state) }}
        {{ $errors->first('state') }}
    </div>
    <div>
        {{ Form::label('zip_code', 'Zip Code: ') }}
        {{ Form::text('zip_code', $company->zip_code) }}
        {{ $errors->first('zip_code') }}
    </div>
    <div>
        {{ Form::label('phone', 'Phone: ') }}
        {{ Form::text('phone', $company->phone) }}
        {{ $errors->first('phone') }}
    </div>
    <div>
        {{ Form::label('email', 'Email: ') }}
        {{ Form::email('email', $company->email) }}
        {{ $errors->first('email') }}
    </div>
        {{Form::hidden('id', $company->id) }}
    <div>{{ Form::submit('Save Changes') }}<a href="{{ URL::previous() }}">Go Back</a>
        {{ Form::close() }}

<a href="{{ URL::previous() }}">Go Back</a>
@stop

