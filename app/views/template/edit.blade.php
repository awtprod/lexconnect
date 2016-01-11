@extends('layouts.default')

@section('content')
<h1>Edit Steps for Process:{{$process->name}}</h1>

{{ Form::open(['route' => 'template.update']) }}
<div id="table">
    <table>
        <tr>
            <th>Step Name</th>
            <th>Judicial/Non-Judicial</th>
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
            <th>Delete Step</th>
        </tr>

        <tr>
            @foreach($templates as $template)

                <td>
                    {{ Form::label('') }}
                    {{ Form::text('name['.$template->id.']',$template->name) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::select('jud['.$template->id.']', array('Both' => 'Both', 'Judicial' => 'Judicial', 'Non-Judicial' => 'Non-Judicial'),$template->judicial) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('sortOrder['.$template->id.']',$template->sort_order) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('status['.$template->id.']',$template->status) }}
                </td>
                <td>
                    {{ Form::label('', '') }}
                    {{ Form::select('group['.$template->id.']', array('Vendor' => 'Vendor', 'Client' => 'Client', 'Admin' => 'Admin'),$template->group) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('window['.$template->id.']',$template->window) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('RoutineOrigDueDate['.$template->id.']',$template->RoutineOrigDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('RoutineNewDueDate['.$template->id.']',$template->RoutineNewDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('RushOrigDueDate['.$template->id.']',$template->RushOrigDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('RushNewDueDate['.$template->id.']',$template->RushNewDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('SameDayOrigDueDate['.$template->id.']',$template->Same_DayOrigDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('SameDayNewDueDate['.$template->id.']',$template->Same_DayNewDueDate) }}
                </td>
                <div><td>{{ link_to("/template/destroy/{$template->id}", 'Delete Step') }}</td></div>
        </tr>

        @endforeach
    </table>
</div>
{{ Form::hidden('process', $process->id) }}
<div>{{ Form::submit('Save Changes') }}</div>
{{ Form::close() }}

<h1>Add Steps for Process:{{$process->name}}</h1>

{{ Form::open(['route' => 'template.store']) }}
<div id="table">
    <table>
        <tr>
            <th>Select Step</th>
            <th>Step Name</th>
            <th>Judicial/Non-Judicial</th>
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
                    {{ Form::label('') }}
                    {{ Form::checkbox('addStep['.$step->id.']', 'yes') }}
                </td>

                <td>
                    {{ Form::label('') }}
                    {{ Form::text('name['.$step->id.']',$step->name) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::select('jud['.$step->id.']', array('Both' => 'Both', 'Judicial' => 'Judicial', 'Non-Judicial' => 'Non-Judicial'),$step->judicial) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('sortOrder['.$step->id.']',$step->sort_order) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('status['.$step->id.']',$step->status) }}
                </td>
                <td>
                    {{ Form::label('', '') }}
                    {{ Form::select('group['.$step->id.']', array('Vendor' => 'Vendor', 'Client' => 'Client', 'Admin' => 'Admin'),$step->group) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('window['.$step->id.']',$step->window) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('RoutineOrigDueDate['.$step->id.']',$step->RoutineOrigDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('RoutineNewDueDate['.$step->id.']',$step->RoutineNewDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('RushOrigDueDate['.$step->id.']',$step->RushOrigDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('RushNewDueDate['.$step->id.']',$step->RushNewDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('SameDayOrigDueDate['.$step->id.']',$step->Same_DayOrigDueDate) }}
                </td>
                <td>
                    {{ Form::label('') }}
                    {{ Form::text('SameDayNewDueDate['.$step->id.']',$step->Same_DayNewDueDate) }}
                </td>
                {{Form::hidden('stepId['.$step->id.']', $step->id)}}
        </tr>

            @endforeach
    </table>
</div>
{{ Form::hidden('process', $process->id) }}
<div>{{ Form::submit('Save Changes') }}</div>
{{ Form::close() }}

<h1>Create New Step</h1>

{{ Form::open(['route' => 'template.add']) }}
<div id="table">

<table>
    <tr>
        <th>Step Name</th>
        <th>Judicial/Non-Judicial</th>
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
</div>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('name') }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::select('jud', array('Both' => 'Both', 'Judicial' => 'Judicial', 'Non-Judicial' => 'Non-Judicial')) }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('sortOrder') }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('status') }}
</div>
</td>
<div><td>
    {{ Form::label('', '') }}
    {{ Form::select('group', array('Vendor' => 'Vendor', 'Client' => 'Client', 'Admin' => 'Admin')) }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('window') }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('RoutineOrigDueDate') }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('RoutineNewDueDate') }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('RushOrigDueDate') }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('RushNewDueDate') }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('SameDayOrigDueDate') }}
</div>
</td>
<div><td>
    {{ Form::label('') }}
    {{ Form::text('SameDayNewDueDate') }}
</div>
</td>
</tr>
</table>

{{ Form::hidden('process', $process->id) }}

<div>{{ Form::submit('Create Step') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}

<a href="{{ URL::previous() }}">Go Back</a>
@stop
