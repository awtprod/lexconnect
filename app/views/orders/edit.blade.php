<html>
<head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $('#state').change(function(){
                $.get("{{ url('api/getcourts')}}", { option: $('#state').val() },
                        function(data) {
                            var numbers = $('#court');
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
</head>
<body>
<h1>Edit Order</h1>


{{ Form::open(['route' => 'orders.update']) }}
	<div>
	{{ Form::label('plaintiff', 'Plaintiff: ') }}
	{{ Form::text('plaintiff', $data->plaintiff) }}
	{{ $errors->first('plaintiff') }}
	</div>
		<div>
	{{ Form::label('defendant', 'Defendant: ') }}
	{{ Form::text('defendant', $data->defendant) }}
	{{ $errors->first('defendant') }}
	</div>
		<div>
	{{ Form::label('case', 'Court Case: ') }}
	{{ Form::text('case', $data->case) }}
	{{ $errors->first('case') }}
	</div>
			<div>
	{{ Form::label('reference', 'Reference: ') }}
	{{ Form::text('reference', $data->reference) }}
	{{ $errors->first('reference') }}
	</div>

	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, $data->state, ['id' => 'state']) }}
	{{ $errors->first('state') }}
	</div>
<div class="row">
    <div class="large-9 columns">
        <label for="court">Court:</label>
        <select id="court" name="court" selected>
        </select>
        <option selected>{{$data->court}}</option>
    </div>
</div>

@if(Auth::user()->user_role=='Admin')
    	{{ Form::label('company', 'Client: ') }}
	{{ Form::select('company', $clients, $data->company) }}
	{{ $errors->first('company') }}<p>
@endif

{{Form::hidden('orderId', $data->id)}}

	<div>{{ Form::submit('Edit Order') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>

</body>
</html>
