@extends('layouts.default')

@section('content')
<p>Company: {{ $company->name }}</p>
<p>Address: {{ $company->address }}, {{ $company->city }}, {{ $company->state }} {{ $company->zip_code }}</p>
<p>Phone: {{ $company->phone }}</p>
<p>Email: {{ $company->email }}</p>
<a href="{{ URL::previous() }}">Go Back</a>
@stop

