@extends('layouts.default')

@section('content')

@if(!empty($steps))

    <h3>{{ link_to("/steps/create/", 'Add/Edit a Step') }}</h3>

    <table>
        <tr>
            <th>Step Name</th>
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
        </tr>

            @foreach($steps as $step)
                <tr>
                    <div><td>
                        {{ $step->name }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->sort_order }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->status }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->group }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->window }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->RoutineOrigDueDate }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->RoutineNewDueDate }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->RushOrigDueDate }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->RushNewDueDate }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->Same_DayOrigDueDate }}
                    </div>
                    </td>
                    <div><td>
                        {{ $step->Same_DayNewDueDate }}
                    </div>
                    </td>
                </tr>
    @endforeach
    </table>
@else
    <h3>{{ link_to("/steps/create/", 'Add/Edit a Step') }}</h3>

@endif

<a href="{{ URL::previous() }}">Go Back</a>
@stop
