@extends('layouts.default')

@section('content')
<h1>Create Step</h1>
<table>
    <tr>
        <th>Step Name</th>
        <th>Judicial/Non-Judicial</th>
        <th>Sort Order</th>
        <th>Starting Status <br>(Hold or Active)</th>
        <th>Assign User Group</th>
        <th>PopUp Window<br>(Leave Blank For No Popup)</th>
        <th>Routine - Original Due Date<br>(Days from start of Process)</br></th>
        <th>Routine - New Due Date<br>(Days for each reprojection)</th>
        <th>Rush - Original Due Date<br>(Days from start of Process)</br></th>
        <th>Rush - New Due Date<br>(Days for each reprojection)</th>
        <th>Same Day - Original Due Date<br>(Days from start of Process)</br></th>
        <th>Same Day - New Due Date<br>(Days for each reprojection)</th>
        <th>Delete Step</th>
    </tr>

    <tr>



{{ Form::open(['route' => 'steps.store']) }}

        @foreach($steps as $step)
    <tr>
        <div><td>
                {{ Form::label('') }}
                {{ Form::text('revName['.$step->id.']',$step->name) }}
        </div>
        </td>
        <div><td>
                {{ Form::label('') }}
                {{ Form::text('revJud['.$step->id.']',$step->judicial) }}
        </div>
        </td>
        <div><td>
                {{ Form::label('') }}
                {{ Form::text('revSortOrder['.$step->id.']',$step->sortOrder) }}
        </div>
        </td>
        <div><td>
                {{ Form::label('') }}
                {{ Form::text('revStatus['.$step->id.']',$step->status) }}
        </div>
        </td>
        <div><td>
                {{ Form::label('', '') }}
                {{ Form::select('revGroup['.$step->id.']', array('Vendor' => 'Vendor', 'Client' => 'Client', 'Admin' => 'Admin'),$step->group) }}
        </div>
        </td>
        <div><td>
                {{ Form::label('') }}
                {{ Form::text('revWindow['.$step->id.']',$step->window) }}
        </div>
        </td>
        <div><td>
            {{ Form::label('') }}
            {{ Form::text('revRoutineOrigDueDate['.$step->id.']',$step->RoutineOrigDueDate) }}
        </div>
        </td>
        <div><td>
            {{ Form::label('') }}
            {{ Form::text('revRoutineNewDueDate['.$step->id.']',$step->RoutineNewDueDate) }}
        </div>
        </td>
        <div><td>
            {{ Form::label('') }}
            {{ Form::text('revRushOrigDueDate['.$step->id.']',$step->RushOrigDueDate) }}
        </div>
        </td>
        <div><td>
            {{ Form::label('') }}
            {{ Form::text('revRushNewDueDate['.$step->id.']',$step->RushNewDueDate) }}
        </div>
        </td>
        <div><td>
            {{ Form::label('') }}
            {{ Form::text('revSameDayOrigDueDate['.$step->id.']',$step->Same_DayOrigDueDate) }}
        </div>
        </td>
        <div><td>
            {{ Form::label('') }}
            {{ Form::text('revSameDayNewDueDate['.$step->id.']',$step->Same_DayNewDueDate) }}
        </div>
        </td>
        <div><td>{{ link_to("/steps/destroy/{$step->id}", 'Delete Step') }}</td></div>
    </tr>
    {{Form::hidden('stepId['.$step->id.']', $step->id)}}

    @endforeach

        <div><td>
                {{ Form::label('') }}
                {{ Form::text('name') }}
        </div>
        </td>
         <div><td>
            {{ Form::label('') }}
            {{ Form::text('jud') }}
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
	<div>{{ Form::submit('Add Step') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
