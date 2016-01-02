<html>
<head>

</head>
<body>
<h1>Create Process</h1>

{{ Form::open(['route' => 'processes.store']) }}
<table>
  <tr>

    <th>Process Name</th>

  </tr>

<tr>
       <td>
                {{ Form::label('name') }}
                {{ Form::text('name') }}
        </td>

    <div><td>
        {{ Form::label('service', 'Service Type: ') }}

            {{ Form::select('service', array(''=>'Select', 'Filing' => 'Filing', 'Posting' => 'Posting', 'Process Service' => 'Process Service', 'Recording' => 'Recording', 'Supplemental' => 'Supplemental')) }}
    </div>
    </td>

</tr>
</table>
	<div>{{ Form::submit('Create Process') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>