@extends('layouts.default')

@section('content')
<p>{{ link_to("/company/{$company->id}/edit", 'Edit') }}</p>
<p>Company: {{ $company->name }}</p>
<p>Type: {{ $company->v_c }}</p>
@if($company->vendor_prints)
<p>Prints: Yes</p>
@else
<p>Prints: No</p>
@endif
<p>Payment Method: {{ $company->pay_method }}</p>
<p>Address: {{ $company->address }}, {{ $company->city }}, {{ $company->state }} {{ $company->zip_code }}</p>
<p>Phone: {{ $company->phone }}</p>
<p>Email: {{ $company->email }}</p>

<p>{{ link_to("/rates/{$company->v_c}/{$company->id}/", 'View Rates') }}</p>

<a href="{{ URL::previous() }}">Go Back</a>
@stop

