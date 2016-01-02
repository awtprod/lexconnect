@extends('layouts.default')

@section('content')
<h1>Dependent Processes for {{ $predProcess->name }}</h1>

{{ Form::open(['route' => 'dependent.store']) }}
<table>
  <tr>
    <th>Process</th>
    <th>Dependent</th>
  </tr>

@foreach ($processes as $process)

<tr>
    <div>
        <td>{{$process->name}}</td>
    </div>
	<div>

@if(!empty($depArray[$process->id]))
	<div><td>
	{{ Form::label('dependent') }}
	{{ Form::checkbox('dependent['.$process->id.']', 'yes', true) }}
	</div>
	</td>
@else
	<div><td>
	{{ Form::label('dependent') }}
	{{ Form::checkbox('dependent['.$process->id.']', 'yes') }}
	</div>
	</td>
@endif
</div>
@endforeach
</tr>
</table>

<td>{{ Form::hidden('processId', $predProcess->id) }}</td>

	<div>{{ Form::submit('Save Dependents') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
