@extends('layouts.default')
@section('head')

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function() {
            $("#checkAll").change(function () {
                $("input:checkbox").prop('checked', $(this).prop("checked"));
            });
        });
    </script>
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

     {{ link_to("/documents/upload/{$orders->id}", 'Upload Documents') }}<br>
     {{ link_to("/documents/view/{$orders->id}", 'View Documents') }}<br>

  </div>

<div>

{{ Form::open(['route' => 'jobs.actions']) }}

    <table>
        <tr>
            <th>Servee</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>History</th>
            <th>Actions <label><input type="checkbox" id="checkAll"/> Select all</label></th>
        </tr>

@if(!empty($verify))

            <tr>
                <td>Verify Documents</td>
                <td>{{ $verify->process }}</td>
                <td>{{ date("m/d/y", strtotime($verify->deadline)) }} </td>
                <td></td>
                <td>{{ Form::checkbox('jobId[]', $verify->job_id) }}</td>
            </tr>

@endif

@if(!empty($recording))
            <tr>
                <td>{{ $recording->defendant }}</td>

                <td> {{ $recordingStatus }} </td>

                @if(!empty($recordingTasks))
                <td>{{ date("m/d/y", strtotime($recordingTasks->deadline)) }} </td>

                @else

                <td></td>

                @endif
                <td></td>
                <td>{{ Form::checkbox('jobId[]', $recording->id) }}</td>
            </tr>
@endif

@if(!empty($filing))

            <tr>

                <td>{{ $filing->defendant }}</td>

                <td> {{ $filingStatus }} </td>

                @if(!empty($filingTasks))
                    <td>{{ date("m/d/y", strtotime($filingTasks->deadline)) }} </td>

                @else

                    <td></td>

                @endif
                <td></td>
                <td>{{ Form::checkbox('jobId[]', $filing->id) }}</td>
            </tr>

@endif

@if(!empty($defendants))

@foreach($servees as $servee)


            <tr>

                <td>{{ $servee->defendant }}</td>

                <td> {{ $defendants[$servee->id]["status"] }} </td>

                @if(!empty($defendants[$servee->id]["due"]))
                    <td>{{ date("m/d/y", strtotime($defendants[$servee->id]["due"])) }} </td>

                @else

                    <td></td>

                @endif
                <td></td>
                <td>{{ Form::checkbox('jobId[]', $defendants[$servee->id]["jobId"]) }}</td>
            </tr>

@endforeach

@endif
    </table>
<br>

{{ Form::select('action', $actions) }}
{{ Form::submit('Submit') }}
{{ Form::close() }}

</div>
<a href="{{ URL::previous() }}">Go Back</a>
@stop