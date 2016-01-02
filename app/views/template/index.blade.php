@extends('layouts.default')

@section('content')

    <table>
        <tr>
            <th>Process Id</th>
            <th>Process Name</th>
            <th>Service Type</th>
            <th>State</th>
            <th>Priority Level</th>
            <th>Edit Steps</th>

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
                @if(empty($process->state))
                    <td>All</td>
                @else
                    <td>{{$process->state}}</td>
                @endif
                <td>{{$process->priority}}</td>
                <td>{{ link_to("/template/edit/{$process->id}", 'Edit Steps') }}</td>


                @endforeach
            </tr>
    </table>

<a href="{{ URL::previous() }}">Go Back</a>
@stop
