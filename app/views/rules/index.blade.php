@extends('layouts.default')

@section('content')
<h1>View Rules</h1>
{{ link_to('/rules/create', 'Edit') }}

<table>
  <tr>
    <th>State</th>
    <th>Affidavit Required</th>		
    <th>Sub-Serve Mailing</th>
    <th>Filing Fee (to client)</th>
    <th>Filing Fee (to vendor)</th>
    <th>Service Fee (to client)</th>
    <th>Service Fee (to vendor)</th>
  </tr>
@foreach ($rules as $rule)
<tr>
	<div>
	<td>{{ $rule->name }}</td>
	</div>

	<div><td>
	<td>{{ $rule->affidavit }}</td>
	</div>
	</td>
	<div>
	<td>{{ $rule->mailing }}</td>
	</div>
	<div>
	<td>{{ $rule->filing_client }}</td>
	</div>
	<div>
	<td>{{ $rule->filing_vendor }}</td>
	</div>
	<div>
	<td>{{ $rule->service_client }}</td>
	</div>
	<div>
	<td>{{ $rule->service_vendor }}</td>
	</div>
@endforeach
</tr>
</table>

<a href="{{ URL::previous() }}">Go Back</a>
@stop
