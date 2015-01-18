<!DOCTYPE html>
<html>

<head>
<style>
table {
    width:50%;
}
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;
}
table#t01 tr:nth-child(even) {
    background-color: #eee;
}
table#t01 tr:nth-child(odd) {
   background-color:#fff;
}
table#t01 th	{
    background-color: black;
    color: white;
}
</style>
</head>

<body>

<h1>Reproject Task</h1>

{{ Form::open(['route' => 'reprojections.store']) }}
	<div>
	{{ Form::label('reprojected', 'New Deadline: ') }}
	{{ Form::input('date', 'reprojected') }}
	{{ $errors->first('reprojected') }}
	</div>
				<div>
	{{ Form::label('description', 'Delay Reason: ') }}
	{{ Form::textarea('description') }}
	{{ $errors->first('description') }}
	</div>
{{ Form::hidden('task_id', $task_id) }}

	<div>{{ Form::submit('Reproject Task') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}


@if(!empty($reprojections->reprojection))
<h2> Past Reprojections: </h2><p>
@foreach ($reprojections as $reprojection)
<table>
  <tr>
    <th><b>Date: {{ $reprojection->reprojection }}</b></th>
  </tr>
<tr>
<td>{{ $reprojection->description }}</td>
</tr>
</table>
@endforeach
@endif
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>
