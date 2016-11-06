@extends('layouts.default')

@section('content')
<h1>Predecessor Processes for {{ $predProcess->name }}</h1>

{{ Form::open(['route' => 'dependent.store']) }}
<table>
  <tr>
    <th>Process</th>
    <th>Predecessor</th>
  </tr>

@foreach ($processes as $process)

<tr>
    <div>
        <td>{{$process->name}}</td>
    </div>
	<div>

@if(!empty($predArray[$process->id]))
	<div><td>
	{{ Form::label('predecessor') }}
	{{ Form::checkbox('predecessor['.$process->id.']', 'yes', true) }}
	</div>
	</td>
@else
	<div><td>
	{{ Form::label('predecessor') }}
	{{ Form::checkbox('predecessor['.$process->id.']', 'yes') }}
	</div>
	</td>
@endif
</div>
@endforeach
</tr>
</table>

<td>{{ Form::hidden('processId', $predProcess->id) }}</td>

	<div>{{ Form::submit('Save Predecessors') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
