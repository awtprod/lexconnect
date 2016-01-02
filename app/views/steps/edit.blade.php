@extends('layouts.default')

@section('content')
<h1>Edit Steps for Process:{{$process->name}}</h1>

{{ Form::open(['route' => 'steps.update']) }}
<div id="table">
<table>
    <tr>
        <th>Step Name</th>
        <th>Sort Order</th>
        <th>Starting Status</th>
        <th>Assign User Group</th>
        <th>PopUp Window <br>(Leave Blank For No popup_</th>
        <th>Routine - Original Due Date<br>(Days from start of Process)</br></th>
        <th>Routine - New Due Date<br>(Days for each reprojection)</th>
        <th>Rush - Original Due Date<br>(Days from start of Process)</br></th>
        <th>Rush - New Due Date<br>(Days for each reprojection)</th>
        <th>Same Day - Original Due Date<br>(Days from start of Process)</br></th>
        <th>Same Day - New Due Date<br>(Days for each reprojection)</th>
    </tr>

    <tr>
        @foreach($steps as $step)

            <td>
                    {{ Form::label('name') }}
                    {{ Form::text('name['.$step->id.']',$step->name) }}
            </td>
            <td>
                    {{ Form::label('sortOrder') }}
                    {{ Form::text('sortOrder['.$step->id.']',$step->sortOrder) }}
            </td>
            <td>
                    {{ Form::label('status') }}
                    {{ Form::text('status['.$step->id.']',$step->status) }}
            </td>
            <td>
                    {{ Form::label('group', '') }}
                    {{ Form::select('group', array('Vendor' => 'Vendor', 'Client' => 'Client', 'Admin' => 'Admin'),$step->group) }}
            </td>
            <td>
                    {{ Form::label('window') }}
                    {{ Form::text('window['.$step->id.']',$step->window) }}
            </td>
            <div><td>
                {{ Form::label('') }}
                {{ Form::text('RoutineOrigDueDate['.$step->id.']',$step->RoutineOrigDueDate) }}
            </div>
            </td>
            <div><td>
                {{ Form::label('') }}
                {{ Form::text('RoutineNewDueDate['.$step->id.']',$step->RoutineNewDueDate) }}
            </div>
            </td>
            <div><td>
                {{ Form::label('') }}
                {{ Form::text('RushOrigDueDate['.$step->id.']',$step->RushOrigDueDate) }}
            </div>
            </td>
            <div><td>
                {{ Form::label('') }}
                {{ Form::text('RushNewDueDate['.$step->id.']',$step->RushNewDueDate) }}
            </div>
            </td>
            <div><td>
                {{ Form::label('') }}
                {{ Form::text('SameDayOrigDueDate['.$step->id.']',$step->Same_DayOrigDueDate) }}
            </div>
            </td>
            <div><td>
                {{ Form::label('') }}
                {{ Form::text('SameDayNewDueDate['.$step->id.']',$step->Same_DayNewDueDate) }}
            </div>
            </td>
        @endforeach
    </tr>
</table>
</div>
{{ Form::hidden('process', $process->id) }}
	<div>{{ Form::submit('Save Steps') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
