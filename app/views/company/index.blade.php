@extends('layouts.default')

@section('content')
<h1>Companies</h1>
{{ link_to('/company/create', 'Add Company') }}
@if ($company->count())

@foreach ($company as $companies)
<li>{{ link_to("/company/{$companies->id}", $companies->name) }} </li>
@endforeach
@else
<h2>No Companies to display!</h2>
@endif
@stop
