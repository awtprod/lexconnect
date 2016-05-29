<html>
<head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) {

        });

    </script>
    <meta charset="utf-8">
    <style>
        table {
            height:70%;
            width:70%;
            padding:0;
            margin:0;

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
<h1>Locations:</h1>

@if(Session::has('message'))
    <p class="alert alert-info">{{ Session::get('message') }}</p>
@endif

<table>
    <tr>
        <th>Name</th>
        <th>Street</th>
        <th>City</th>
        <th>State</th>
        <th>Zip Code</th>
        <th>Delete Location</th>
    </tr>

    {{ Form::open(['route' => 'locations.store']) }}

        @if(!empty($locations))

        @foreach($locations as $location)
    <tr class="table">
        <div><td>
                {{ Form::label('Location Name:') }}
                {{ Form::text('location['.$location->id.'][name]',$location->name) }}
                {{ $errors->first('name') }}
                <br>        </div>
        </td>
        <div><td>
                {{ Form::label('Street:') }}
                {{ Form::text('location['.$location->id.'][street]',$location->street) }}
                {{ $errors->first('street') }}
                <br>        </div>
        </td>
        <div><td>
                {{ Form::label('City:') }}
                {{ Form::text('location['.$location->id.'][city]',$location->city) }}
                {{ $errors->first('city') }}
                <br>        </div>
        </td>
        <td>
            <div>
                {{ Form::label('State') }}
                {{ Form::select('location['.$location->id.'][state]', $states, $location->state, ['id' => 'state']) }}
                {{ $errors->first('state') }}
            </div>
        </td>
        <div><td>
                {{ Form::label('Zip Code:') }}
                {{ Form::text('location['.$location->id.'][zipcode]',$location->zipcode) }}
                {{ $errors->first('zipcode') }}
                <br>        </div>
        </td>
        <div><td>{{ link_to("/locations/destroy/{$location->id}", 'Delete Location') }}</td></div>
    </tr>

    @endforeach

    @endif
    <tr class="table">
        <div><td>
                {{ Form::label('Location Name:') }}
                {{ Form::text('location[0][name]') }}
                {{ $errors->first('name') }}
                <br>        </div>
        </td>
        <div><td>
                {{ Form::label('Street:') }}
                {{ Form::text('location[0][street]') }}
                {{ $errors->first('street') }}
                <br>        </div>
        </td>
        <div><td>
                {{ Form::label('Suite/Unit #:') }}
                {{ Form::text('location[0][street2]') }}
                {{ $errors->first('street2') }}
                <br>        </div>
        </td>
        <div><td>
                {{ Form::label('City:') }}
                {{ Form::text('location[0][city]') }}
                {{ $errors->first('city') }}
                <br>        </div>
        </td>
        <td>
            <div>
                {{ Form::label('State') }}
                {{ Form::select('location[0][state]', $states, null, ['id' => 'state']) }}
                {{ $errors->first('state') }}
            </div>
        </td>
        <div><td>
                {{ Form::label('Zip Code:') }}
                {{ Form::text('location[0][zipcode]') }}
                {{ $errors->first('zipcode') }}
                <br>        </div>
        </td>
        <div><td></td></div>
    </tr>
</table>
	<div>{{ Form::submit('Save') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>
