@extends('layouts.default')

@section('content')
<h1>Edit Rules</h1>

{{ Form::open(['route' => 'rules.store']) }}
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
	<td>{{ Form::hidden('state['.$rule->id.']', $rule->name) }}{{ $rule->name }}</td>
@if($rule->affidavit == 'yes')
	<div><td>
	{{ Form::label('affidavit') }}
	{{ Form::checkbox('affidavit['.$rule->id.']', 'yes', true) }} 
	</div>
	</td>
@else
	<div><td>
	{{ Form::label('affidavit') }}
	{{ Form::checkbox('affidavit['.$rule->id.']', 'yes') }} 
	</div>
	</td>
@endif
@if($rule->mailing == 'yes')
	<div><td>
	{{ Form::label('mailing') }}
	{{ Form::checkbox('mailing['.$rule->id.']', 'yes', true) }} 
	</div>
	</td>
@else
	<div><td>
	{{ Form::label('mailing') }}
	{{ Form::checkbox('mailing['.$rule->id.']', 'yes') }} 
	</div>
	</td>
@endif
<div><td>
	{{ Form::label('filing_client') }}
	{{ Form::text('filing_client['.$rule->id.']', $rule->filing_client) }}	
</div>
</td>
<div><td>
	{{ Form::label('filing_vendor') }}
	{{ Form::text('filing_vendor['.$rule->id.']', $rule->filing_vendor) }}	
</div>
</td>
<div><td>
	{{ Form::label('service_client') }}
	{{ Form::text('service_client['.$rule->id.']', $rule->service_client) }}	
</div>
</td>
<div><td>
	{{ Form::label('service_vendor') }}
	{{ Form::text('service_vendor['.$rule->id.']', $rule->service_vendor) }}	
</div>
</td>
@endforeach
</tr>
</table>
	<div>{{ Form::submit('Save Rules') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
