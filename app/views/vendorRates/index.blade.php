<html>
<head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $('#state').change(function(){
                $.get("{{ url('api/getcounties')}}", { option: $('#state').val() },
                        function(data) {
                            var numbers = $('#county');
                            numbers.empty();
                            $.each(data, function(key, value) {
                                numbers .append($("<option></option>")
                                        .attr("value",key)
                                        .text(value));
                            });
                        });
            });
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
<h1>Modify Rates for Service</h1>
<table>
    <tr>
        <th>State</th>
        <th>County</th>
        <th>Filing</th>
        <th>Process Service</th>
        <th>Recording</th>
        <th>Delete County</th>
    </tr>

    <tr>
    {{ Form::open(['route' => 'vendorrates.store']) }}

        @foreach($rates as $rate)
    <tr>
        <div><td>
                {{ $rate->state }}
        </div>
        </td>
        <div><td>
                {{ $rate->county }}

        </div>
        </td>
        <div><td>
                <br>{{ Form::label('Flat Rate:') }}
                {{ Form::text('revFilingFlat['.$rate->id.']',$rate->filingFlat) }}</br>
                <br>{{ Form::label('OR Base Rate:') }}
                    {{ Form::text('revFilingBase['.$rate->id.']',$rate->filingBase) }}</br>
                <br>{{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('revFilingMileage['.$rate->id.']',$rate->filingMileage) }}</br>
        </div>
        </td>
        <div><td>
                <br>{{ Form::label('Flat Rate:') }}
                    {{ Form::text('revServiceFlat['.$rate->id.']',$rate->serviceFlat) }}</br>
                <br>{{ Form::label('OR Base Rate:') }}
                    {{ Form::text('revServiceBase['.$rate->id.']',$rate->serviceBase) }}</br>
                <br>{{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('revServiceMileage['.$rate->id.']',$rate->serviceMileage) }}</br>
        </div>
        </td>
        <div><td>
                <br>{{ Form::label('Flat Rate:') }}
                    {{ Form::text('revRecordingFlat['.$rate->id.']',$rate->recordingFlat) }}</br>
                <br>{{ Form::label('OR Base Rate:') }}
                    {{ Form::text('revRecordingBase['.$rate->id.']',$rate->recordingBase) }}</br>
                <br>{{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('revRecordingMileage['.$rate->id.']',$rate->recordingMileage) }}</br>
        </div>
        </td>
        <div><td>{{ link_to("/vendorRates/destroy/{$rate->id}", 'Delete County') }}</td></div>
    </tr>
        {{Form::hidden('rateId['.$rate->id.']', $rate->id)}}
    @endforeach
    <tr>
    <td>
    <div>
        {{ Form::label('') }}
        {{ Form::select('state', $states, null, ['id' => 'state']) }}
        {{ $errors->first('state') }}
    </div>
    </td>
    <td>
    <div class="row">
        <div class="large-9 columns">
            <label for="county"></label>
            <select id="county" name="county">
            </select>
            {{ $errors->first('county') }}
        </div>
        </div>
     </td>
        <div><td>
                <br>{{ Form::label('Flat Rate:') }}
                    {{ Form::text('filingFlat') }}</br>
                <br>{{ Form::label('OR Base Rate:') }}
                    {{ Form::text('filingBase') }}</br>
                <br>{{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('filingMileage') }}</br>
        </div>
        </td>
        <div><td>
                <br>{{ Form::label('Flat Rate:') }}
                    {{ Form::text('serviceFlat') }}</br>
                <br>{{ Form::label('OR Base Rate:') }}
                    {{ Form::text('serviceBase') }}</br>
                <br>{{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('serviceMileage') }}</br>
        </div>
        </td>
        <div><td>
                <br>{{ Form::label('Flat Rate:') }}
                    {{ Form::text('recordingFlat') }}</br>
                <br>{{ Form::label('OR Base Rate:') }}
                    {{ Form::text('recordingBase') }}</br>
                <br>{{ Form::label('Plus Mileage Rate:') }}
                    {{ Form::text('recordingMileage') }}</br>
        </div>
        </td>
</tr>
</table>
	<div>{{ Form::submit('Add County') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>
