@extends('layouts.default')

@section('content')

@if(!empty($processes))

    <h3>{{ link_to("/processes/create/", 'Create a process') }}</h3>

<table>
  <tr>
    <th>Process Id</th>
    <th>Process Name</th>
    <th>Service Type</th>
    <th>Steps</th>
    <th>Dependent Processes</th>
    <th>Edit Process</th>
    <th>Delete Process</th>

  </tr>
@foreach ($processes as $process)
<tr>
<td>{{  $process->id }}</td>
    <td>{{$process->name}}</td>
    @if(empty($process->service))
    <td></td>
    @else
    <td>{{$process->service}}</td>
    @endif
    <td>{{ link_to("/template/edit/{$process->id}", 'View Steps') }}</td>
    <td>{{ link_to("/dependent/process/{$process->id}", 'Dependent Processes') }}</td>
    <td>{{ link_to("/processes/edit/{$process->id}", 'Edit Process') }}</td>
    <td>{{ link_to("/processes/destroy/{$process->id}", 'Delete process') }}</td>

@endforeach
</tr>
</table>

@else

    <h3>{{ link_to("/processes/create/", 'Create a process') }}</h3>

@endif
<a href="{{ URL::previous() }}">Go Back</a>
@stop
