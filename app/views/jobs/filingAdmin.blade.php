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
<td>Accept Filing Job</td>
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
    <td>Filing Documents Uploaded</td>
    <td>{{ $jobtask[1]["deadline"] }}</td>

    @if ($jobtask[1]["completed"] == NULL)
        <td></td>
    @else
        <td>{{ $jobtask[1]["completed"] }}</td>
    @endif
        <td>Client</td>
    </tr>

    <tr>
        <td>Filing Documents Picked Up</td>
        <td>{{ $jobtask[2]["deadline"] }}</td>

        @if ($jobtask[2]["completed"] == NULL)
            <td></td>
        @else
            <td>{{ $jobtask[2]["completed"] }}</td>
        @endif
        @if ($step->step == 2 AND $step->completion == NULL)
            <td>{{ link_to("/tasks/complete/?id={$jobtask[2]["taskId"]}&_token={$jobtask[2]["token"]}", 'Documents Picked Up') }}</td>
        @else
            <td></td>
        @endif
    </tr>

    <tr>
        <td>QA Check Documents</td>
        <td>{{ $jobtask[3]["deadline"] }}</td>
        @if ($jobtask[3]["completed"] == NULL)
            <td></td>
        @else
            <td>{{ $jobtask[3]["completed"] }}</td>
        @endif
        @if ($step->step == 3 AND $step->completion == NULL)
            <td>{{ link_to("/tasks/complete/?id={$jobtask[3]["taskId"]}&_token={$jobtask[3]["token"]}", 'QA Documents')}}</td>
        @else
            <td></td>
        @endif
    </tr>

    @if(!empty($jobtask[4]["deadline"]))
    <tr>
    <td>QA Fail - Client Notified</td>
    <td>{{ $jobtask[4]["deadline"] }}</td>

    @if ($jobtask[4]["completed"] == NULL)
        <td></td>
    @else
        <td>{{ $jobtask[4]["completed"] }}</td>
    @endif
    <td>Client</td>
    </tr>
    @endif

    <tr>
    <td>Documents for Filing Received</td>
    <td>{{ $jobtask[5]["deadline"] }}</td>
    @if ($jobtask[5]["completed"] == NULL)
        <td></td>
    @else
        <td>{{ $jobtask[5]["completed"] }}</td>
    @endif
    @if ($step->step == 5 AND $step->completion == NULL)
        <td>{{ link_to("/tasks/complete/?id={$jobtask[5]["taskId"]}&_token={$jobtask[5]["token"]}",'Documents Received')}}</td>
    @else
        <td></td>
    @endif
    </tr>

    @if(!empty($jobtask[6]["deadline"]))
    <tr>
    <td>Documents Filed</td>
    <td>{{ $jobtask[6]["deadline"] }}</td>
    @if ($jobtask[6]["completed"] == NULL)
        <td></td>
    @else
        <td>{{ $jobtask[6]["completed"] }}</td>
    @endif
    @if ($step->step == 6 AND $step->completion == NULL)
            <td>{{ link_to("/tasks/complete/?id={$jobtask[6]["taskId"]}&_token={$jobtask[6]["token"]}",'Documents Filed')}}</td>
    @else
        <td></td>
    @endif
    </tr>
        @endif

    @if(!empty($jobtask[7]["deadline"]))
        <tr>
            <td>Documents Recorded</td>
            <td>{{ $jobtask[7]["deadline"] }}</td>
            @if ($jobtask[7]["completed"] == NULL)
                <td></td>
            @else
                <td>{{ $jobtask[7]["completed"] }}</td>
            @endif
            @if ($step->step == 7 AND $step->completion == NULL)
                <td>{{ link_to("/tasks/complete/?id={$jobtask[7]["taskId"]}&_token={$jobtask[7]["token"]}",'Documents Recorded')}}</td>
            @else
                <td></td>
            @endif
        </tr>
        @endif
</table>
@stop
