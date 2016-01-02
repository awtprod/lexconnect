@extends('layouts.default')

@section('content')
<h1>Job #{{ $jobs->id }}</h1><p>

    {{ link_to("/documents/view/?jobId={$jobs->id}&_token={$token}", 'View Documents') }}<br>

<table>
  <tr>
    <th>Task</th>
    <th>Deadline</th>
    <th>Completed</th>
    <th>Actions</th>
  </tr>

<tr>
<td>Accept Serve</td>
<td>{{ $jobtask[0]["deadline"] }}</td>
@if ($jobtask[0]["completed"] == NULL)
<td></td>
@else
<td>{{ $jobtask[0]["completed"] }}</td>
@endif
@if ($step->step == 0 AND $step->completion == NULL)
<td>{{ link_to("/tasks/complete/?id={$jobtask[0]["taskId"]}&accept=true&_token={$jobtask[0]["token"]}", 'Accept') }}/{{ link_to("/tasks/complete/?id={$jobtask[0]["taskId"]}&_token={$jobtask[0]["token"]}", 'Deny') }}</td>
@else
<td></td>
@endif
</tr>

    <tr>
    <td>Attempt Serve</td>
    <td>{{ $jobtask[1]["deadline"] }}</td>

    @if ($jobtask[1]["completed"] == NULL)
        <td></td>
    @else
        <td>{{ $jobtask[1]["completed"] }}</td>
    @endif


    @if($jobtask[1]["taskStatus"] == 0)
     <td>Waiting for Documents</td>
     @elseif($jobtask[1]["jobStatus"] == 1)
     <td>Job on Hold</td>

            @elseif ($step->step == 1 AND $step->completion == NULL)
        <td>{{ link_to("/tasks/complete/?id={$jobtask[1]["taskId"]}&_token={$jobtask[1]["token"]}", 'Enter Attempt') }}/{{ link_to("/tasks/complete/?id={$jobtask[1]["taskId"]}&served=true&_token={$jobtask[1]["token"]}", 'Defendant Served') }}</td>
    @else
        <td></td>

    @endif
    </tr>

    <tr>
    <td>Upload Proof</td>
    <td>{{ $jobtask[2]["deadline"] }}</td>
    @if ($jobtask[2]["completed"] == NULL)
        <td></td>
    @else
        <td>{{ $jobtask[2]["completed"] }}</td>
    @endif
    @if ($step->step == 2 AND $step->completion == NULL)
        <td>{{ link_to("/tasks/complete/?id={$jobtask[2]["taskId"]}&_token={$jobtask[2]["token"]}", 'Generate Proof') }}</td>
    @else
        <td></td>
    @endif
    </tr>

    <tr>
    <td>File Proof</td>
    <td>{{ $jobtask[3]["deadline"] }}</td>
    @if ($jobtask[3]["completed"] == NULL)
        <td></td>
    @else
        <td>{{ $jobtask[3]["completed"] }}</td>
    @endif
    @if ($step->step == 3 AND $step->completion == NULL)
        <td>{{ link_to("/tasks/complete/?id={$jobtask[3]["taskId"]}&_token={$jobtask[3]["token"]}", 'Send Proof')}}</td>
                @else
        <td></td>
    @endif
    </tr>

    @if(!empty($jobtask[4]["deadline"]))
    <tr>
        <td>Generate and Upload Declaration of Mailing</td>
        <td>{{ $jobtask[4]["deadline"] }}</td>
        @if ($jobtask[4]["completed"] == NULL)
            <td></td>
        @else
            <td>{{ $jobtask[4]["completed"] }}</td>
        @endif
        @if ($step->step == 4 AND $step->completion == NULL)
            <td>{{ link_to("/tasks/complete/?id={$jobtask[4]["taskId"]}&_token={$jobtask[4]["token"]}", 'Generate Declaration of Mailing')}}</td>
        @else
            <td></td>
        @endif
        @endif
    </tr>
        @if(!empty($jobtask[5]["deadline"]))
            <tr>
                <td>File Declaration of Mailing</td>
                <td>{{ $jobtask[5]["deadline"] }}</td>
                @if ($jobtask[5]["completed"] == NULL)
                    <td></td>
                @else
                    <td>{{ $jobtask[5]["completed"] }}</td>
                @endif
                @if ($step->step == 5 AND $step->completion == NULL)
                    <td>{{ link_to("/tasks/complete/?id={$jobtask[5]["taskId"]}&_token={$jobtask[5]["token"]}", 'Send Declaration of Mailing')}}</td>
                        @else
                    <td></td>
                @endif
                @endif
            </tr>
</table>
@stop
