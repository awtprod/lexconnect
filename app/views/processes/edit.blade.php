@extends('layouts.default')

@section('content')
<h1>Edit Process:{{$process->name}}</h1>

{{ Form::open(array(
                    'method' => 'PUT',
                    'route' => 'processes.update'
                    )) }}
<div id="table">

<table>
  <tr>
    <th>Process Id</th>
    <th>Process Name</th>
    <th>Service Type</th>
  </tr>


<tr>
        <td>{{ $process->id }}</td>
        <td>
                {{ Form::label('name') }}
                {{ Form::text('name', $process->name) }}

        </td>
    <td>
        {{ Form::label('service', 'Service Type: ') }}

        @if(!empty($process->service))
        {{ Form::select('service', array(''=>'Select', 'Filing' => 'Filing', 'Posting' => 'Posting', 'Process Service' => 'Process Service', 'Recording' => 'Recording', 'Supplemental' => 'Supplemental'), $process->service) }}
        @else
        {{ Form::select('service', array(''=>'Select', 'Filing' => 'Filing', 'Posting' => 'Posting', 'Process Service' => 'Process Service', 'Recording' => 'Recording', 'Supplemental' => 'Supplemental')) }}
        @endif

    </td>


</tr>
</table>
</div>

{{ Form::hidden('processId', $process->id) }}
	<div>{{ Form::submit('Save Process') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
