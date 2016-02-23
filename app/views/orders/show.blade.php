@extends('layouts.default')
@section('head')
@stop
@section('content')

<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>

{{ link_to('/orders/create', 'New Order') }}&nbsp;{{ link_to("/orders/edit/{$orders->id}", 'Edit Order') }}

@if (!empty($orders))
<h2>Order # {{ $orders->id }}</h2><p>


{{ $orders->court }}<p>
{{ $orders->plaintiff }}v.{{ $orders->defendant }}<p>
Case: {{ $orders->courtcase }}<p>
Reference: {{ $orders->reference }}<p>
@else
<h2>No Order to display!</h2>
@endif
<h2>{{ Form::open(['route' => 'jobs.create']) }}{{ Form::hidden('ordersId', $orders->id) }}{{ Form::submit('Add Defendants') }}{{ Form::close() }}</h2>
 <div>

     {{ link_to("/documents/upload/?orderId={$orders->id}&_token={$token}", 'Upload Documents') }}<br>
     {{ link_to("/documents/view/?orderId={$orders->id}&_token={$token}", 'View Documents') }}<br>

  </div>


@if(!empty($verify))

    <div>
        <h2> Documents Need To Be Uploaded: </h2><p>
        <table>
            <tr>
                <th>Task</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>

            <tr>
                <td>{{ $verify->process }}</td>
                <td>{{ date("m/d/y", strtotime($verify->deadline)) }} </td>
                <td></td>
            </tr>

        </table>
    </div>
    <br>

@endif

@if(!empty($recording))

    <div>
        <h2> Recording: </h2><p>
        <table>
            <tr>
                <th>Location</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>

            <tr>

                //Location
                <td>{{ $recording->defendant }}</td>

                //Status
                <td> {{ $recordingStatus }} </td>

                //Due Date
                @if(!empty($recordingTasks))
                <td>{{ date("m/d/y", strtotime($recordingTasks->deadline)) }} </td>

                @else

                <td></td>

                @endif

                //Actions
                <td>
                    {{ Form::open(['route' => 'jobs.actions']) }}
                    {{ Form::select('action', $recordingActions) }}
                    {{ Form::hidden('jobId', $recording->id) }}
                    {{ Form::submit('Submit') }}
                    {{ Form::close() }}
                </td>
            </tr>

        </table>
    </div>
    <br>

@endif

@if(!empty($filing))

    <div>
        <h2> Court Filing: </h2><p>
        <table>
            <tr>
                <th>Location</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>

            <tr>

                //Location
                <td>{{ $filing->defendant }}</td>

                //Status
                <td> {{ $filingStatus }} </td>

                //Due Date
                @if(!empty($filingTasks))
                    <td>{{ date("m/d/y", strtotime($filingTasks->deadline)) }} </td>

                @else

                    <td></td>

                @endif

                //Actions
                <td>
                    {{ Form::open(['route' => 'jobs.actions']) }}
                    {{ Form::select('server', $filingActions) }}
                    {{ Form::hidden('jobId', $filing->id) }}
                    {{ Form::submit('Submit') }}
                    {{ Form::close() }}
                </td>
            </tr>

        </table>
    </div>
    <br>

@endif

@if(!empty($defendants))

@foreach($servees as $servee)
    <div>
        <table>
            <tr>
                <th>Defendant</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>

            <tr>

                //Defendant
                <td>{{ $servee->defendant }}</td>

                //Status
                <td> {{ $defendants[$servee->id]["status"] }} </td>

                //Due Date
                @if(!empty($defendants[$servee->id]["due"]))
                    <td>{{ date("m/d/y", strtotime($defendants[$servee->id]["due"])) }} </td>

                @else

                    <td></td>

                @endif

                //Actions
                <td>
                    {{ Form::open(['route' => 'jobs.actions']) }}
                    {{ Form::select('action', $defendants[$servee->id]["actions"]) }}
                    {{ Form::hidden('jobId', $defendants[$servee->id]["jobId"]) }}
                    {{ Form::submit('Submit') }}
                    {{ Form::close() }}
                </td>
            </tr>

        </table>
    </div>
    <br>
@endforeach

@else
 <h2>No Defendants to display!</h2>

@endif

<a href="{{ URL::previous() }}">Go Back</a>
@stop