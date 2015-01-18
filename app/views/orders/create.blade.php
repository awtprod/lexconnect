<html>
<head>
<script type="text/javascript">
    $(document).ready(function() {
        $("#state").change(function() {
            $.getJSON("../orders/courts/" + $("#state").val(), function(data) {
                var $courts = $("#courts");
                $courts.empty();
                $.each(data, function(index, value) {
                    $courts.append('<option value="' + index +'">' + value + '</option>');
                });
            $("#courts").trigger("change"); /* trigger next drop down list not in the example */
            });
        });
    });
</script>
</head>
<body>
<h1>Create New Order</h1>


{{ Form::open(['route' => 'orders.store']) }}
	<div>
	{{ Form::label('plaintiff', 'Plaintiff: ') }}
	{{ Form::text('plaintiff') }}
	{{ $errors->first('plaintiff') }}
	</div>
		<div>
	{{ Form::label('defendant', 'Defendant: ') }}
	{{ Form::text('defendant') }}
	{{ $errors->first('defendant') }}
	</div>
		<div>
	{{ Form::label('case', 'Court Case: ') }}
	{{ Form::text('case') }}
	{{ $errors->first('case') }}
	</div>
			<div>
	{{ Form::label('reference', 'Reference: ') }}
	{{ Form::text('reference') }}
	{{ $errors->first('reference') }}
	</div>

	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, null, ['id' => 'state']) }}
	{{ $errors->first('state') }}
	</div>
	{{ Form::label('court', 'Court: ') }}
	{{ Form::select('court', $courts) }}
	{{ $errors->first('court') }}
    	<div>
	{{ Form::label('services', 'Services: ') }}
	{{ Form::checkbox('filing', 'yes') }} 
	{{ Form::label('filing', 'Filing ') }}
	{{ Form::checkbox('recording', 'yes') }}
	{{ Form::label('recording', 'Recording ') }}
	{{ Form::checkbox('service', 'yes') }} 
	{{ Form::label('service', 'Service ') }}
	{{ $errors->first('services') }}
	</div>
@if (Auth::user()->user_role=='Admin')
    	{{ Form::label('company', 'Client: ') }}
	{{ Form::select('company', $company) }}
	{{ $errors->first('company') }}
@else
	{{ Form::hidden('company', $company) }}
@endif

	<div>{{ Form::submit('Create Order') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>

</body>
</html>
